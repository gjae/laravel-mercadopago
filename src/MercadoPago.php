<?php
namespace Gjae\MercadoPago;


use MercadoPago\SDK;
use MercadoPago\Preference;

use Gjae\MercadoPago\Exceptions\ItemInvalidException;
use Gjae\MercadoPago\Exceptions\NoConfigException;
use Gjae\MercadoPago\Exceptions\InitializeException;
use Gjae\MercadoPago\Exceptions\NoHasTransactionObjectException;

use Gjae\MercadoPago\Contracts\HasTransaction;
use Gjae\MercadoPago\Contracts\IItem;

use Gjae\MercadoPago\Classes\MercadoPagoCredentials;
use Gjae\MercadoPago\Classes\MercadoPagoItems;
use Gjae\MercadoPago\Classes\Item;

use Gjae\MercadoPago\Models\MPTransaction;
use Gjae\MercadoPago\Models\ModelTransaction;

use DB;
use Closure;
class MercadoPago 
{

    private $config = array();


    private $credentials = null;

    private $items = null;


    private $preference = null;

    private $transaction = null;

    public function __construct(array $config, array $credentials)
    {
        $this->config = $config;
        $this->items = new MercadoPagoItems();
        $this->credentials = new MercadoPagoCredentials($credentials);

        if( !$this->config['local_debug'] ){
            SDK::setAccessToken( $this->getAccessToken() );
            $this->preference = new Preference();
            $this->setPreferences( $this->getConfig() );
        }

    }

    public function __call($method,$args)
    {
        return $this->config[$method];
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Inicia las preferencias desde la configuracion
     *
     * @param array $preference
     * @return void
     */
    public function setPreferences(array $preference)
    {
        $this->preference->back_urls    = $preference['back_urls'];
        $this->preference->auto_return  = $preference['auto_return'];
    }

    public function backUrlAddQS(array $qs = [])
    {
        $preferences = [];
        if( count( $qs ) )
        {
            $qs_str = http_build_query($qs);

            $preferences['back_urls'] = $this->getConfig()['back_urls'];
            foreach($preferences['back_urls'] as $key => $url) {
                $preferences['back_urls'][$key] = $url.'?'.$qs_str;
            }

            $preferences['auto_return'] =  $this->getConfig()['auto_return'];

            $this->setPreferences($preferences);
        }
    }


    public function getAccessToken() : string
    {
        return $this->credentials->getToken();
    }

    /**
     * En caso de que el usuario desee agregar preferencias manualmente fuera del archivo de configuracion
     *
     * @param array $credentials
     * @return void
     */
    public function setCredentials(array $credentials)
    {
        if( !array_key_exists('access_token', $credentials) ) $credentials['access_token'] = $this->credentials->getToken();
        if( !array_key_exists('public_key', $credentials) ) $credentials['public_key'] = $this->credentials->getPublicKey();

        $this->credentials->set( $credentials );
    }

    /**
     * Agrega un nuevo item a la coleccion de items de la transacción
     *
     * @param  Gjae\MercadoPago\Contracts\IITem $item
     * @return void
     */
    public function addItem($item)
    {
        if( is_object( $item ) &&  !( $item instanceof IItem ) ) throw new ItemInvalidException('Object need implement Gjae\\MercadoPago\\Contracts\\IItem interface');
        if( is_array( $item ) ) $item = new Item($item);
        $this->items->add($item);
    }

    /**
     * Retorna el objeto con la colleccion de items
     *
     * @return void
     */
    public function items()
    {
        return $this->items;
    }


    /**
     * Inicua la transacción con mercadopago, implementa una transacción encaso de que 
     * genere una excepcion, la transacción no sea guardada en la base de datos
     * 
     * @param Closure $closure
     * @param array|Gjae\MercadoPago\Contracts\HasTransactions $withModels
     * @param string $external_ref
     * @return void
     */
    public function begin(Closure $closure, $withModels = null, string $external_ref = 'S/RE')
    {
        DB::beginTransaction();
        try{
            $closure( $this );
            $this->preference->items = $this->items()->asArray();
            $this->preference->save();
            $this->saveTransaction($external_ref);
            $this->store($withModels);
            DB::commit();
        }catch(throwNoHasTransactionModel $e){
            DB::rollback();
            return $e;
        }
        catch(\Exception $e)
        {
            DB::rollback();

            return dd( $e->getMessage() );
            return $e;
        }
    }

    /**
     * Guarda la nueva transacción en la base de datos de la aplicación
     *
     * @param string $external_ref
     * @return void
     */
    public function saveTransaction($external_ref)
    {
        $this->transaction = new MPTransaction();
        $this->transaction->external_reference = $external_ref;
        $this->transaction->preference_id = $this->id();
        $this->transaction->amount = $this->items()->getAllItems()->reduce(function($carry, $item){
            return $carry + $item->unit_price;
        });
        $this->transaction->save();
    }


    /**
     * Almacena los modelos relacionados con la transacción en MercadoPago
     * Los objetos $withModels deben implementar el contrato Gjae\MercadoPago\Contracts\HasTransaction y definir el metodo transactions 
     * (Nota: en el trait Gjae\MercadoPago\Traits\HasTransactions ya hay una implementación de este metodo)
     * 
     * @param array|Gjae\MercadoPago\Contracts\HasTransaction $withModels
     * @return void
     */
    private function store($withModels = null)
    {
        if( !is_null( $withModels ) )
        {
            if( is_array($withModels) )
            {
                foreach( $withModels as $key => $model ) 
                    if( $model instanceof HasTransaction )
                        $this->hasModel( $model );
                    else $this->throwNoHasTransactionModel();
            }
            else if( is_object( $withModels ) && $withModels instanceof HasTransaction )
                $this->hasModel( $withModels );
            else if( is_object( $withModels ) && !( $withModels instanceof HasTransaction ) ) $this->throwNoHasTransactionModel();
        }
    }

    /**
     * Almacena la relación polimorfica entre el model MPTransaction y los objetos con el contrato HasTransaction
     *
     * @param HasTransaction $model
     * @return boolean
     */
    private function hasModel( HasTransaction $model )
    {
        $model->transactions()->save(
            new ModelTransaction([
                'mercadopago_transaction_id'    => $this->transaction->id
            ])
        );
    }
    
    public function throwNoHasTransactionModel()
    {
        throw new NoHasTransactionObjectException('  Model not implement HasTransaction Contract ');
    }

    public function initPoint()
    {
        return $this->preference->init_point;
    }

    public function getPreferences()
    {
        return $this->preference;
    }

    public function id()
    {
        return $this->preference->id;
    }
}
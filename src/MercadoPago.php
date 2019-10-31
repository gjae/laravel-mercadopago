<?php
namespace Gjae\MercadoPago;


use MercadoPago\SDK;
use MercadoPago\Preference;
use Gjae\MercadoPago\Classes\MercadoPagoCredentials;

use Gjae\MercadoPago\Exceptions\ItemInvalidException;
use Gjae\MercadoPago\Exceptions\NoConfigException;
use Gjae\MercadoPago\Exceptions\InitializeException;

use Gjae\MercadoPago\Classes\MercadoPagoItems;
use Gjae\MercadoPago\Contracts\IItem;
use Gjae\MercadoPago\Classes\Item;
use Closure;
class MercadoPago 
{

    private $config = array();


    private $credentials = null;

    private $items = null;


    private $preference = null;

    public function __construct(array $config, array $credentials)
    {
        $this->config = $config;

        $this->credentials = new MercadoPagoCredentials($credentials);

        $this->items = new MercadoPagoItems();

        SDK::setAccessToken( $this->getAccessToken() );

        $this->preference = new Preference();

    }

    public function setPreferences(array $preference)
    {
        $this->preference->url_backs = $preference['url_backs'];
    }


    public function getAccessToken() : string
    {
        return $this->credentials->getToken();
    }

    public function setCredentials(array $credentials)
    {
        if( !array_key_exists('access_token', $credentials) ) $credentials['access_token'] = $this->credentials->getToken();
        if( !array_key_exists('public_key', $credentials) ) $credentials['public_key'] = $this->credentials->getPublicKey();

        $this->credentials->set( $credentials );
    }

    public function addItem($item)
    {
        if( is_object( $item ) &&  !( $item instanceof IItem ) ) throw new ItemInvalidException('Object need implement Gjae\\MercadoPago\\Contracts\\IItem interface');
        if( is_array( $item ) ) $item = new Item($item);
        $this->items->add($item);
    }

    public function items()
    {
        return $this->items;
    }


    public function begin(Closure $closure)
    {
        $closure( $this );
        $this->preference->items = $this->items()->asArray();
    }

}
<?php
namespace Gjae\MercadoPago\Classes;

use Gjae\MercadoPago\Contracts\MPResponse;
use Gjae\MercadoPago\Contracts\HasTransaction;
use Gjae\MercadoPago\Models\MPTransaction;
use Gjae\MercadoPago\Models\ModelTransaction;

use Gjae\MercadoPago\Exceptions\NoHasTransactionObjectException;
use Illuminate\Http\Request;
class MercadoPagoResponse implements MPResponse 
{

    private $response       = null;

    private $transaction    = null;

    public function __construct(Request $request)
    {
        $this->response = $request->all();
        foreach($request->all() as $key => $value) $this->$key = $value;

        $this->updateTransaction();
    }


    public function getTransaction()
    {
        return $this->transaction = is_null( $this->transaction ) 
        ? MPTransaction::wherePreferenceId( $this->getPreferenceId() )->firstOrFail() 
        : $this->transaction;
    }

    public function updateTransaction()
    {
        $this->getTransaction()->fill( $this->response );
        $this->getTransaction()->save();
    }


    /**
     * Retorno del collection_id de la respuesta de mercadopago
     *
     * @return string
     */
    public function getCollectionId()
    {
        return $this->collection_id;
    }

    /**
     * Reporto del collection_status
     *
     * @return string
     */
    public function getCollectionStatus()
    {
        return $this->collection_status;
    }

    /**
     * Retorno del external_reference de la respuesta de mercadopago
     *
     * @return string
     */
    public function getExternalReference()
    {
        return $this->external_reference;
    }

    /**
     * Retorna el payment_type
     *
     * @return string
     */
    public function getPaymentType()
    {
        return $this->payment_type;
    }

    /**
     * Retorno del merchant_order_id de la respuesta de mercadopago
     *
     * @return string
     */
    public function getMerchantOrderId()
    {
        return $this->merchant_order_id;
    }


    /**
     * Retorno del preference_id de la respuesta del mercadopago
     *
     * @return string
     */
    public function getPreferenceId()
    {
        return $this->preference_id;
    }

    /**
     * Retorno del site_id
     *
     * @return string
     */
    public function getSiteId()
    {   
        return $this->site_id;
    }

    /**
     * Retorno del processing_mode de mercadopago
     *
     * @return string
     */
    public function getProcessingMode()
    {
        return $this->processing_mode;
    }

    /**
     * Retorno del merchant_account_id
     *
     * @return string
     */
    public function getMerchantAccountId()
    {
        return $this->merchant_account_id;
    }

}
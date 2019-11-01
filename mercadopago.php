<?php

return [

    /**
     * Si es verdadero entonces no se validan las credenciales
     * en mercadopago
     */
    'local_debug'        => true,

    /**
     * Especifica el modo en el que se estara usando
     * el mercado pago, : sandbox, production
     */
    'mode'              => 'sandbox',

    /**
     * Credenciales para el caso en que la aplicacion este en modo 
     * produccion (especificado en la clave mode)
     */
    'production'        => [

        'access_token'  => env('MP_ACCESS_TOKEN', ''),


        'public_key'    => env('MP_PUBLIC_KEY', ''),


    ],

    /**
     * Credenciales para el modo sandbox
     * especificado el uso en la clave "mode"
     */
    'sandbox'           => [
        'access_token'  => env('MP_SANDBOX_ACCESS_TOKEN', ''),

        'public_key'    => env('MP_SANDBOX_PUBLIC_KEY', '')
    ],


    /**
     * Tipo de identificacion usada para los pagos 
     */
    'identification_type'   => 'DNI',


    /**
     * Codigo de area telefonico
     */
    'area_code'             => '',

    /**
     * Especifica las URL de retorno apra el smartcheckout
     */
    'back_urls'             => [

        'success'   => '',

        'failure'   => '',

        'pending'   => '',

    ],

    'auto_return'   => 'approved',


];
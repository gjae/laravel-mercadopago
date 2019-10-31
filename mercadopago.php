<?php

return [

    'credentials'       => [

        'access_token'  => env('MP_ACCESS_TOKEN', ''),


        'public_key'    => env('MP_PUBLIC_KEY', ''),


    ],

    'identification_type'   => 'DNI',

    'area_code'             => '',

    'back_urls'             => [

        'success'   => '',

        'failure'   => '',

        'pending'   => '',

    ],

    'auto_return'   => 'approved',


];
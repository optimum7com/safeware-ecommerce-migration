<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bigcommerce Api
    |--------------------------------------------------------------------------
    |
    | This file is for setting the credentials for bigcommerce api key and secret.
    |
    */
    'default' => env("BC_CONNECTION", 'oAuth'),


    'basicAuth' => [
        'store_url' => env("BC_STORE_URL", null),
        'username'  => env("BC_USERNAME", null),
        'api_key'   => env("BC_API_KEY", null)
    ],

    'oAuth' => [
        'client_id'     => '43p57yc8nr285dymjn2exejibxn6ujw',//env("BC_CLIENT_ID", null),
        'client_secret' => 'jp75zao9n4oilohbtoubxb8k2y5i4kz',//env("BC_CLIENT_SECRET", null),
        'redirect_url'  => 'https://api.bigcommerce.com/stores/8zcngt4nvy/v3/'//env("BC_REDIRECT_URL", null)
    ],

];
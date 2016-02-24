<?php return array (
    'time_to_expire_new_token' => 10,
    'new_token_enabled' => false,
    'realm' => 'panel',
    'new_token_path' => '/get_anfix_token',
    'oauth_consumer_key' => '',
    'oauth_signature' => '',
    'applicationIdUrl' => array(
        '1' => 'https://apps.anfix.com/os/os/parc/',
        'E' => 'https://apps.anfix.com/facturapro-servicios/gestiona/servicios/'
    ),
    'requestToken' => 'https://apps.anfix.com/os/os/parc/partner/request_token',
    'accessToken' => 'https://apps.anfix.com/os/os/parc/partner/access_token',
    'loginUrl' => 'https://anfix.com/login-partner',
    /*
     * Si puede leer este comentario desde config/ es porque todavía no ha generado un token válido, debe establecer
     * new_token_enabled a true y visitar la url indicada en new_token_path para generar un token de conexión con anfix
     */
    'api_token' => '',
    'api_secret' => ''
);
<?php

namespace Lucid\Anfix;

use Illuminate\Routing\Controller;

class AnfixController extends Controller{

    private $oauth_consumer_key;
    private $oauth_signature;
    private $requestToken;
    private $accessToken;
    private $loginUrl;

    public function __construct(){
        $this->oauth_consumer_key = \Config::get('anfix.oauth_consumer_key');
        $this->oauth_signature = \Config::get('anfix.oauth_signature');
        $this->requestToken = \Config::get('anfix.requestToken');
        $this->accessToken = \Config::get('anfix.accessToken');
        $this->loginUrl = \Config::get('anfix.loginUrl');
    }

    /**
     * Inicio de la generación de un token anfix
     */
    public function generate(){

        if(\Request::has('oauth_verifier'))
            return $this->finishGeneration();

        $oauth_callback = urlencode(\Request::getUri());
        $realm = \Config::get('anfix.realm');

        $response = Anfix::getHeaders($this->requestToken,
            ["Authorization: realm=\"{$realm}\",
            oauth_consumer_key=\"{$this->oauth_consumer_key}\",
            oauth_signature_method=\"PLAINTEXT\",
            oauth_callback=\"{$oauth_callback}\",
            oauth_signature=\"{$this->oauth_signature}&\""]
        );

        //Almacenamos la clave temporale en la cache durante 10 minutos
        \Cache::add("anfix.oauth_token_{$response['oauth_token']}",$response['oauth_token_secret'],\Config::get('anfix.time_to_expire_new_token',10));

        //Enviamos al usuario a la página de anfix
        return \Response::redirectTo($this->loginUrl.'?oauth_token='.$response['oauth_token']);
    }

    /**
     * Realiza la segunda parte de la obtención del token
     */
    private function finishGeneration(){
        $token = \Input::get('oauth_token');
        $verifier = \Input::get('oauth_verifier');

        //Recuperamos la clave de la cache
        $secret = \Cache::get("anfix.oauth_token_{$token}");
        $realm = \Config::get('anfix.realm');

        $response = Anfix::getHeaders($this->accessToken,
            ["Authorization: realm=\"{$realm}\",
            oauth_consumer_key=\"{$this->oauth_consumer_key}\",
            oauth_signature_method=\"PLAINTEXT\",
            oauth_token=\"$token\",
            oauth_verifier=\"$verifier\",
            oauth_signature=\"{$this->oauth_signature}&$secret\""]
        );

        //En respuesta recibiremos el token y contraseña definitivos
        $content = \Config::get('anfix', []);
        $content['api_token'] = $response['oauth_token'];
        $content['api_secret'] = $response['oauth_token_secret'];

        //Escribimos el fichero de configuración
        \File::put(config_path().'/anfix.php','<?php return '.var_export($content,true).';');

        echo('Token generado correctamente');
        dd($response);

    }

}
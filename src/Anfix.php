<?php

namespace Lucid\Anfix;

use Lucid\Anfix\Exceptions\AnfixResponseException;

class Anfix {

    private static $curl;
    //Traducción de los errores más comunes al cristiano
    private static $errorsCodes = [
        'ERR000050000' => 'Normalmente lo que no es válido es companyId'
    ];

    /**
     * Devuelve las cabeceras de una petición
     * @param $url
     * @param array $headers
     * @return mixed
     * @throws \Exception
     */
    public static function getHeaders($url, array $headers){
        return self::send($url,$headers,[],true)['headers'];
    }

    /**
     * Devuelve el retorno de una petición
     * @param $url
     * @param $data Parámetros de la petición
     * @return mixed
     * @throws \Exception
     */
    public static function sendRequest($url, array $data){

        $oauth_consumer_key = \Config::get('anfix.oauth_consumer_key');
        $oauth_signature = \Config::get('anfix.oauth_signature');
        $api_token = \Config::get('anfix.api_token');
        $api_secret = \Config::get('anfix.api_secret');
        $realm = \Config::get('anfix.realm');

        $headers = ["Authorization: realm=\"{$realm}\",
            oauth_consumer_key=\"{$oauth_consumer_key}\",
            oauth_signature_method=\"PLAINTEXT\",
            oauth_token=\"$api_token\",
            oauth_signature=\"{$oauth_signature}&$api_secret\""];

        return self::send($url,$headers,json_encode($data),false)['response'];
    }

    /**
     * Hace una llamada con las cabeceras indicadas
     * @param string $url Url a acceder
     * @param array $headers array de arrays sin índices
     * @param array $data Datos a enviar
     * @return array(body,headers)
     */
    private static function send($url, array $headers, $data = '', $returnHeaders = false){

        if(empty(self::$curl))
            self::$curl = curl_init();

        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Host: anfix.com';

        curl_setopt(self::$curl, CURLOPT_HTTPGET, false); //Post query
        curl_setopt(self::$curl, CURLOPT_POST, true); //Post query
        curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt(self::$curl, CURLOPT_URL, $url); //Set complete url
        curl_setopt(self::$curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt(self::$curl, CURLOPT_HEADER, $returnHeaders);
        curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);  // Return transfer as string

        $curl_response = curl_exec(self::$curl);

        if(!$curl_response)
            throw new \Exception('La solicitud curl falló devolviendo el siguiente error: '.curl_error(self::$curl));

        $response_headers = [];

        if($returnHeaders) {
            list($rheader, $response_raw) = explode("\r\n\r\n", $curl_response, 2);

            foreach(explode("\r\n",$rheader) as $value){
                $v = explode(': ',$value,2);
                if(count($v) != 2)
                    continue;
                $response_headers[$v[0]] = $v[1];
            }

        }else
            $response_raw = $curl_response;

        $response = json_decode($response_raw);

        if($response == null) //A veces las cadenas vienen mal codificadas
            $response = json_decode(utf8_decode($response_raw));

        if($response == null)
            throw new AnfixResponseException('La respuesta no puede interpretarse como una cadena Json válida: '.utf8_decode($response_raw));

        if($response->result != 0 && !empty($response->errorList[0]->text)) {
            $err_message = $response->errorList[0]->text . (!empty(self::$errorsCodes[$response->errorList[0]->code]) ? ' ('.self::$errorsCodes[$response->errorList[0]->code].')' : '');
            throw new AnfixResponseException($response->errorList[0]->code . ': ' . $err_message);
        }

        if($response->result != 0)
            throw new AnfixResponseException("Se esperaba result = 1 en la llamada a $url con los datos:".print_r($data,true).' pero la respuesta fue:'.print_r($response,true));

        return ['response' => $response, 'headers' => $response_headers];
    }
}
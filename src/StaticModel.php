<?php

/*
* 2006-2015 Lucid Networks
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
*
* DISCLAIMER
*
*  Date: 9/2/16 18:21
*  @author Networkkings <info@lucidnetworks.es>
*  @copyright  2006-2015 Lucid Networks
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

namespace Lucid\Anfix;

use Lucid\Anfix\Exceptions\AnfixException;

/**
 * Class BaseModel
 * Esta clase es la clase base desde la que deben heredar todos los modelos de anfix
 * su comportamiento en muy similar al de Eloquent, salvo en la intrucción where que no admite llamadas recursivas
 * debe rellenar en cada modelo los datos obligatorios para poder establecer una conexión con anfix
 * @package Lucid\Anfix
 */
class StaticModel{

    protected static $applicationId; //Obligatorio, Identificador de la App Anfix, este identificador asocia la Url base por defecto conforme a config/anfix.php
    protected static $Model; //Opcional, Nombre de la entidad en Anfix, por defecto será el nombre de la clase
    protected static $primaryKey; //Opcional, Nombre de la clave primaria en Anfix, por defecto {$Model}Id
    protected static $apiBaseUrl; //Opcional, Url de la API a la que conectar, por defecto se obtiene de config/anfix en función del applicationId
    protected static $apiUrlSufix; //Opcional, Sufijo que se añade a la url de la API, por defecto nombre de la entidad, si se indica apiBaseUrl no se tendrá en cuenta este parámetro
    
    /**
     * Construye la configuración estática
     */
    protected static function constructStatic(){
    
        if(empty(self::$applicationId))
            throw new AnfixException('Debe indicar un applicationId en el modelo para poder utilizar la API');
            
        if(empty(self::$Model))
            self::$Model = end(explode('\\',get_called_class()));
            
        if(empty(self::$apiUrlSufix))
            self::$apiUrlSufix = strtolower(self::$Model).'/';

        if(empty(self::$apiBaseUrl))
            self::$apiBaseUrl = \Config::get('anfix.applicationIdUrl.'.self::$applicationId).self::$apiUrlSufix;

        if(empty(self::$primaryKey))
            self::$primaryKey = self::$Model.'Id';
    }
    
}
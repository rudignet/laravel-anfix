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
*  Date: 9/2/16 18:57
*  @author Networkkings <info@lucidnetworks.es>
*  @copyright  2006-2015 Lucid Networks
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

namespace Lucid\Anfix;

class NextNumber 
{
    private static $applicationId = 'E';
	private static $Model = 'NextNumber';
	private static $apiBaseUrl = 'http://apps.anfix.com/facturapro-servicios/gestiona/servicios/common/nextnumber/compute';

	/**
	 * Devuelve el siguiente n�mero disponible para un tipo de entidad
	 * @param string $entityTypeId Tipo de entidad: 1 Clientes, 2 Proveedores/Acreedores
	 * @param string $companyId Id de empresa
	 */
	public static function compute($entityTypeId, $companyId){
		
	    $result = Anfix::sendRequest(self::$apiBaseUrl,[
            'applicationId' =>  self::$applicationId,
            'companyId' => $companyId,
            'inputBusinessData' => [
				self::$Model => [
                    'EntityTypeId' => $entityTypeId
                ]
            ]
        ]);

        if(empty($result->outputData->{self::$Model}))
            return false;

        return $result->outputData->{self::$Model}->Number;
	}

}
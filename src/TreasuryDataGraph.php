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

class TreasuryDataGraph extends StaticModel
{
    protected static $applicationId = 'E';
	protected static $apiBaseUrl = 'http://apps.anfix.com/facturapro-simple/gestiona/simple/treasury/';
	
	/**
	 * Obtiene información agregada de tesorería.
	 * @param array $params Debe contener CheckExpenses, CheckRevenues, CheckTreasury obligatoriamente
	 * @param string $companyId Id de empresa
	 */
	public static function search(array $params, $companyId){
	
		self::constructStatic();

	    $result = Anfix::sendRequest(self::$apiBaseUrl.'search',[
            'applicationId' =>  self::$applicationId,
            'companyId' => $companyId,
            'inputBusinessData' => [
				self::$Model => $params
            ]
        ]);

        if(empty($result->outputData->{self::$Model}))
            return false;

        return $result->outputData->{self::$Model};
	}
}
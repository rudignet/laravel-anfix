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

class City extends BaseModel
{
    protected $applicationId = '1';

    /**
     * Busca una ciudad por su código postal
     * @param $postalCode
     * @param $countryId
     * @return mixed|null
     */
    public static function findByPostalCode($postalCode,$countryId){
        $data =  self::where([
            'CountryId' => (string)$countryId,
            'PostalCodeCode' => (string)$postalCode
        ])->get([],1,'searchbypostalcode');

        if(empty($data))
            return null;

        return head($data);
    }

}
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

class ReceivedInvoice extends BaseModel
{
    protected $applicationId = 'E';
    protected $update = true;
    protected $create = true;
    protected $delete = true;


    /**
     * Crea un cobro de una factura
     * @param string $date
     * @param float $amount
     * @throws Exceptions\AnfixException
     * @throws Exceptions\AnfixResponseException
     * @return Payment
     */
    public function createPayment($date, $amount){
        $formapago = $this->IssuedInvoicePayChargeMethodId == 'c' ? 'Domiciliación' : 'Tarjeta';
        return Payment::create([
            'PaymentSourceId' => $this->{$this->primaryKey},
            'PaymentSourceType' => '1', //2 Fra recibida
            'PaymentDate' => $date,
            'PaymentDescription' => 'Pago de la factura '.$this->ReceivedInvoiceRefNumber,
            'PaymentAmount' => $amount > 0 ? $amount : $amount * -1,
            'PaymentComments' => 'Pago mediante '.$formapago,
            'PaymentIsRefund' => $amount < 0
        ], $this->companyId);
    }
}
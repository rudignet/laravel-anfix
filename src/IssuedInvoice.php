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

class IssuedInvoice extends BaseModel
{
    protected $applicationId = 'E';
    protected $update = true;
    protected $create = true;
    protected $delete = true;

    /**
     * Crea un pago de una factura
     * @param string $date
     * @param float $amount
     * @return Charge
     * @throws Exceptions\AnfixException
     * @throws Exceptions\AnfixResponseException
     */
    public function createPayment($date, $amount){
        $formapago = $this->IssuedInvoicePayChargeMethodId == 'b' ? 'Tarjeta' : 'Transferencia';
        return Charge::create([
            'ChargeSourceId' => $this->{$this->primaryKey},
            'ChargeSourceType' => '2', //2 Fra emitida
            'ChargeDate' => $date,
            'ChargeDescription' => 'Pago de la factura '.$this->IssuedInvoiceSerialNum.'/'.$this->IssuedInvoiceNumber,
            'ChargeAmount' => $amount > 0 ? $amount : $amount * -1,
            'ChargeComments' => 'Pago mediante '.$formapago,
            'ChargeIsRefund' => $amount < 0
        ], $this->companyId);
    }
    
    /**
     * Devuelve el importe total pagado para la factura actual
     * @return float
     * @throws Exceptions\AnfixException
     * @throws Exceptions\AnfixResponseException
     */
    public function getAmountPayed(){
    	$total = 0;
    	foreach(Charge::where(['ChargeSourceId' => $this->{$this->primaryKey}], $this->companyId)->get() as $charge)
    		$total += ($charge->ChargeAmount * ($charge->ChargeIsRefund ? -1 : 1));
    	
    	return $total;
    }
}

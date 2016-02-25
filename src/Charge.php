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

class Charge extends BaseModel
{
    protected $applicationId = 'E';
    protected $update = true;
    protected $create = true;
    protected $delete = true;

    /**
     * Consulta de cobros a clientes vinculados a facturas emitidas
     * @param array $fields = [] Campos a devolver
     * @param int $maxRows = null Máximo de filas a mostrar
     * @return array BaseModel
     */
    public function getFromInvoice(array $fields = [], $maxRows = null){
        return $this->get($fields,$maxRows,'searchfor-issuedinvoice');
    }
}

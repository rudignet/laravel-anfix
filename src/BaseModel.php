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
class BaseModel
{
    protected $applicationId; //Obligatorio

    protected $Model; //Opcional
    protected $primaryKey; //Opcional
    protected $apiBaseUrl; //Opcional
    protected $update = true; //Por defecto
    protected $create = false; //Por defecto
    protected $delete = false; //Por defecto


    private $draft = [];
    private $attributes = [];
    private $wherecond = [];
    protected $companyId = null;
    private $applicationsIdCompanyMandatory = ['E']; //ApplicationsId que requieren definición de la compañia

    public function __construct(array $params = [], $emptyDraft = false, $companyId = null){

        if(empty($this->applicationId))
            throw new AnfixException('Debe indicar un applicationId en el modelo para poder utilizar la API');

        if(!$this->Model)
            $this->Model = last(explode('\\',get_called_class()));

        if(!empty($params))
            $this->fill(array_except($params,['wherecond']));

        if(isset($params['wherecond']))
            $this->wherecond = $params['wherecond'];

        if($emptyDraft)
            $this->emptyDraft();

        if(empty($this->apiBaseUrl))
            $this->apiBaseUrl = \Config::get("anfix.applicationIdUrl.{$this->applicationId}").strtolower($this->Model).'/';

        if(empty($this->primaryKey))
            $this->primaryKey = $this->Model.'Id';

        if(!empty($companyId))
            $this->companyId = $companyId;
        else if(in_array($this->applicationId,$this->applicationsIdCompanyMandatory))
            throw new AnfixException('Esta entidad requiere companyId');

        return $this;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
        if($value !== null)
            $this->draft[$name] = $value;
        else
            unset($this->draft[$name]);
    }

    public function __get($name){
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Rellena el objeto con los parámetros dados
     * @param array $params
     * @return BaseModel
     */
    public function fill(array $params){
        foreach($params as $k => $value) {
            $this->$k = $value;
        }
        return $this;
    }

    /**
     * Elimina el draft
     */
    public function emptyDraft(){
        $this->draft = [];
    }

    /**
     * Crea o actualiza el objeto Anfix
     * @return BaseModel
     */
    public function save(){
        if(empty($this->draft))
            return $this;

        if(empty($this->attributes[$this->primaryKey])) {
            $return = $this->create($this->draft, $this->companyId);
            $this->fill(get_object_vars($return));
            $this->emptyDraft();
            return $return;
        }else
            return $this->update($this->draft);
    }

    /**
     * Elimina un objeto de la API
     * @throws \Exception
     * @return bool
     */
    public function delete(){
        if(!$this->delete)
            throw new AnfixException("El objeto {$this->Model} no admite eliminación");

        if(empty($this->attributes[$this->primaryKey]))
            throw new AnfixException('No puede borrar un objeto sin id');

        return $this->destroy($this->attributes[$this->primaryKey], $this->companyId);
    }

    /**
     * Devuelve un array de objetos desde la API
     * @param array $fields = [] Campos a devolver
     * @param int $maxRows = null Máximo de filas a mostrar
     * @param string $path = 'search' Path de la función en anfix
     * @return array BaseModel
     */
    public function get(array $fields = [], $maxRows = null, $path = 'search'){
        $obj_data = [];
        $return = [];

        if(!empty($fields))
            $obj_data['Fields'] = $fields;

        if(!empty($this->wherecond))
            $obj_data['Filters'] = $this->wherecond;

        if(!empty($maxRows))
            $obj_data['MaxRows'] = $maxRows;

        $result = Anfix::sendRequest($this->apiBaseUrl.$path,[
            'applicationId' =>  $this->applicationId,
            'companyId' => $this->companyId,
            'inputBusinessData' => [
                $this->Model => !empty($obj_data) ? $obj_data : new \stdClass()
            ]
        ]);
        if($result->outputData->TotalRowNumber == 0)
            return [];

        foreach($result->outputData->{$this->Model} as $params)
            $return[$params->{$this->primaryKey}] = new $this(get_object_vars($params), true, $this->companyId);

        return $return;
    }

    /**
     * Actualiza el Modelo en la API
     * @param array $params
     * @return BaseModel
     * @param string $path = 'update' Path de la función en anfix
     * @throws \Exception
     */
    public function update(array $params, $path = 'update'){
        if(!$this->update)
            throw new AnfixException("El objeto {$this->Model} no admite actualización");

        if(empty($this->attributes[$this->primaryKey]))
            throw new AnfixException("No se puede realizar un update si el objeto no tiene id");

        $params[$this->primaryKey] = $this->attributes[$this->primaryKey];

        $result = Anfix::sendRequest($this->apiBaseUrl.$path,[
            'applicationId' =>  $this->applicationId,
            'companyId' => $this->companyId,
            'inputBusinessData' => [
                $this->Model => $params
            ]
        ]);

        if($result->result == 0) {
            $this->fill($params);
            $this->draft = [];
        }

        return $this;
    }

    // ############################## STATIC FUNCTIONS ##############################

    /**
     * Crea el objeto en la API
     * @param array $params
     * @param string $companyId Identificador de la compañia, sólo necesario en algunas entidades
     * @param string $path = 'create' Path de la función en anfix
     * @return BaseModel
     */
    public static function create(array $params, $companyId = null, $path = 'create'){
        $obj = new static([],false,$companyId);
        if(!$obj->create)
            throw new AnfixException("El objeto {$obj->Model} no admite creación");

        $result = Anfix::sendRequest($obj->apiBaseUrl.$path,[
            'applicationId' =>  $obj->applicationId,
            'companyId' => $companyId,
            'inputBusinessData' => [
                $obj->Model => $params
            ]
        ]);

        $received_params = array_filter(get_object_vars($result->outputData->{$obj->Model}),function($value){ return !is_array($value); });
        $received_params_arr = array_filter(get_object_vars($result->outputData->{$obj->Model}),function($value){ return is_array($value); });

        //Mezclamos aquellos valores que no son de tipo array
        $params = array_merge($params,$received_params);

        //Mezclamos los valores de tipo array manualmente
        foreach($received_params_arr as $k => $arr){
            foreach($arr as $pos => $o){
                if(!empty($params[$k][$pos]) && is_array($params[$k][$pos]) && is_object($o))
                    $params[$k][$pos] = array_merge_recursive($params[$k][$pos], get_object_vars($o));
            }
        }

        return new static($params,true,$companyId);
    }

    /**
     * Destruye un objeto por su id
     * @param $id
     * @param string $companyId Identificador de la compañia, sólo necesario en algunas entidades
     * @param string $path = 'delete' Path de la función en anfix
     * @return bool
     */
    public static function destroy($id, $companyId = null, $path = 'delete'){
        $obj = new static([],false,$companyId);
        $result = Anfix::sendRequest($obj->apiBaseUrl.$path,[
            'applicationId' =>  $obj->applicationId,
            'companyId' => $companyId,
            'inputBusinessData' => [
                $obj->Model => [
                    $obj->primaryKey => $id
                ]
            ]
        ]);

        if($result->result == 0)
            return true;

        return false;
    }

    /**
     * Inserta una condición where para la obtención de datos
     * @param array $conditions
     * @param string $companyId Identificador de la compañia, sólo necesario en algunas entidades
     * @return BaseModel
     */
    public static function where(array $conditions, $companyId = null){
        $wherecond = [];
        foreach($conditions as $k => $val)
            $wherecond[] = [$k => $val];

        return new static(['wherecond' => $wherecond], false, $companyId);
    }

    /**
     * Devuelve un objeto por su id o null si no existe
     * @param $id
     * @param string $companyId Identificador de la compañia, sólo necesario en algunas entidades
     * @return BaseModel
     */
    public static function find($id, $companyId = null){
        $obj = new static([],false,$companyId);
        return static::first([$obj->primaryKey => $id], $companyId);
    }

    /**
     * Devuelve un objeto por su id o una excepción si no existe
     * @param $id
     * @param string $companyId Identificador de la compañia, sólo necesario en algunas entidades
     * @return BaseModel
     * @throws \Exception
     */
    public static function findOrFail($id, $companyId = null){
        $obj = static::find($id, $companyId);
        if(empty($obj))
            throw new AnfixException("No se ha encontrado ningún objeto con el id {$id}");

        return $obj;
    }

    /**
     * Devuelve todos los elementos de la colección
     * @param string $companyId Identificador de la compañia, sólo necesario en algunas entidades
     * @return array BaseModel
     */
    public static function all($companyId = null){
        $obj = new static([], false, $companyId);
        return $obj->get();
    }

    /**
     * Realiza la consulta y devuelve el primer elemento
     * @param array $params
     * @param string $companyId Identificador de la compañia, sólo necesario en algunas entidades
     * @return BaseModel
     */
    public static function first(array $params, $companyId = null){
        $data = static::where($params, $companyId)->get([],1);
        if(empty($data))
            return null;

        return head($data);
    }

    /**
     * Devuelve el primer elemento de la colección o un nuevo objeto
     * @param array $params
     * @param string $companyId Identificador de la compañia, sólo necesario en algunas entidades
     * @return BaseModel
     */
    public static function firstOrNew(array $params, $companyId = null){
        $obj = static::first($params, $companyId);
        if(!empty($obj))
            return $obj;

        return new static($params, false, $companyId);
    }

    /**
     * Devuelve el primer elemento de la colección o crea uno nuevo si no existe
     * @param array $params
     * @param string $companyId Identificador de la compañia, sólo necesario en algunas entidades
     * @return BaseModel
     */
    public static function firstOrCreate(array $params, $companyId = null){
        $obj = static::first($params, $companyId);
        if(!empty($obj))
            return $obj;

        return static::create($params, $companyId);
    }

    /**
     * Devuelve el primer elemento de la colección o genera una Excepción
     * @param array $params
     * @param string $companyId Identificador de la compañia, sólo necesario en algunas entidades
     * @return BaseModel
     */
    public static function firstOrFail(array $params, $companyId = null){
        $obj = static::first($params, $companyId);
        if($obj)
            return $obj;

        throw new AnfixException("No se ha encontrado ningún objeto {$obj->Model} con los parámetros indicados");
    }

}
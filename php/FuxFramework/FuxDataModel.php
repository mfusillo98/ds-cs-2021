<?php

namespace Fux;

/**
 * Classe che permette di essere una base per un DataModel ovvero una composizione di dati che si basano però
 * sui model esistenti dell'app.
 *
 * Ad esempio è possibile avere una entità R1 che ha una relazione 1:N con R2. Attraverso questa classe è possibile
 * estendere e creare una classe specifica per rappresentare una sorgente di dati formattata in modo specifico per
 * le esigenze di business.
 *
 * Quindi è possibile dichiarare una classe del tipo
 *
 * class R1DataModel extends FuxDataModel {
 *  function __constructor(array $r1Data, array $r2List){
 *      $this->setMultipleField($r1Data);
 *      $this->setField("r2List",$r2List);
 *  }
 * }
 *
 * E' possibile poi creare una nuova istanza della classe e accedere ai suoi elementi come segue
 *
 * $obj = new R1DataModel($r1Data, $r2List);
 * $obj->r1Field_A = "test";
 * $obj->r2List[0]->r2Field_A = "I'm editing field A of first element of r2 List"
*/
class FuxDataModel implements \JsonSerializable {

    protected $data = [];

    public function setMultipleField($dataList){
        foreach ($dataList as $key => $value){
            $this->data[$key] = $value;
        }
    }

    public function setField($field, $value){
        $this->data[$field] = $value;
    }

    public function __get($name)
    {
        if(isset($this->data[$name]))
            return $this->data[$name];
        else
            throw new Exception("$name dow not exists");
    }

    public function __toString()
    {
        return json_encode($this->data);
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}

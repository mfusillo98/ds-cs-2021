<?php

class ExampleMutliplePKModel extends FuxModel
{
    public function __construct()
    {
        $this->setTableName("table_name2");
        $this->setTableFields(["field1", "field2", "field3"]);
        $this->setPkField(["field1","field2"]);
    }
}

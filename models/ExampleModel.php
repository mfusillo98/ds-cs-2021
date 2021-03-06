<?php

class ExampleModel extends FuxModel
{
    public function __construct()
    {
        $this->setTableName("table_name");
        $this->setTableFields(["field1", "field2", "field3"]);
        $this->setPkField("field1");
    }
}

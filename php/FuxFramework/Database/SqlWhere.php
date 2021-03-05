<?php


class SqlWhere
{
    private $fields = [];
    private $custom = [];
    /**
     * SqlWhere constructor.
     * @param $fields Array {<fieldName>:<fieldValue>}
     */
    public function __construct($fields)
    {
        $this->fields = $fields;
    }

    public function fields($fields){
        $this->fields = $fields;
        return $this;
    }

    public function addStr($sql, $operator){
        $this->custom[] = ["sql"=>$sql, "operator"=>$operator];
        return $this;
    }

    public function __toString()
    {
        $where = "";
        $fieldsWhere = [];
        foreach($this->fields as $name => $value){
            $fieldsWhere[] = "$name = '$value'";
        }
        $where .= "(".implode(" AND ", $fieldsWhere).")";

        foreach($this->custom as $customWhere){
            if ($where != ""){
                $where.=" $customWhere[operator]";
            }
            $where .= " $customWhere[sql]";
        }
        return $where;
    }
}
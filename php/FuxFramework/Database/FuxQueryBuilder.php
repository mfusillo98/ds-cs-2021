<?php

namespace Fux;

class FuxQueryBuilder
{
    private $selectables = [];
    private $isUpdateQuery = false;
    private $table = "";
    private $setClause = [];
    private $joins = []; // => Array of ["type"=>"left", "table"=>"", "on"=>"where"]
    private $whereClause = [];
    private $groupBy = [];
    private $orderBy = [];
    private $limit;
    private $offset;
    private $havingClause = [];

    public function select($data){
        if (is_array($data)){
            $this->selectables = $data;
        }else {
            $this->selectables = func_get_args();
        }
        return $this;
    }

    public function selectAppend($data){
        if (is_array($data)){
            $newSelects = $data;
        }else {
            $newSelects = func_get_args();
        }
        $this->selectables = array_merge($this->selectables, $newSelects);
        return $this;
    }

    public function update($table){
        $this->table = $table;
        $this->isUpdateQuery = true;
        return $this;
    }

    public function set($field, $value, $valueUseColumns = false){
        $this->setClause[] = $value === null ? "$field = NULL" : ($valueUseColumns ? "$field = $value" : "$field = '$value'");
        return $this;
    }

    public function SQLSet($clause){
        $this->setClause[] = $clause;
        return $this;
    }

    public function massiveSet($fields){
        foreach($fields as $field => $value){
            $this->setClause[] = $value === null ? "$field = NULL" : "$field = '$value'";
        }
        return $this;
    }

    public function from($table, $as = null){
        $this->table = $table;
        if ($as) $this->table.=" as $as";
        return $this;
    }

    public function join($with, $on, $as = null){ return $this->_join("INNER",$with, $on, $as); }
    public function leftJoin($with, $on, $as = null){ return $this->_join("LEFT",$with, $on, $as); }
    public function rightJoin($with, $on, $as = null){ return $this->_join("RIGHT",$with, $on, $as); }
    public function fullJoin($with, $on, $as = null){ return $this->_join("FULL",$with, $on, $as); }

    private function _join($type, $with, $on, $as = null){
        $this->joins[] = ["type"=>$type, "table" => $with, "on" => $on, "as" => $as];
        return $this;
    }

    public function SQLWhere($clause){
        $this->whereClause[] = $clause;
        return $this;
    }

    public function where($field, $value){
        $this->whereClause[] = $value === null ? "$field IS NULL" : "$field = '$value'";
        return $this;
    }

    public function massiveWhere($fields){
        foreach($fields as $fieldName => $wantedValue){
            $this->whereClause[] = "$fieldName = '$wantedValue'";
        }
        return $this;
    }

    public function orderBy($field, $type){
        $this->orderBy[] = [$field, $type];
        return $this;
    }

    public function groupBy(){
        $this->groupBy = func_get_args();
        return $this;
    }

    public function having($field, $value){
        $this->havingClause[] = "$field = '$value'";
        return $this;
    }

    public function massiveHaving($fields){
        foreach($fields as $fieldName => $wantedValue){
            $this->havingClause[] = "$fieldName = '$wantedValue'";
        }
        return $this;
    }

    public function SQLHaving($clause){
        $this->havingClause[] = $clause;
        return $this;
    }

    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset) {
        $this->offset = $offset;
        return $this;
    }

    private function _getWhereParts(){
        $query = [];
        if (!empty($this->whereClause)) {
            $query[] = "WHERE";
            $query[] = join(' AND ', $this->whereClause);
        }
        return $query;
    }

    private function _getGroupByParts(){
        $query = [];
        if (!empty($this->groupBy)){
            $query[] = "GROUP BY";
            $query[] = join(', ', $this->groupBy);
        }
        return $query;
    }

    private function _getOrderByParts(){
        $query = [];
        if (!empty($this->orderBy)){
            $query[] = "ORDER BY";
            $orderBy = [];
            foreach($this->orderBy as $clauses){
                $orderBy[] = $clauses[0]." ".$clauses[1];
            }
            $query[] = implode(",",$orderBy);
        }
        return $query;
    }

    private function _getHavingParts(){
        $query = [];
        if (!empty($this->havingClause)) {
            $query[] = "HAVING";
            $query[] = join(' AND ', $this->havingClause);
        }
        return $query;
    }

    private function _getLimitParts(){
        $query = [];
        if (!empty($this->limit)) {
            $query[] = "LIMIT";
            $query[] = $this->limit;
        }
        if (!empty($this->offset)) {
            $query[] = "OFFSET";
            $query[] = $this->offset;
        }
        return $query;
    }

    private function _getSetParts(){
        $query = [];
        if (!empty($this->setClause)) {
            $query[] = "SET";
            $query[] = join(', ', $this->setClause);
        }
        return $query;
    }

    private function _getQueryPartsForUpdate(){
        $query = [];
        $query[] = "UPDATE";
        $query[] = $this->table;

        $query = array_merge($query, $this->_getSetParts());
        $query = array_merge($query, $this->_getWhereParts());
        $query = array_merge($query, $this->_getGroupByParts());
        $query = array_merge($query, $this->_getOrderByParts());
        $query = array_merge($query, $this->_getHavingParts());
        $query = array_merge($query, $this->_getLimitParts());

        return $query;
    }

    private function _resultForSelect(){
        $query[] = "SELECT";
        // if the selectables array is empty, select all
        if (empty($this->selectables)) {
            $query[] = "*";
        }
        // else select according to selectables
        else {
            $query[] = join(', ', $this->selectables);
        }

        $query[] = "FROM";
        $query[] = $this->table;

        if (!empty($this->joins)){
            foreach($this->joins as $join){
                $query[] = "$join[type] JOIN";
                if ($join['table'] instanceof FuxQuery){
                    $query[] = "($join[table])";
                }else {
                    $query[] = $join['table'];
                }
                if ($join['as']) $query[] = " as $join[as]";
                $query[] = "ON $join[on]";
            }
        }

        $query = array_merge($query, $this->_getWhereParts());
        $query = array_merge($query, $this->_getGroupByParts());
        $query = array_merge($query, $this->_getHavingParts());
        $query = array_merge($query, $this->_getOrderByParts());
        $query = array_merge($query, $this->_getLimitParts());

        return $query;
    }

    public function result(){
        if ($this->isUpdateQuery){
            $query = $this->_getQueryPartsForUpdate();
        }else{
            $query =  $this->_resultForSelect();
        }
        return new FuxQuery(join(' ', $query));
    }

    public function execute($returnFetchAll = true){
        $sql = $this->result();
        $q = DB::ref()->query($sql) or die(DB::ref()->error."SQL: $sql");
        if ($returnFetchAll){
            return $q->fetch_all(MYSQLI_ASSOC);
        }
        return $q;
    }

    public function __toString()
    {
        return (string) $this->result();
    }
}

class FuxQuery{
    private $sql = "";
    public function __construct($sql)
    {
        $this->sql = $sql;
    }
    public function __toString()
    {
        return $this->sql;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Matteo
 * Date: 31/10/2018
 * Time: 20:42
 */

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/DB.php';
require_once __DIR__ . '/FuxQueryBuilder.php';
require_once __DIR__ . '/SqlWhere.php';

use \Fux\DB;
use Fux\FuxQueryBuilder;

if (!defined("MYSQL_CODE_DUPLICATE_KEY")) define("MYSQL_CODE_DUPLICATE_KEY", 1062);
if (!defined("MYSQL_UNKNOWN_FIELD_ERROR")) define("MYSQL_UNKNOWN_FIELD_ERROR", 1054);


class FuxModel
{

    private $table_name = "";
    private $table_fields = [];
    private $pk_field = ["id"];
    private $hasMultiplePk = false;
    private $loadedData = null; //Ogni operazione che recupera un singolo record, salva il record preso in questa variabile
    private $watableExtraActionCallback = null;
    private $watableFieldValueOverrideCallback = null;
    private $saveModes = [];
    private $currentSaveMode = null;
    private $softDelete = false;
    protected $softDeleteField = "data_eliminazione";

    public function setSaveModeFields($modeName, $modeFields)
    {
        $this->saveModes[$modeName] = $modeFields;
    }

    public function setCurrentSaveMode($modeName)
    {
        $this->currentSaveMode = $modeName;
    }

    public function getCurrentSaveModeFields()
    {
        if ($this->currentSaveMode && isset($this->saveModes[$this->currentSaveMode])) {
            return $this->saveModes[$this->currentSaveMode];
        } else {
            return $this->table_fields;
        }
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->table_name;
    }

    /**
     * @param string $table_name
     */
    public function setTableName($table_name)
    {
        $this->table_name = $table_name;
    }

    /**
     * @return array
     */
    public function getTableFields()
    {
        return $this->table_fields;
    }

    /**
     * @param array $table_fields
     */
    public function setTableFields($table_fields)
    {
        $this->table_fields = $table_fields;
    }

    /**
     * @return array
     */
    public function getPkField()
    {
        return $this->pk_field;
    }

    /**
     * @param string | array $pk_field
     */
    public function setPkField($pk_field)
    {
        if (!is_array($pk_field)) $pk_field = array($pk_field);
        $this->pk_field = $pk_field;
        if (is_array($pk_field) && count($pk_field) > 1) $this->hasMultiplePk = true;
    }

    public function setSoftDeletes($status)
    {
        $this->softDelete = $status;
    }

    public function getSoftDeletes()
    {
        return $this->softDelete;
    }


    public function save($data, $ignoreNullData = true, $ignoreClause = false)
    {

        $data = array_intersect_key($data, array_flip($this->table_fields)); //Toglie da $data tutti i campi che non stanno in $this->table_field

        if ($this->currentSaveMode) {
            $data = array_intersect_key($data, array_flip($this->getCurrentSaveModeFields()));
        }

        if (!$this->issetPkField($data)) {
            foreach ($this->pk_field as $pk)
                $data[$pk] = null;
        }

        //Se mi passano l'ID verifico se esiste realmente
        if ($this->issetPkField($data)) {
            if (!$this->getRecordFromPk($data)) {
                $nextPkValue = $data; //Nel caso in cui viene passata una pk non esistente viene salvata e usata per l'insert
                foreach ($this->pk_field as $pk)
                    $data[$pk] = null;
            }
        }

        if ($this->issetPkField($data, true)) { //Modifica del record
            $where = [];
            foreach ($this->pk_field as $pk)
                $where[] = "$pk = '" . $data[$pk] . "'";
            $where = implode(" AND ", $where);

            if ($ignoreNullData) {
                foreach ($data as $field => $value) {
                    if ($value === null) {
                        unset($data[$field]);
                    }
                }
            }

            $sql = (new FuxQueryBuilder())->update($this->table_name)
                ->massiveSet($data)
                ->SQLWhere($where)
                ->result();
        } else {
            if (isset($nextPkValue)) {
                $data = $nextPkValue;
            }
            $sql = FuxModel::insertQuery($this->table_name, $data, $ignoreNullData, $ignoreClause);
        }

        $q = DB::ref()->query($sql) or die(DB::ref()->error . "SQL:" . $sql);

        if (DB::ref()->errno === MYSQL_CODE_DUPLICATE_KEY) return null;

        if ($id = DB::ref()->insert_id) { //Doveva essere creato il record
            return $id;
        } else { //Doveva essere modificato il record quindi se non ci sono errori/cambiamenti allora restituisco la primary key passata in $data
            return (DB::ref()->affected_rows || DB::ref()->errno == 0) ? count($this->pk_field) == 1 ? $data[$this->pk_field[0]] : $data : false;
        }
    }

    public function saveWhere($data, $where, $ignoreNullData = true)
    {
        global $mysqli;

        if ($where instanceof SqlWhere) {
            $where = (string)$where;
        }

        if ($ignoreNullData) {
            foreach ($data as $field => $value) {
                if ($value === null) {
                    unset($data[$field]);
                }
            }
        }

        $sql = (new FuxQueryBuilder())->update($this->table_name)
            ->massiveSet($data)
            ->SQLWhere($where)
            ->result();

        $q = DB::ref()->query($sql) or die(DB::ref()->error . $sql);
        return DB::ref()->affected_rows || DB::ref()->errno == 0;
    }

    public function delete($pk_value, $forceDelete = false)
    {
        $whereClause = [];
        if (is_array($pk_value)){
            foreach($pk_value as $field => $value){
                $whereClause[] = "$field = '$value'";
            }
        }else {
            $whereClause[] = $this->pk_field[0] . " = '$pk_value'";
        }
        return $this->deleteWhere(implode(" AND ",$whereClause), $forceDelete);
    }

    public function deleteWhere($where, $forceDelete = false)
    {
        if ($this->softDelete && !$forceDelete) {
            return $this->saveWhere([$this->softDeleteField => date('Y-m-d H:i:s')], $where);
        }

        if ($where instanceof SqlWhere) {
            $where = (string)$where;
        }
        $sql = FuxModel::deleteQuery($this->table_name, $where);
        $q = DB::ref()->query($sql) or die(DB::ref()->error);
        return DB::ref()->affected_rows || DB::ref()->errno == 0;
    }

    public function getRecord($pk_value, $neededFields = null)
    {
        $qb = (new FuxQueryBuilder())->select("*")->from($this->table_name);

        if (is_array($pk_value)) {
            foreach ($pk_value as $f => $v) $qb->where($f, $v);
        } else {
            $qb->where($this->pk_field[0], $pk_value);
        }

        $sql = $qb->result();

        $q = DB::ref()->query($sql) or die(DB::ref()->error . "-" . $sql);
        if ($row = $q->fetch_assoc()) {
            $this->loadedData = $row;
            if (is_array($neededFields)) $row = array_intersect_key($row, array_flip($neededFields)); //Toglie a $row tutti i campi NON presenti in $neededFields
            return $row;
        }
        return false;
    }

    /**
     * @description: restituisce un record o null che ha le chiavi primarie uguali a quelle contenute nell'oggetto pk_values
     * @param: $pk_values => array/mixed, contiene come chiavi i nomi delle chiavi primarie e come valore il valore da cercare corrispondente
     */
    public function getRecordFromPk($pk_values, $neededFields = null)
    {
        $where = [];
        foreach ($this->pk_field as $pk) {
            if (isset($pk_values[$pk])) {
                $where[] = "$pk = '" . $pk_values[$pk] . "'";
            }
        }
        $where = implode(" AND ", $where);
        return $this->getWhere($where, $neededFields);
    }

    public function getWhere($SQL_WHERE, $neededFields = null)
    {
        global $mysqli;

        if ($SQL_WHERE instanceof SqlWhere) {
            $SQL_WHERE = (string)$SQL_WHERE;
        }

        $sql = "SELECT * FROM " . $this->table_name . " WHERE $SQL_WHERE LIMIT 1";
        $q = DB::ref()->query($sql) or die(DB::ref()->error . " <br>QUERY:" . $sql);
        if ($row = $q->fetch_assoc()) {
            $this->loadedData = $row;
            if (is_array($neededFields)) $row = array_intersect_key($row, array_flip($neededFields)); //Toglie a $row tutti i campi NON presenti in $neededFields
            return $row;
        }
        return false;
    }

    public function listRecords($neededFields = null)
    {
        global $mysqli;
        $l = [];
        $sql = "SELECT * FROM " . $this->table_name;
        $q = DB::ref()->query($sql) or die(DB::ref()->error . $sql);
        while ($row = $q->fetch_assoc()) {
            if (is_array($neededFields)) $row = array_intersect_key($row, array_flip($neededFields)); //Toglie a $row tutti i campi NON presenti in $neededFields
            $l[] = $row;
        }
        return $l;
    }

    public function listWhere($SQL_WHERE, $neededFields = null)
    {
        global $mysqli;
        $l = [];
        if ($SQL_WHERE instanceof SqlWhere) {
            $SQL_WHERE = (string)$SQL_WHERE;
        }
        $sql = "SELECT * FROM " . $this->table_name . " WHERE $SQL_WHERE";
        $q = DB::ref()->query($sql) or die(DB::ref()->error . " QUERY: $sql");
        while ($row = $q->fetch_assoc()) {
            if (is_array($neededFields)) $row = array_intersect_key($row, array_flip($neededFields)); //Toglie a $row tutti i campi NON presenti in $neededFields
            $l[] = $row;
        }
        return $l;
    }

    public function getProperty($fieldName)
    {
        if ($this->loadedData) {
            if (isset($this->loadedData[$fieldName])) return $this->loadedData[$fieldName];
        }
        return false;
    }

    public function getData()
    {
        return $this->loadedData ? $this->loadedData : false;
    }

    /**
     * @param $dtm FuxModel: Riferimento alla classe Model che rappresenta la tabella con cui fare la join
     * @param string|null $onClause <p>
     * Clausola ON nella query oppure se è null tenterà la join sulle chiavi primarie
     * Utilizzare la notazione {{t1}} e {{t2}} rispettivamente come placeholder del nome della prima e seconda tabella
     * </p>
     * @param string $where <p>
     * Clausola WHERE nella query. Di default non c'è condizione.
     * Utilizzare la notazione {{t1}} e {{t2}} rispettivamente come placeholder del nome della prima e seconda tabella
     * </p>
     * @param array|string $table1NeededFields <p>
     * Array di colonne da selezionare dalla tabella 1 o wildcard "*" per averli tutti
     * </p>
     * @param array|string $table2NeededFields </p>
     * Array di colonne da selezionare dalla tabella 2 o wildcard "*" per averli tutti
     * </p>
     * @return mixed <p>
     * Lista di Array associativo che ha come chiavi i nomi delle colonne selezionate
     * </p>
     */
    public function listWithJoin(self $dtm, string $onClause = null, $where = '1', $table1NeededFields = "*", $table2NeededFields = "*", $joinType = "")
    {
        global $mysqli;

        if ($where instanceof SqlWhere) {
            $where = (string)$where;
        }

        $table1 = $this->getTableName();
        $table2 = $dtm->getTableName();

        //Definisco quali devono essere le colonne da selezionare
        $table1NeededFields = $table1NeededFields === "*" ? $this->getTableFields() : $table1NeededFields;
        $table2NeededFields = $table2NeededFields === "*" ? $dtm->getTableFields() : $table2NeededFields;

        $table1NeededFields = array_map(function($field) use($table1) {
            return "$table1.$field";
        }, $table1NeededFields);

        $table2NeededFields = array_map(function($field) use($table2) {
            return "$table2.$field";
        }, $table2NeededFields);

        if ($onClause === null) {
            $onClause = [];
            foreach ($this->getPkField() as $t1Pk) {
                foreach ($dtm->getPkField() as $t2Pk) {
                    if ($t1Pk === $t2Pk) {
                        $onClause[] = "$table1.$t1Pk = $table2.$t2Pk";
                    }
                }
            }
            $onClause = count($onClause) ? implode(" AND ", $onClause) : "1";
        } else {
            $onClause = str_replace("{{t1}}", $table1, $onClause);
            $onClause = str_replace("{{t2}}", $table2, $onClause);
        }

        $where = str_replace("{{t1}}", $table1, $where);
        $where = str_replace("{{t2}}", $table2, $where);

        $fields = implode(",",array_merge($table1NeededFields, $table2NeededFields));

        $sql = "SELECT $fields FROM $table1
                $joinType JOIN $table2 ON $onClause
                WHERE $where";

        $q = DB::ref()->query($sql) or die(DB::ref()->error . $sql);
        return $q->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @description Return the value of the aggregated field name
     * @param string $aggregateFunction : AVG, SUM, COUNT, etc...
     * @param string $aggregateFieldName : A field name that was passed with setTableFields
     * @param string $where : A string that rapresent the WHERE clause
     * @return mixed | bool
     */
    public function getAggregateWhere(string $aggregateFunction, string $aggregateFieldName, string $where = '1')
    {
        global $mysqli;
        if ($where instanceof SqlWhere) {
            $where = (string)$where;
        }
        $sql = "SELECT $aggregateFunction($aggregateFieldName) as $aggregateFieldName FROM " . $this->getTableName() . " WHERE $where";
        $q = DB::ref()->query($sql) or die(DB::ref()->error . $sql);
        if ($row = $q->fetch_assoc()) {
            return $row[$aggregateFieldName];
        }
        return false;
    }

    /**
     * @description Return a list of query result set, composed by the aggregated field name and the group's clause fields
     * @param string $aggregateFunction : AVG, SUM, COUNT, etc...
     * @param string $aggregateFieldName : A field name that was passed with setTableFields
     * @param array | string $groupFieldName : An array of field names or a field name by string that will be used to group the results
     * @param string $where : A string that rapresent the WHERE clause
     * @return mixed
     */
    public function listAggregateGroupsWhere(string $aggregateFunction, string $aggregateFieldName, $groupFieldName, string $where = '1')
    {
        global $mysqli;
        if ($where instanceof SqlWhere) {
            $where = (string)$where;
        }
        $groups = is_array($groupFieldName) ? implode(", ", $groupFieldName) : $groupFieldName;
        $sql = "SELECT $aggregateFunction($aggregateFieldName) as $aggregateFieldName, $groups FROM " . $this->getTableName() . " WHERE $where GROUP BY $groups";
        $q = DB::ref()->query($sql) or die(DB::ref()->error);
        return $q->fetch_all();
    }

    /**
     * @description: Verifica se le chiavi primarie sono settate nell'object $data
     * @return: boolean => true se le chiavi sono settate, false altrimenti
     */
    public function issetPkField($data, $andIsNotNull = false)
    {
        foreach ($this->pk_field as $pk) {
            if (!isset($data[$pk]) || ($andIsNotNull && $data[$pk] === null)) return false;
        }
        return true;
    }

    public static function insertQuery($table, $data, $skipNullData = false, $ignoreClause = false, $execute = false, $updateFieldOnExecute = false)
    {
        global $mysqli;
        $sqlValue = [];
        $sqlField = [];
        foreach ($data as $field => $value) {
            if ($value === null) {
                if (!$skipNullData) {
                    $sqlField[] = $field;
                    $sqlValue[] = 'NULL';
                }
            } else {
                $sqlField[] = $field;
                $sqlValue[] = "'$value'";
            }
        }
        $ignore = $ignoreClause ? "IGNORE" : "";
        $sql = "INSERT $ignore INTO $table (" . implode(",", $sqlField) . ") VALUES (" . implode(",", $sqlValue) . ")";
        if ($execute) {
            $q = DB::ref()->query($sql);
            if (DB::ref()->errno == MYSQL_UNKNOWN_FIELD_ERROR && $updateFieldOnExecute) {
                if (FuxModel::addMissingField($table, $sqlField)) {
                    return FuxModel::insertQuery($table, $data, $skipNullData, $ignoreClause, true, false);
                }
            }
            return $q;
        }
        return $sql;
    }

    public static function deleteQuery($table, $where)
    {
        if ($where instanceof SqlWhere) {
            $where = (string)$where;
        }
        return "DELETE FROM $table WHERE $where";
    }

    public static function fieldList($table)
    {
        global $mysqli;
        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table'";
        $q = DB::ref()->query($sql) or die(DB::ref()->error . $sql);
        $realFields = [];
        while ($row = $q->fetch_assoc()) {
            $realFields[] = $row['COLUMN_NAME'];
        }
        return $realFields;
    }

    public static function addMissingField($table, $fields)
    {
        global $mysqli;
        $realFields = self::fieldList($table);
        $missFields = array_diff($fields, $realFields);
        foreach ($missFields as $f) {
            $sql = "ALTER TABLE $table ADD COLUMN $f VARCHAR(255)";
            $q = DB::ref()->query($sql) or die(DB::ref()->error . $sql);
        }
        return true;
    }


    public function setWatableExtraActionCallback($cb)
    {
        $this->watableExtraActionCallback = $cb;
    }

    public function setWatableFieldValueOverride($cb)
    {
        if (is_callable($cb)) $this->watableFieldValueOverrideCallback = $cb;
    }

    public function watable($removeFields = [], $listCb = null, $actions = ["delete", "edit"])
    {
        $data = array("cols" => array(), "rows" => array());
        $lista = is_callable($listCb) ? $listCb() : $this->listRecords();
        foreach ($lista as $i => $row) { //$row = $lista[$i] ma per valore e non riferimento
            $pk = $row[$this->getPkField()[0]];
            foreach ($lista[$i] as $key => $value) {
                if (in_array($key, $removeFields)) {
                    unset($lista[$i][$key]);
                } else {
                    $lista[$i][$key] = htmlspecialchars($value);
                    if ($this->watableFieldValueOverrideCallback) {
                        if ($newVal = call_user_func($this->watableFieldValueOverrideCallback, $key, $value)) {
                            $lista[$i][$key] = $newVal;
                        }
                    }
                }
            }
            $lista[$i]['azioni'] = "";

            if (in_array("edit", $actions))
                $lista[$i]['azioni'] .= "<button class='btn btn-sm btn-primary mx-1' data-pk='$pk' data-role='watable-action-edit' data-toggle='tooltip' title='Modifica'><i class='fa fa-edit'></i></button>";

            if (in_array("delete", $actions))
                $lista[$i]['azioni'] .= "<button class='btn btn-sm btn-danger mx-1' data-pk='$pk' data-role='watable-action-delete' data-toggle='tooltip' title='Elimina'><i class='fa fa-trash'></i></button>";

            if (is_callable($this->watableExtraActionCallback))
                $lista[$i]['azioni'] .= call_user_func($this->watableExtraActionCallback, $row); //Passo la tupla del db


            $data['rows'][] = $lista[$i];
        }

        $i = 1;
        if (isset($data['rows'][0])) {
            foreach ($data['rows'][0] as $key => $value) {
                $data['cols'][$key] = array(); //Creo la descrizione della colonna
                $data['cols'][$key]['index'] = ++$i;
                $data['cols'][$key]['friendly'] = ucwords(str_replace("_", " ", $key));
                //Tento traduzione del campo friendly
                //$try = Translator::translate("it",$_SESSION['lang'],$data['cols'][$key]['friendly']);
                //if ($try) $data['cols'][$key]['friendly'] = $try;

                $data['cols'][$key]['sorting'] = true;
                $data['cols'][$key]['placeHolder'] = "Cerca nella colonna...";
                if ($key == "azioni") $data['cols'][$key]['index'] = 1;
                if ($key == $this->getPkField()) $data['cols'][$key]['unique'] = true;
            }
        }

        return $data;
    }

}

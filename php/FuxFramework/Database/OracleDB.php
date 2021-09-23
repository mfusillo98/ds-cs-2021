<?php

namespace Fux;

class OracleDB{

    public static function query($sql){
        $stmt = oci_parse(DB::ref(), $sql);
        oci_execute($stmt);
        return $stmt;
    }

    public static function fetchAll($stmt){
        $res = [];
        oci_fetch_all($stmt, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW);
        return $res;
    }

}
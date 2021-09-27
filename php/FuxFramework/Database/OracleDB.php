<?php

namespace Fux;

class OracleDB
{

    public static function query($sql, $binds = [])
    {
        $stmt = oci_parse(DB::ref(), $sql);
        foreach($binds as $name => &$var){
            oci_bind_by_name($stmt, $name, $var);
        }
        $result = oci_execute($stmt);
        if (!$result) {
            echo oci_error();
            return $result;
        }
        return $stmt;
    }

    public static function fetchAll($stmt)
    {
        $res = [];
        oci_fetch_all($stmt, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW);
        return $res;
    }

}
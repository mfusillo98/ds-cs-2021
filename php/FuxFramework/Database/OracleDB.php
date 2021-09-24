<?php

namespace Fux;

class OracleDB
{

    public static function query($sql)
    {
        $stmt = oci_parse(DB::ref(), $sql);
        $result = oci_execute($stmt);
        if (!$result) {
            echo oci_error();
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
<?php

namespace App\Controllers;

use Fux\OracleDB;

class SeedingController
{

    public static function seeding()
    {
        ini_set('max_execution_time',0);
        $stmt = OracleDB::query('CALL populate()');
        oci_free_statement($stmt);
        return "Seeding completed";
    }

}
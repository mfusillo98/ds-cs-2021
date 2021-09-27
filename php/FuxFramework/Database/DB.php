<?php


namespace Fux;
require_once __DIR__ . '/FuxQueryBuilder.php'; //Include dipendenze del DB

class DB
{
    private static $connection = null;
    private static $isTransactionStarted = false;

    public static function ref()
    {
        if (self::$connection) {
            return self::$connection;
        }

        switch(DB_TYPE){
            case DB_TYPE_MYSQL:
                $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
                if (!$connection) {
                    throw new \Error("Database connection error");
                }
                $connection->query("SET time_zone = '+1:00'");
                $connection->query("SET timezone = '+1:00'");
                $connection->set_charset("utf8mb4");
                $connection->query("SET SESSION sql_mode = 'ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
                break;
            case DB_TYPE_ORACLE:
                $connection = \oci_connect(DB_USER,DB_PASSWORD,DB_HOST."/".DB_DATABASE);
                if (!$connection){
                    throw new \Error("Database connection error");
                }
        }



        self::$connection = $connection;

        return self::$connection;
    }

    public static function startSingleTransaction()
    {
        if (!self::$isTransactionStarted) {
            return DB::ref()->begin_transaction();
        }
        return true;
    }

    public static function endSingleTransaction()
    {
        self::$isTransactionStarted = false;
    }

    public static function builder()
    {
        return new FuxQueryBuilder();
    }

    public static function sanitize($str)
    {
        return filter_var($str, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public static function multiQuery($queries)
    {
        $q = DB::ref()->multi_query(implode(";", $queries));
        if (!$q) return false;

        $results = [];

        do {
            /* store first result set */
            if ($result = DB::ref()->store_result()) {
                $results[] = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
            }
        } while (DB::ref()->more_results() && DB::ref()->next_result());

        return $results;
    }
}

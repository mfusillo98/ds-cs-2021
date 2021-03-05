<?php


namespace Fux;
require_once __DIR__.'/FuxQueryBuilder.php'; //Include dipendenze del DB

class DB
{
    private static $connection = null;
    private static $isTransactionStarted = false;

    public static function ref(){
        if (self::$connection){
            return self::$connection;
        }

        $mysqli = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        if (!$mysqli){
            throw new Error("Database connection error");
        }

        $mysqli->query("SET time_zone = '+1:00'");
        $mysqli->query("SET timezone = '+1:00'");
        $mysqli->set_charset("utf8mb4");
        $mysqli->query("SET SESSION sql_mode = 'ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
        self::$connection = $mysqli;

        return self::$connection;
    }

    public static function startSingleTransaction(){
        if (!self::$isTransactionStarted){
            return DB::ref()->begin_transaction();
        }
        return true;
    }

    public static function endSingleTransaction(){
        self::$isTransactionStarted = false;
    }

    public static function builder(){
        return new FuxQueryBuilder();
    }

    public static function sanitize($str){
        return DB::ref()->real_escape_string($str);
    }
}

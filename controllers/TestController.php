<?php


use Fux\OracleDB;

class TestController
{

    public static function myTestMethod(){

        $stmt = OracleDB::query("select * from users");
        $results = OracleDB::fetchAll($stmt);
        echo "<pre>";
        print_r($results);
        echo "</pre>";

        return view("myExampleView",["myViewParameter"=>"HelloWorld"]);
    }

}

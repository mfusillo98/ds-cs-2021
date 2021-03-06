<?php


class TestController
{

    public static function myTestMethod(){
        return view("myExampleView",["myViewParameter"=>"HelloWorld"]);
    }

}

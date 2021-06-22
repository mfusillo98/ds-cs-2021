<?php


namespace Fux\Events;


class EventManager
{
    //Ogni chiave è associata ad un array di callable
    private static $eventsMap = [];

    public static function subscribe(string $eventName, callable $callback){
        if (!isset(self::$eventsMap[$eventName])){
            self::$eventsMap[$eventName] = array();
        }
        self::$eventsMap[$eventName][] = $callback;
    }

    public static function trigger(string $eventName, $param){
        if (isset(self::$eventsMap[$eventName])){
            foreach(self::$eventsMap[$eventName] as $cb){
                call_user_func($cb, $param);
            }
        }
    }
}
<?php

use Fux\Request;
use Fux\Router;

if (!isset($router)) $router = new Router(new Request());


$router->get('/seeding', function (){
    return \App\Controllers\SeedingController::seeding();
});
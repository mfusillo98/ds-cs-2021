<?php

use Fux\Request;
use Fux\Router;

if (!isset($router)) $router = new Router(new Request());


$router->get('/seeding/web-page', function (){
    return \App\Controllers\Seeding\WebpageSeedingController::webpageSeeding();
});
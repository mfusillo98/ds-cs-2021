<?php

use Fux\Request;
use Fux\Router;

if (!isset($router)) $router = new Router(new Request());

$router->get('/', function(Request $request){
    return App\Controllers\IndexController::index($request);
});

$router->get('/search', function(Request $request){
    return App\Controllers\SearchController::index($request);
});

$router->get('/save-search', function(Request $request){
    return App\Controllers\SearchController::saveSearchQuery($request);
});

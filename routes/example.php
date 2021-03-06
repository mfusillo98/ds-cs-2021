<?php

use Fux\Request;
use Fux\Router;

if (!isset($router)) $router = new Router(new Request());

$router->get('/home', function(Request $request){
    return TestController::myTestMethod();
});


$router->get('/error', function (){
    return new FuxResponse("ERROR","This is an error!", null, true);
});
$router->get('/success', function (){
    return new FuxResponse("OK","This is custom success page!", [
        "forwardLink" => "https://google.com",
        "forwardLinkText" => "Go to Google homepage"
    ], true);
});

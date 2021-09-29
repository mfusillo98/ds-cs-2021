<?php

use Fux\Request;
use Fux\Router;

if (!isset($router)) $router = new Router(new Request());

$router->get('/', function(Request $request){
    return App\Controllers\IndexController::index($request);
});

/** @MARK: Search */
$router->get('/search', function(Request $request){
    return App\Controllers\SearchController::index($request);
});

$router->get('/save-search', function(Request $request){
    return App\Controllers\SearchController::saveSearchQuery($request);
});

/** @MARK: Web pages */
$router->get('/add-web-page', function(Request $request){
    return App\Controllers\WebpageController::addWebPagePage();
});

$router->post('/add-web-page', function(Request $request){
    return App\Controllers\WebpageController::addWebPage($request);
});

//Show page content
$router->get('/view-page/{page_url}', function(Request $request){
    return App\Controllers\WebpageController::viewWebPagePage($request);
});

//Show linked pages content
$router->get('/view-linked-pages', function(Request $request){
    return App\Controllers\WebpageController::viewLinkedWebPagesPage($request);
});


/** @MARK: Queries */
$router->get('/saved-queries', function(Request $request){
    return App\Controllers\QueryController::savedQueriesPage();
});

$router->get('/view-query', function(Request $request){
    return App\Controllers\QueryController::viewQueryPage($request);
});

<?php
require_once __DIR__ . '/php/FuxFramework/bootstrap.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With, Application");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD");

include_once './php/FuxFramework/Routing/Request.php';
include_once './php/FuxFramework/Routing/Router.php';
include_once './php/FuxFramework/helpers.php';

if (DB_ENABLE && DB_TYPE === DB_TYPE_MYSQL){
    sanitize_get();
    sanitize_post();
    sanitize_request();
}

use Fux\Request;
use Fux\Router;

EmptyRequestFixService::fix();

$router = new Router(new Request());

/* Load external routes file */
foreach (rglob(__DIR__ . "/routes/*.php") as $filename) {
    include_once($filename);
}




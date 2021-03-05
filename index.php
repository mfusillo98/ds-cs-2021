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


sanitize_get();
sanitize_post();
sanitize_request();

use Fux\DB;
use Fux\Request;
use Fux\Router;

ReactAxiosFixService::fix();


/*
 * LOGGING AREA
 * */

// Get remote IP
// If the site uses cloudflare, the true remote IP is served
// in the HTTP_CF_CONNECTING_IP server var:
$ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];

// Post data / Cookies / Files
if (isset($_POST) && count($_POST)) {
    $postdata = $_POST;
    if (isset($postdata['password'])) {
        $postdata['password'] = "***HIDDEN***";
    }
    if (isset($postdata['password2'])) {
        $postdata['password2'] = "***HIDDEN***";
    }
}


(new LogModel())->save([
    "method" => $_SERVER['REQUEST_METHOD'],
    "url" => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
    "user_agent" => $_SERVER['HTTP_USER_AGENT'] ?? '',
    "body" => DB::ref()->real_escape_string(json_encode($postdata ?? [])),
    "session" => DB::ref()->real_escape_string(json_encode($_SESSION ?? [])),
    "ip" => $ip
]);

/*
 * END LOGGING AREA
 * */


$router = new Router(new Request());

/* Load external routes file */
foreach (rglob(__DIR__ . "/routes/*.php") as $filename) {
    include_once($filename);
}




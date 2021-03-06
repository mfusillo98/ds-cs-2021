<?php
if (session_status() == PHP_SESSION_NONE) session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Rome');
$_POST_SANITIZED = false;
$_GET_SANITIZED = false;
$_REQUEST_SANITIZED = false;

use Fux\DB;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Http/FuxResponse.php'; //Include dipendenze del DB
require_once __DIR__ . '/helpers.php';

/* ##########################
 * Env configuration bootstrapping
 * ########################## */

foreach (glob(__DIR__ . '/../../config/*.php') as $filename) {
    require_once $filename;
}

require_once __DIR__ . '/autoloaders.php';
require_once __DIR__ . '/Service/FuxServiceProvider.php';
require_once __DIR__ . '/Database/FuxModel.php';
require_once __DIR__ . '/View/FuxView.php';
require_once __DIR__ . '/View/FuxViewComposerManager.php';
require_once __DIR__ . '/FuxDataModel.php';

bootstrapServiceProviders();
register_shutdown_function("disposeServiceProviders");

/* ##########################
 * Database bootstrapping
 * ########################## */

require_once __DIR__ . '/Database/FuxQueryBuilder.php'; //Include dipendenze del DB

$now = new DateTime();
$mins = $now->getOffset() / 60;
$sgn = ($mins < 0 ? -1 : 1);
$mins = abs($mins);
$hrs = floor($mins / 60);
$mins -= $hrs * 60;
$offset = sprintf('%+d:%02d', $hrs * $sgn, $mins);

if (DB_ENABLE) {
    DB::ref()->set_charset("utf8");
    DB::ref()->query("SET SESSION sql_mode = 'ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
    DB::ref()->query("SET time_zone='$offset'");
}


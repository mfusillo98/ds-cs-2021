<?php
/* GLOBAL VARIABLES */

include_once (__DIR__."/environment.php");

/** @MARK Project information */
define("BRAND_NAME","FuxFramework");
define("PROJECT_NAME","FuxFramework");
define("OWNER_NAME", "FuxFramework");

/** @MARK Project information */
define("ROOT_DIR",$_SERVER['DOCUMENT_ROOT']);
define("PROJECT_ROOT_DIR", ROOT_DIR.PROJECT_DIR);
define("PROJECT_VIEWS_DIR", PROJECT_ROOT_DIR.'/views');
define("PROJECT_MODELS_DIR", PROJECT_ROOT_DIR.'/models');
define("PROJECT_URL", PROJECT_HTTP_SCHEMA."://".$_SERVER['SERVER_NAME'].PROJECT_DIR);


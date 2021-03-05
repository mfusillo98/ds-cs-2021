<?php

define("DEVELOPMENT_MODE",true);

/**
 * @MARK Environment variables
*/

/** @MARK Domain e Hosting constants */
define("DOMAIN_NAME", $_SERVER['SERVER_NAME'].":8888");
define("PROJECT_DIR", "/subdir"); //Empty string if you want to use root directory, else you need to use a subdir
define("PROJECT_HTTP_SCHEMA","http");

/** @MARK Database Connection */
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASSWORD", "root");
define("DB_DATABASE", "database_name");


<?php

/* ##########################
 * Autoloader per i middlewares
 * ########################## */
$_MIDDLEWARES_FILESYSTEM_TREE = null;
spl_autoload_register(function ($className) {
    global $_MIDDLEWARES_FILESYSTEM_TREE;
    if (strpos($className, "Middleware")) {
        $files = $_MIDDLEWARES_FILESYSTEM_TREE ?? rglob(PROJECT_ROOT_DIR . "/middlewares/*.php");
        foreach ($files as $filePath) {
            $fileName = basename($filePath);
            if ($fileName === "$className.php") {
                include_once $filePath;
                break;
            }
        }
        if (!$_MIDDLEWARES_FILESYSTEM_TREE) $_MIDDLEWARES_FILESYSTEM_TREE = $files;
    }
});

/* ##########################
 * Autoloader per i controllers
 * ########################## */
$_CONTROLLERS_FILESYSTEM_TREE = null;
spl_autoload_register(function ($className) {
    global $_CONTROLLERS_FILESYSTEM_TREE;
    if (strpos($className, "Controller")) {
        $classNameParts = explode("\\", $className);
        $className = end($classNameParts); //Rimuovo la parte di namespacing
        $files = $_CONTROLLERS_FILESYSTEM_TREE ?? rglob(PROJECT_ROOT_DIR . "/controllers/*.php");
        $found = false;
        foreach ($files as $filePath) {
            $fileName = basename($filePath);
            if ($fileName === "$className.php") {
                $found = true;
                include_once $filePath;
                break;
            }
        }
        if (!$found) {
            throw new Exception("FuxAutoloaderException: Cannot autoload class $className");
        }

        if (!$_CONTROLLERS_FILESYSTEM_TREE) $_CONTROLLERS_FILESYSTEM_TREE = $files;
    }
});

/* ##########################
 * Autoloader per i models
 * ########################## */
$_MODELS_FILESYSTEM_TREE = null;
spl_autoload_register(function ($className) {
    global $_MODELS_FILESYSTEM_TREE;
    if (strpos($className, "Model")) {
        $files = $_MODELS_FILESYSTEM_TREE ?? rglob(PROJECT_ROOT_DIR . "/models/*.php");
        foreach ($files as $filePath) {
            $fileName = basename($filePath);
            if ($fileName === "$className.php") {
                include_once $filePath;
                break;
            }
        }
        if (!$_MODELS_FILESYSTEM_TREE) $_MODELS_FILESYSTEM_TREE = $files;
    }
});


/* ##########################
 * Autoloader per i file nella cartella \App
 * ########################## */
spl_autoload_register(function ($className) {
    if (strpos($className, "App\\") !== false) {
        $relativeClassPath = str_replace("App/", "", str_replace("\\", "/", $className));
        $filePath = __DIR__ . "/../../app/$relativeClassPath.php";
        if (file_exists($filePath)) {
            include_once $filePath;
        } else {
            throw new Exception("FuxAutoloaderException: Cannot autoload app class $className");
        }
    }
});

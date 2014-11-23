<?php
$APP_NAME = 'Medley';

// Debug
ob_start();

date_default_timezone_set('America/New_York');

error_reporting(E_ERROR);

include_once 'debug.php'; # sets debug mode 
if (isset($DEBUG) && $DEBUG) {
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', true);
    ini_set('html_errors', false);
    define('DEBUG_MODE', 'ON');
    $DEBUG = true;
} else {
    ini_set('display_errors', false);
    define('DEBUG_MODE', 'OFF');
    $DEBUG = false;
}

define('IFX_ROOT', dirname(__FILE__));

define('DB_USERNAME', 'influx');
define('DB_PASSWORD', 'influx');
define('DB_HOST', 'localhost');
define('DB_DATABASE', 'influx');

?>

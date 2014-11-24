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

$REMOTE_HOST = "medleymovies.me";
if (strpos($_SERVER['HTTP_HOST'], $REMOTE_HOST) !== FALSE) {
	define('DB_USERNAME', '1741910_medley');
	define('DB_PASSWORD', 'influxinfluxmedleyinflux9');
	define('DB_HOST', 'fdb6.atspace.me');
	define('DB_DATABASE', '1741910_medley');
} else {
	define('DB_USERNAME', 'influx');
	define('DB_PASSWORD', 'influx');
	define('DB_HOST', 'localhost');
	define('DB_DATABASE', 'influx');
}
?>
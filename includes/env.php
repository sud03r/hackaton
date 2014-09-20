<?php
	$APP_NAME = 'Influx';
    
	// Debug
    ob_start();
    error_reporting(E_ERROR);
    ini_set('display_errors', true);
    ini_set('html_errors', false);
    define('DEBUG_MODE', 'ON');

    define('IFX_ROOT', dirname(__FILE__));

    define('DB_USERNAME', 'influx');
    define('DB_PASSWORD', 'influx');
    define('DB_HOST', 'localhost');
    define('DB_DATABASE', 'influx');
?>


<?php
	$APP_NAME = 'Medley';
    
	// Debug
    ob_start();
    error_reporting(E_ERROR);
	error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', true);
    ini_set('html_errors', false);
    define('DEBUG_MODE', 'ON');

    define('IFX_ROOT', dirname(__FILE__));

    define('DB_USERNAME', 'influx');
    define('DB_PASSWORD', 'influx');
    define('DB_HOST', '192.168.0.17');
    define('DB_DATABASE', 'influx');

	// TODO David: I know this replicates 'DEBUG_MODE' but
	// I find it more convenient;
	if (!isset($DEBUG))
//		$DEBUG = true; // turn it on
		$DEBUG = false; // turn it off
?>

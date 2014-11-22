<?php
require_once(dirname(__FILE__). "/env.php");
define('QUERY_FAILURE', 'query_failure');
class Db {

    private static $conn = FALSE;

	public static function init() {
		if( self::$conn != FALSE )
			return;
		self::$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		if( self::$conn === FALSE ) {
			header('location: oops');
			exit;
		}
	}

	public static function query($q) {
		$result = mysqli_query(self::$conn, $q);
		if( $result===FALSE ) {
			self::logError(QUERY_FAILURE, $q, mysqli_error(self::$conn));
			return FALSE;
		}
		return $result;
	}

	public static function getNumRows($result) {
		
//		if (!is_null($result) && $result !== FALSE) {
			return mysqli_num_rows($result);
//		} else {
//			debug_print_backtrace();
//			return 0;
//		}
	}

	public static function getNumRowsAffected() {
		return mysqli_affected_rows(self::$conn);
	}

	public static function getNextRow($result) {
		return mysqli_fetch_array($result);
	}

	public static function escape($str) {
		return mysqli_real_escape_string($str, self::$conn);
	}
	public static function escapeArray($arr) {
		$escaped = array();
		foreach( $arr as $item ) {
			array_push($escaped, Db::escape($item));
		}
		return $escaped;
	}
	public static function getInsertId() {
		return mysqli_insert_id(self::$conn);
	}

	private static function logError($error, $query, $details) {
		trigger_error("Failure when running query '$query'. Response was '$details'", E_USER_ERROR);
		// die($details);
		// todo set up proper logging
	}	

}
Db::init();
?>

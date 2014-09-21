<?php

define('QUERY_FAILURE', 'query_failure');

class Db {

    private static $conn = FALSE;

	public static function init() {
		if( self::$conn != FALSE )
			return;
		self::$conn = mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
		if( self::$conn === FALSE ) {
			header('location: oops');
			exit;
		}
		$db = mysql_select_db(DB_DATABASE, self::$conn);
		if( $db===FALSE ) {
			header('location: oops');
			exit;
		}
	}

	public static function query($q) {
		$result = mysql_query($q, self::$conn);
		if( $result===FALSE ) {
			self::logError(QUERY_FAILURE, $q, mysql_error(self::$conn));
			return FALSE;
		}
		return $result;
	}

	public static function getNumRows($result) {
		return mysql_num_rows($result);
	}

	public static function getNumRowsAffected() {
		return mysql_affected_rows(self::$conn);
	}

	public static function getNextRow($result) {
		return mysql_fetch_array($result);
	}

	public static function escape($str) {
		return mysql_real_escape_string($str, self::$conn);
	}
	public static function escapeArray($arr) {
		$escaped = array();
		foreach( $arr as $item ) {
			array_push($escaped, Db::escape($item));
		}
		return $escaped;
	}
	public static function getInsertId() {
		return mysql_insert_id(self::$conn);
	}

	private static function logError($error, $query, $details) {
		//echo ($query);
		//die($details);
		// todo set up proper logging
	}	
	
}

Db::init();

?>

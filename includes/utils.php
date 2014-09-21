<?php
/*
	This file has some utility methods.
*/

require_once(dirname(__FILE__) . "/movie.php");
class Utils {

	public static function printArray($arr) {
		echo "[";
		foreach ($arr as $entry)
			echo "$entry, ";
		echo "]\n";
	}

	public static function printDict($dict) {
		echo "[";
		foreach ($dict as $key => $val)
			echo "\"$key\"=>\"$val\", ";
		echo "]\n";
	}

	public static function getWebData($url) {
		$url = str_replace(' ', '%20', $url);
		return file_get_contents($url);
		/*$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL,'$url');
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Influx');
		$query = curl_exec($curl_handle);
		curl_close($curl_handle);*/
	}


	public static function createMovieFromDbRow($row) {
		$movie = new Movie($row['name'], $row['rNetflix'], $row['id'], $row['year'], $row['imageURL']);
		$movie->populateFromIMDB(json_decode($row['imdbJSON'], true));
		$matches = array();
		$numMatches = preg_match('/"similar":"(.*?)"/', $row['rottenJSON'], $matches);
		$similarLink = "";
		if (count($matches) > 0)
			$similarLink = $matches[1];
			
		$movie->populateFromRottenTomatoes($similarLink, $row['rRotTomCritic'], $row['rRotTomViewer']);
		return $movie;
	}
}

?>

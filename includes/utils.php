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

	/*
		Removes everything after the last space,
		If there's no space, return the empty string.
	*/
	public static function removeLastWord($string) {
		$lastSpacePosition = strrpos($string," ");
		if ($lastSpacePosition === false) {
			return ""; // not found
		} else {
			return substr($string,0,$lastSpacePosition);
		}
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
		$matchNetflix = array();
		preg_match('/^"(.*?)"/', $row['netflixJSON'], $matchNetflix);
		$netflixId = $matchNetflix[1];

		$movie = new Movie($row['name'], $row['rNetflix'], $netflixId, $row['year'], $row['imageURL']);
		$movie->populateFromIMDB(json_decode($row['imdbJSON'], true));
		$matches = array();
		$numMatches = preg_match('/"similar":"(.*?)"/', $row['rottenJSON'], $matches);
		$similarLink = "";
		if (count($matches) > 0)
			$similarLink = $matches[1];
			
		$movie->populateFromRottenTomatoes($similarLink, $row['rRotTomCritic'], $row['rRotTomViewer']);
		return $movie;
	}


	/** 
		This function takes a string representation of a year specified in 
		the format '56, and returns the 4 digit version 1956. 
		
		Returned value is integer.

		This function may be called with a four digit year representation too,
		in which case it will simply return it as an integer.

		TODO: Note that the current implementation works until 2020..
	*/
    public static function fixYearFormat($date) {
        if ($date[0] == "'") {
            $num = substr($date, 1);
            if (intval($num)<20) // TODO this is horrible
                return (2000+intval($num));
            else
                return (1900+intval($num));
        }
        return intval($date);
    }
}

?>

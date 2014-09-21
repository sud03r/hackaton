<?php
require_once(dirname(__FILE__) . "/Db.php");
require_once(dirname(__FILE__) . "/utils.php");
class Query {

	// returns an array (possibly empty) of array objects
	public static function byTitle($title) {
        $movies = array();
		// TODO: can get fancy here matching similar things...
		$result = Db::query("select * from movies where name LIKE '%$title%';");
		for ($i = 0; $i < Db::getNumRows($result); $i++) {
			$row = Db::getNextRow($result);
			array_push($movies, Utils::createMovieFromDbRow($row));
		}
		return $movies;
	}

	// takes one movie title and returns an array of similar movies (object!)
	public static function bySimilarity($movie) {
        $movies = array();
		if ($movie->similarLink != "") {
			$url="$movie->similarLink?limit=5&apikey=y9ycwv778uspxkj6g4txme2h";
			$similar = json_decode(Utils::getWebData($url), true);
			foreach ($similar["movies"] as $sMovie) {
				$movies += Query::byTitle($sMovie["title"]);
			}
		}
		return $movies;
	}
}

/*$movies = Query::byTitle("gump");
foreach ($movies as $movie) {
	echo "$movie->mName\n";
	Query::bySimilarity($movie);
}*/

?>

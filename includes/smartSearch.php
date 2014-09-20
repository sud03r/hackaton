<?php
require_once(dirname(__FILE__) . "/movie.php");
/*
	This file contains SMART search functions. 
*/


/* Performs searches that requires pre-processing at our end, return an array of matching movies.
	- (We expect movie list here, all operations are Linear-time wrt to that list, so the shorter the input
		list is, better off we are)
	- (DONE) movies (genre)
	- (TODO: with david)movies like ... (title of the movie) "The Notebook"
	- (TODO) movies after ... (date) "2012"
	- (TODO) movies before ... (date) "2012"
	- (Linear-Time) movies rated higher than ... (rating)
	- (Linear-time) movies (family rating)
	- (Linear-time) movies shorter than ... (length of movie)
	- (Linear-time) movies longer than ... (length of the movie"
	- (Linear-time) Complex Searches (combining the simple searches, I'm not going to list all the combinations)
	- movies (genre) with ... (person)
    	"romantic" movies with "Ryan Gosling"
	- movies like ... (title of the movie) with ... (person)
    	movies like "The Notebook" with "Scarlett Johannson"
	- movies after ... (date) with ... (person)
    	movies after "2010" with "Ryan Gosling"
	- movies (genre) before ... (date)
    	"romantic" movies before "1990"
	- movies with ... (person) AND rated higher than ... (rating)
	- movies with "Ryan Gosling" AND rated higher than "8.0"
	- movies (family rating) rated higher than ... (rating)
	- "PG" movies rated higher than "8.0
*/

class SmartSearch {

	public static function mapByGenre($movies) {
		$genreToIdx = array();
		for ($idx = 0; $idx < count($movies); $idx++) {
			foreach ($movies[$idx]->genres as $genre) {
				echo "$genre";
				if (array_key_exists($genre, $genreToIdx))
					$genreToIdx[$genre] = $genreToIdx[$genre] . "$idx";
				else
					$genreToIdx[$genre] = "$idx";
			}
		}
		return $genreToIdx;
	}

	public static function filterByGenre($movies, $genre) {
		$genreToIdx = SmartSearch::mapByGenre($movies);
		$filteredIndices = str_split($genreToIdx[$genre]);
		$filteredMovies = array();
        foreach($filteredIndices as $idx) {
            array_push($filteredMovies, $movies[$idx]);
        }
        return $filteredMovies;
	}
	
	public static function filterByGenreAdv($movies, $genre1, $op, $genre2) {
		$genreToIdx = SmartSearch::mapByGenre($movies);
		$g1Indices = str_split($genreToIdx[$genre1]);
		$g2Indices = str_split($genreToIdx[$genre2]);
		$filteredIndices = array();
		if ($op == "AND")
			$filteredIndices = array_intersect($g1Indices, $g2Indices);
		else if ($op == "OR")
			$filteredIndices = $g1Indices + $g2Indices;
		else if ($op == "NOT")
			$filteredIndices = array_diff($g1Indices, $g2Indices);
		
		$filteredMovies = array();
		foreach($filteredIndices as $idx) {
			array_push($filteredMovies, $movies[$idx]);
		}
		return $filteredMovies;
	}

	public static $scoreDatabase = array(
		"imdb" => "imdb",
		"netflix" => "netflix",
		"rotten tomato audience" => "audience_score",
		"rotten tomato critics" => "critics_score",
		);

	# 'where' is one of "imdb", "netflix", "rotten tomato audience", "rotten tomato critics",
	# case insensitive
	public static function moviesRatedHigher($movieList, $where, $rValue) {
		$filteredMovies = array();
		foreach ($movieList as $movie) {
			if ($movie->rating[ SmartSearch::$scoreDatabase[ strtolower("$where")] ] >= $rValue)
				array_push($filteredMovies, $movie);
		}
		return $filteredMovies;
	}

	public static function moviesShorterThan($movieList, $duration) {
		$filteredMovies = array();
		foreach ($movieList as $movie) {
			if ($movie->runtime <= $duration)
				array_push($filteredMovies, $movie);
		}
		return $filteredMovies;
	}
	
	public static function moviesLongerThan($movieList, $duration) {
		$filteredMovies = array();
		foreach ($movieList as $movie) {
			if ($movie->runtime >= $duration)
				array_push($filteredMovies, $movie);
		}
		return $filteredMovies;
	}
	
	public static function moviesLike($movieName) {
		$movieList = basicSearch("title=$movieName");
		// Picking the first match, coz ideally there shouldn't be many matches
		$movie = $movieList[0];
		$movieList = basicSearch("title=$movieName");
	}
}

?>

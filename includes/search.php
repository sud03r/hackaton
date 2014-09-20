<?php
/*
	This file contains search functions. 
*/
require_once(__DIR__ . "/movie.php");
require_once(__DIR__ . "/utils.php");

/* Perform a basic search, return an array of matching Movies.

In the basic search you can specify a set of properties of the movie. These are:
- title
- year
- director
- actor
(This is what I see documented at the moment, if I find more I will add it here...)

The $searchString can be any combination of these and should look like::

	keyword1=value&keyword2=value

For example:

	title=The%20Boondocks&year=2005

----------------------------------------------------------------------------------
Special cases and Error handling:
- If there are no queries matching on netflix, we simply return an empty list.
- If the movie exists on netflix, but we cannot access the imdb data, we leave
	the fields that were supposed to be coming from imdb empty.
*/

function basicSearch($searchString) {
	// TODO this is not secure... check this for better options:
	// http://www.bin-co.com/php/scripts/load/
	$url = "http://netflixroulette.net/api/api.php?" . $searchString;
	$contents = Utils::getWebData($url);

	if ($contents === false) {
		// the query failed, return an empty list
		return array();
	}

	// now we extract the name, netflix rating and id, date
	$moviesData = json_decode($contents);
	$movies = array(); // the list of movies we populate

	if (is_array($moviesData)) {
		foreach ($moviesData as $movie) {
			array_push($movies, getMovieFromNetflixData($movie));
		}
	} else {
		array_push($movies, getMovieFromNetflixData($moviesData));
	}

	// At this point we have a list of all movies with partial info.
	// - much of our information we get from imdb
	// also some from rotten tomatoes
	foreach ($movies as $movie) {
		$imdbData = getImdbData($movie);
		if (!is_null($imdbData)) {
//			echo "reading from imdb for " . $movie->mName . "\n";
			$movie->populateFromIMDB($imdbData);
		}
		/* UNDER DEV -------------------------------------------
		$rottenData = getRottenData($movie);
		if (!is_null($rottenData)) {
			// this data might have a number of movies..
			$similarTitles = array(); // this will keep track of similar titles
			$ourMovie = selectOurMovieFromRotten($rottenData, $movie, $similarTitles);
			// now we add the info from rottenTomatoes to the selected movie
			$movie.populateFromRottenTomatoes($ourMovie->["ratings"])
			$movie.addSimilarTitles($similarTitles);
		}
		*/
	}

	return $movies;
}



// ------------------------------------------------------ //
// These are utility functions                            //
// ------------------------------------------------------ //

// Takes an object generated from the json response of the netflix API,
// creates and returns a Movie object based on it.
function getMovieFromNetflixData($movieData) {
	return new Movie($movieData->show_title, $movieData->rating, $movieData->show_id, $movieData->release_year);
}

/* Tries to get the data from imdb for the specified Movie object.

If we are not successful, null is returned.
*/
function getImdbData($movie) {
	$urlBase = "http://www.omdbapi.com/?t=" . $movie->mName;
//	echo "trying=" . $urlBase . "&y=" . $movie->year . "\n";
	$imdbjson = Utils::getWebData($urlBase . "&y=" . $movie->year);
//	var_dump($imdbjson);
	if ($imdbjson !== false) {
		$mdata = json_decode($imdbjson, true);
		if ($mdata['Response'] === "True") {
			return $mdata;
		} else {
			// we could not find a matching movie...
			// so relax the year constraint
//			echo "..trying=" . $urlBase . "\n";
			$imdbjson = @Utils::getWebData($urlBase);
			if ($imdbjson !== false) {
				$mdata = json_decode($imdbjson, true);
				if ($mdata['Response']) {
					return $mdata;
				}
			}
		}
	}
	return null; // at this point we failed
}

?>

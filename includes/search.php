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
	the fields that were supposed to be coming from imdb empty / we never set them.
- Same thing with rotten tomatoes.
*/

function basicSearch($searchString) {
	require_once(__DIR__ . "/tests/createMovies.php");
	return getMovies();

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

		$rottenData = getRottenDataByName($movie->mName);
		if (!is_null($rottenData) && count($rottenData) > 0) {
			// this data might have a number of movies..
			$similarTitles = array(); // this will keep track of similar titles
			$ourMovie = selectOurMovieFromRotten($rottenData, $movie, $similarTitles);
			// now we add the info from rottenTomatoes to the selected movie
			$movie->populateFromRottenTomatoes($ourMovie["ratings"]);
			$movie->addSimilarTitles($similarTitles);
		}
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

/* Make a query to the rotten tomatoes with the movie name.

We return the decoded json (list of movie entries, may have 0 length); 
or 'null' if we don't get a reply.
*/
function getRottenDataByName($movieName) {
	$urlBase = "http://api.rottentomatoes.com/api/public/v1.0/movies.json?q=" . $movieName . "&page_limit=10&page=1&apikey=y9ycwv778uspxkj6g4txme2h";
	$imdbjson = Utils::getWebData($urlBase);
//	var_dump($imdbjson);
	if ($imdbjson !== false) {
		$mdata = json_decode($imdbjson, true);
		return $mdata['movies'];
	}
	// at this point the quiery failed
	return null;
}

/* Given a list of movies in $rottenData it selects $myMovie from it -- BASED ON YEAR.
All other ones it will put in $similarTitles as "title"=>id entries; this is passed by reference.
*/
function selectOurMovieFromRotten($rottenData, $myMovie, &$similarTitles) {
	$selected = null;
	foreach ($rottenData as $mData) {
		// now this is data for just one movie.
		if (is_null($selected) && $mData['year'] == $myMovie->year) {
			$selected = $mData;
		} else {
			$similarTitles += array($mData['title'] => $mData['id']);
		}
	}
	// if we didn't select anything, select the first result now
	if (is_null($selected)) {
		$selected = $rottenData[0];
		unset($similarTitles[ $selected["title"] ]);
	}

	return $selected;
}

?>

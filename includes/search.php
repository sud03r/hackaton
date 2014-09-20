<?php
/*
	This file contains search functions. 
*/
require_once(__DIR__ . "/Movie.php");

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

*/
function basicSearch($searchString) {
	// TODO this is not secure... check this for better options:
	// http://www.bin-co.com/php/scripts/load/
	$url = "http://netflixroulette.net/api/api.php?" . $searchString;
	$contents = file_get_contents($url);
	# now we extract the name, netflix rating and id, date
	$moviesData = json_decode($contents);
	$movies = array();
	if (is_array($moviesData)) {
		foreach ($moviesData as $movie) {
//			var_dump($movie);
			array_push($movies, getMovieFromNetflixData($movie));
		}
	} else {
//		var_dump($moviesData);
		array_push($movies, getMovieFromNetflixData($moviesData));
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





?>


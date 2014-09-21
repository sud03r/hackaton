<?php

class Query {

	// returns an array (possibly empty) of array objects
	public static function byTitle($title) {
		// TODO can get fancy here matching similar things...
		
		// TODO write the sql query

        $movies = array();
		// if we get non-zero number of hits, turn then into movies based on json
		// -- push results in there if any

		return $movies;
	}

	// takes one movie title and returns an array of similar movies (object!)
	public static function bySimilarity($movieTitle) {
        // get the rotten tomatoes id from sql for this movie title
        
        // query rottenTomatoes...
        
        return array();
	}

}


?>

<?php

class Movie {

	public $name, $netflixId;
	public $rImdb, $rNetflix, $rRotTom;
	public $date;
	public $actors, $directors; # These are arrays.
	public $runtime;
	public $imdbVotes, $language;

	# Construct the movie object from netflix data
	public function __construct($name, $rNetfLix, $netflixId, $date) {
		echo "Created movie with: $name, $rNetfLix, $netflixId, $date\n";
	}  

	# From the key-value pairs, populate the unfilled fields
	public function populateFromIMDB($imdbData) {
	}

	# Populate the rotten tomatoes
	public function populateFromRottenTomatoes($rRotTom) {
	}
}

	$movie = new Movie("Forrest Gump", '9', '12345', '1994');
?>

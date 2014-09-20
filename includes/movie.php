<?php

class Movie {

	# Construct the movie object from netflix data
	public function __construct($name, $rNetfLix, $netflixId, $date) {
		$this->mName = $name;
		$this->rNetfLix = $rNetfLix;
		$this->netflixId = $netflixId;
		$this->date = $date;
	}  

	# From the key-value pairs, populate the unfilled fields
	public function populateFromIMDB($imdbData) {
		$imdbData = json_decode($imdbData, true);
		$this->rFamily = $imdbData['Rated'];
		$this->date = $imdbData['Released'];
		$this->runtime = $imdbData['Runtime'];
		$this->genres = explode(',', $imdbData['Genre']);
		$this->directors = explode(',', $imdbData['Director']);
		$this->writers = explode(',', $imdbData['Writer']);
		$this->actors = explode(',', $imdbData['Actors']);
		$this->plot = $imdbData['Plot'];
		$this->language = $imdbData['Language'];
		$this->country = $imdbData['Country'];
		$this->awards = $imdbData['Awards'];
		$this->image = $imdbData['Poster'];
		$this->rImdb = $imdbData['imdbRating'];
		$this->imdbVotes = $imdbData['imdbVotes'];
		$this->mType = $imdbData['Type'];
	}

	# Populate the rotten tomatoes
	public function populateFromRottenTomatoes($rRotTom) {
	}
}
?>

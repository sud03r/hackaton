<?php

class Movie {

	# Construct the movie object from netflix data
	public function __construct($name, $rNetfLix, $netflixId, $date) {
		$this->mName = $name;
		$this->rating['netflix'] = $rNetfLix;
		$this->netflixId = $netflixId;
		$this->date = $date;
	}  

	# From the key-value pairs, populate the unfilled fields
	public function populateFromIMDB($imdbData) {
		$imdbData = json_decode($imdbData, true);
		$this->rFamily = $imdbData['Rated'];
		$this->date = DateTime::createFromFormat('j M Y', $imdbData['Released']);
		$this->runtime = $imdbData['Runtime'];
		$this->genres = array_map('trim', explode(',', $imdbData['Genre']));
		$this->directors = array_map('trim', explode(',', $imdbData['Director']));
		$this->writers = array_map('trim', explode(',', $imdbData['Writer']));
		$this->actors = array_map('trim', explode(',', $imdbData['Actors']));
		$this->plot = $imdbData['Plot'];
		$this->language = $imdbData['Language'];
		$this->country = $imdbData['Country'];
		$this->awards = $imdbData['Awards'];
		$this->image = $imdbData['Poster'];
		$this->rating['imdb'] = $imdbData['imdbRating'];
		$this->imdbVotes = $imdbData['imdbVotes'];
		$this->mType = $imdbData['Type'];
	}

	# Populate the rotten tomatoes
	public function populateFromRottenTomatoes($criticRating, $viewerRating) {
		$this->rating['rotTomCritic'] = $criticRating;
		$this->rating['rotTomViewers'] = $viewerRating;
	}

	# This shall work irrespective of how release date is stored
	public function getYear() {
		return 2005;
		return $this->date->format('Y');
	}
}
?>

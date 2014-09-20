<?php

class Movie {

	# Construct the movie object from netflix data
	public function __construct($name, $rNetfLix, $netflixId, $year) {
		$this->mName = $name;
		$this->rating['netflix'] = $rNetfLix;
		$this->netflixId = $netflixId;
		$this->year = $year;
	}  

	# From the key-value pairs, populate the unfilled fields.
	public function populateFromIMDB($imdbDataJson) {
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

}
?>

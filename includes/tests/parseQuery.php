<?php

$DEBUG = true;

require_once(__DIR__ . "/../parsing/parseQuery.php");
use \parsing\ParseQuery;

$queries = array(
    "romantic comedies"=>"feeling=>genre + genre(alias)",
    "comedies from before 1970"=>"genre(alias) + daterange",
    "funny family movie"=>"emotion->genre + genre",
	"thriller movies"=>"genre",
    "sad movies"=>"emotion->2 OK genres",
	"Scary movies rated higher than 7 on imdb"=>"emotion(case)->genre + rating/imdb",
	"movies directed by Tom Hanks with rating higher than 61% on rotten tomato"=>"this should check both critic and audiance and if matches either, report",
	"romantic comedy from before 1970"=>"genre info",
	"romance or drama with ryan Gosling"=>"logical and genre",
	"not thriller by Al Pacino"=>"negative logical",
	"scary movie"=>"return both the comedy and thrillers/horrors",
	"before sunset"=>"return the movie",
	"movies like the expandables"=>"pretty clear..",
	"Movies with Ryan Gosling before 2000"=>"claer...",
	"quentin tarantino movies"=>"ja..",
	"quenton tarantino movies"=>"typo in his name",
	"ryan gosling movies like Drive"=>"should match -movies like-",
    "movies like Drive and drama"=>"should match -movies like-",	
	"Hitchhiker's Guide to the Galaxy"=>"missing 'the' from the beginning",
	"The Princess Bride"=>"easy",
	"tarantino 2009"=>"ahh",
	"movies since 2000 that are PG-13" => "jesus christ",
	"family movie that is shorter than 90 minutes"=>"why not?",
	"Bob & Carol & Ted & Alice" => "special characters...",
	"Don't Look Back" => "more special characters...",
	"8 1/2" => "We are building something insanely robust here"
);

$iter = 0;
foreach ($queries as $query => $descr) {
# 	if ($iter > 3) break;
	$iter += 1;
	echo "-- query: $query\n";
	echo "          $descr\n";
	$movieMatching = ParseQuery::parseQuery($query);
	$tmp = array();
	foreach ($movieMatching as $movie) {
		$tmp[] = ($movie->movie->mName . $movie->relevance);
	}
    echo "Number of matching results: ";
	print_r(count($tmp));
	//
	//var_dump($tmp);
	echo "\n\n";
}



?>

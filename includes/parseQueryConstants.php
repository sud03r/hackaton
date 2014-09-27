<?php
namespace parsing;
/**
	This file contains (almost?) all the constants that parseQuery
	and related classes use.
*/

// TODO map emotions to genres
// TODO plural versions of genres
	
/*
	A word about the encoding.

		"base" describes what category the given word may fall in. It always
				exists when we categorize a word, it may be empty though "".
		"oLim" and its friends describe whether we know that a category ends/starts
				on the token. (Both can occur at the same time, in fact).
				& 1 : starts, & 2 ends.
*/
const ST = 1;
const EN = 2;
const BOTH = 3;

const SEP = "!!"; // we insert this whereever we take stuff out from the query

class C {
	// there are a couple of categories of basic queries; we keep track of them
	public static $baseQueries = array(
			"year", "genre", "director", "actor", "rating", "rated", "length",
			"lOp", "sep", "connector" /* should these be here? : not queries, categories */
	);

	public static $logicOps = array(
			"and"=>array(),
			"or"=>array(),
			"not"=>array()
	);

	// we keep track of keywords, with associated information to them
	public static $keywords = array(
			"before"=> array("base"=>"year", "oLim"=>ST),
			"after"=> array("base"=>"year", "oLim"=>ST),
			"higher"=> array("base"=>"rating"),
			"lower"=> array("base"=>"rating"),
			"directed"=> array("base"=>"director"),
			"director"=> array("base"=>"director"),
			"rating"=> array("base"=>"rating", "oLim"=>ST),
			"rated"=> array("base"=>array("rating", "rated"), "oLim"=>ST)
	);

	public static $rateSites = array(
			"imdb"=> array(),
			"rotten"=> array(),
			"tomato"=> array(),
			"netflix"=> array()
	);

	public static $genres = array(
			"action", "adventure", "animation", "biography", "comedy", "crime", "documentary", "drama", "family", "fantasy", "film-noir", "history", "horror", "music", "musical", "mystery", "romance", "sci-fi", "sport", "thriller", "war", "western"
	);

	// this is just the ones people may search for
	public static $familyRating = array(
			"G", "PG", "PG-13", "14A", "NC-17", "18A", "R", "X" 
	);

	public static $regexRules = array(
			"/^\d{4}$/"=> array("base"=>"year", "oLim"=>EN),
			"/^\d{1,3}\%?$/"=> array("base"=>array("length","rating")),
			"/^\d{1}\.\d+$/"=> array("base"=>"rating")
	);

	public static $connectors = array(
			"by"=> array("directed"=>array("base"=>"director", "pLim"=>ST)), 
			"than"=> array("lower"=>"copy", "higher"=>"copy"),
			"with"=> array(
					"movies"=>array("base"=>"sep", "oLim" => BOTH ),
					"movie"=>array("base"=>"sep", "oLim" => BOTH)
					)
	);
}

echo "loaded all parsing constants\n";
echo "just a test.. " . count(C::$genres) . "\n";

?>

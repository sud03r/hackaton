<?php
/*
	This file / class takes care of "natural language" query parsing into
	the predefined set of base queries.
*/

class ParseQueries {

	// there are a couple of categories of basic queries; we keep track of them
	public static baseQueries = array(
		"year", "genre", "director", "actor", "rating", "rated", "length",
		"lOp", /* should these be here? : not queries, categories */
	);

	public static logicOps = array(
		"and"=>array(),
		"or"=>array(),
		"not"=>array()
	);

	// we keep track of keywords, with associated information to them
	public static $keywords = array(
		"before"=> array("base"=>"year", "oLim"=>"S"),
		"after"=> array("base"=>"year", "oLim"=>"S"),
		"higher"=> array("base"=>"rating"),
		"lower"=> array("base"=>"rating"),
		"directed"=> array("base"=>"director"),
		"director"=> array("base"=>"director"),
		"rating"=> array("base"=>"rating", "oLim"=>"S"),
		"rated"=> array("base"=>["rating", "rated"], "oLim"=>S)

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
		"G"=>array(),
		"PG"=array(),
		"PG-13"=>array(),
		"14A"=>array(),
		"NC-17"=>array(),
		"18A"=>array(),
		"R"=>array(),
		"X"=>array(), 
	); // TODO this is in the correct order... we WILL have to use this order

	public static $regexRules = array(
		'/^\d{4}$/'=> array("base"=>"year", "oLim"=>"E"),
		'/^\d{0,3}$/'=> array("base"=>["length","rating"]),
		'/^\d{0,1}\.\d+$/'=> array("base"=>"rating")
	);

	/*  This function is given a word and we categorize it by everything we can:
		- if we know it starts or ends its own expression ("oLim")
		- if we know it delimits the previous ("pLim") / next ("nLim") expression
		- if it infers the basequery type ("base")

		All this information is returned in a dictionary / assoc array.
		- The field names are shown in brackets
		- delimiting / starting and ending are signaled by "S" or "E"; may be both
		- note, if something delimits the current expression, we automatically
			can infor the prev or next one stopping / starting. NOT INCLUDED. ? TODO can just do it automatically at the end.
	 */
	public static categorize($word) {
		$word = strtolower($word);
		$toRet = array();
		// first check if it's one of the static keywords
		if (isset(ParseQueries::$keywords[$word]) {
			toRet = ParseQueries:$keywords[$word];
		} elseif (isset(ParseQueries::$rateSites[$word])) {
			toRet = ( array("base"=>"rating") + ParseQueries::$rateSites[$word]);	
		} elseif (isset(ParseQueries::$genres[$word]) {
			toRet = array("base"=>"genre", "oLim"="SE");
		} elseif (isset(ParseQueries::$logicOps[$word]) {
			toRet = array("base"=>"lOp", "pLim"=>"E", "nLim"=>"S");
		} elseif (isset(ParseQueries::$familyRating[$word]) {
			toRet = array("base"=>"rated", "oLim"=>"E");
		} else {
			// there's some other rules: digits
			foreach ($regexRules as $rule) {
				if (preg_match($pattern, $subject) == 1) {
					toRet = $regexRules[$rule];
				}
			}
		}
		// now we can automatically add previous and next delimiting properties
		if (isset($toRet["oLim"])) {
			switch ($toRet["oLim"]) {
				case "E":
					$toRet["nLim"] = "S";
					break;
				case "S":	
					$toRet["pLim"] = "E";
					break;
				case "SE":
					$toRet["nLim"] = "S";
					$toRet["pLim"] = "E";
					break;
				default:
					trigger_error("This is a big", E_WARNING);
			}
		}
		return $toRet;
	}
	
	/* TODO: things to remember for later
	- most of the time 'movie' / 'movies' can be removed
	- most of the time 'with' really means 'and'
	*/

	/* 
		Takes a query and returns a list of appropriate movies.
	 */
	public static parseQuery($query) {
		echo "Parsing '$query'\n";
	}
}




?>

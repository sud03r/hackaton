<?php
/*
	This file / class takes care of "natural language" query parsing into
	the predefined set of base queries.
*/

require_once(__DIR__ . "/queries.php");

class Pq {
    
    // TODO romantic should map to romance, and a lot of similar things

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

	// there are a couple of categories of basic queries; we keep track of them
	public static $baseQueries = array(
		"year", "genre", "director", "actor", "rating", "rated", "length",
		"lOp", /* should these be here? : not queries, categories */
	);

	public static $logicOps = array(
		"and"=>array(),
		"or"=>array(),
		"not"=>array()
	);

	// we keep track of keywords, with associated information to them
	public static $keywords = array(
		"before"=> array("base"=>"year", "oLim"=>Pq::ST),
		"after"=> array("base"=>"year", "oLim"=>Pq::ST),
		"higher"=> array("base"=>"rating"),
		"lower"=> array("base"=>"rating"),
		"directed"=> array("base"=>"director"),
		"director"=> array("base"=>"director"),
		"rating"=> array("base"=>"rating", "oLim"=>Pq::ST),
		"rated"=> array("base"=>array("rating", "rated"), "oLim"=>Pq::ST)
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
		"/^\d{4}$/"=> array("base"=>"year", "oLim"=>Pq::EN),
		"/^\d{0,3}\%?$/"=> array("base"=>array("length","rating")),
		"/^\d{0,1}\.\d+$/"=> array("base"=>"rating")
	);

	/*  This function is given a word and we categorize it by everything we can:
		- if we know it starts or ends its own expression ("oLim")
		- if we know it delimits the previous ("pLim") / next ("nLim") expression
		- if it infers the basequery type ("base")
	 */
	public static function categorize($word) {
		$word = strtolower($word);
		$toRet = array("base"=>"----", "oLim"=>0, "nLim"=>0, "pLim"=>0);
		// first check if it's one of the static keywords
		if (is_numeric($word)) {
			foreach (Pq::$regexRules as $rule=>$data) {
				if (preg_match($rule, $word) == 1) {
					Pq::updateData($toRet, $data);
				}
			}
		} else {
			if (isset(Pq::$keywords[$word])) {
				Pq::updateData($toRet, Pq::$keywords[$word]);
			} elseif (isset(Pq::$rateSites[$word])) {
				Pq::updateData($toRet, ( array("base"=>"rating") + Pq::$rateSites[$word]));	
			} elseif (in_array($word, Pq::$genres)) {
				Pq::updateData($toRet, array("base"=>"genre", "oLim"=>Pq::ST|Pq::EN));
			} elseif (isset(Pq::$logicOps[$word])) {
				Pq::updateData($toRet, array("base"=>"lOp",             "pLim"=>Pq::EN, "nLim"=>Pq::ST, "oLim"=>Pq::ST|Pq::EN));
			} elseif (in_array($word, Pq::$familyRating)) {
				Pq::updateData($toRet, array("base"=>"rated", "oLim"=>Pq::EN));
			} else {
				// there's some other rules: digits
				foreach (Pq::$regexRules as $rule=>$data) {
					if (preg_match($rule, $word) == 1) {
						Pq::updateData($toRet, $data);
					}
				}
			}
		}

		// now we can automatically add previous and next delimiting properties
		if (isset($toRet["oLim"])) {
			if (($toRet["oLim"] & Pq::ST) != 0) // start 
				$toRet["pLim"] = Pq::EN; //end
			if (($toRet["oLim"] & Pq::EN) != 0) // end 
				$toRet["nLim"] = Pq::ST; //end
		}
//		echo "$word\n";
//		var_dump($toRet);

		return $toRet;
	}
	
	/* TODO: things to remember for later
	- most of the time 'movie' / 'movies' can be removed
	- most of the time 'with' really means 'and'
	*/

	/* 
		Takes a query and returns a list of appropriate movies.
	 */
	public static function parseQuery($query) {
	
		// first thing we do is see if this is a movie title:
		$res = Query::byTitle($query);
		if (count($res) > 0) return $res;

        // rest of the stuff we compose / intersect movie lists
        $movieList = array();
        $constraints = array();
        
        // look for "movie[s] like _stuff_ [with/and]"
        $pattern = '/movies? like (.*) (with|and|\s*$) /i';
        if (preg_match($pattern, $query, $matches) == 1) {
            Pq::updateData($movieList, Query::bySimilarity($matches[1]));
            // adds to it -- also delete this section of the query
            $query = preg_replace($pattern, "", $query);
        }
		
		// -- we don't have a title;  analyze for tokens
		$queryC = strtolower($query); // TODO could find names by capital letter maybe
		$queryC = str_replace(" with ", " and ", $queryC);
        $queryC = str_replace(" but ", " and ", $queryC);
		$queryC = Pq::cleanQuery($queryC);
		
		Pq::updateData($constraints, Pq::findDateRangeConstraints($queryC));
		
		
        $words = explode(" ", $queryC);
        $numWords = count($words);
		$tokenLim = array();
		$base = array();
		for ($i = 0; $i < $numWords; $i += 1) {
			$res = Pq::categorize($words[$i]);
			if ($i > 0) {
				// see if we have a suggestion for the previous one
				if (($res["oLim"] & Pq::ST) != 0) {
					$tokenLim[count($tokenLim)-1] |= Pq::EN;
				}
				// see if the previous one had a suggestion for us
				if (($prevRes["nLim"] & Pq::EN) != 0)
					$res["oLim"] |= Pq::EN;
				// see if the previous one ended, we start 
                if (($prevRes["oLim"] & Pq::EN) != 0)
                    $res["oLim"] |= Pq::ST;
					
			} else {
                // first one always starts! :)
                $res["oLim"] |= Pq::ST;
			}
			array_push($base, $res["base"]);
			array_push($tokenLim, $res["oLim"]);

			$prevRes = $res;
		}
		// last one always finishes
		$tokenLim[$numWords-1] |= Pq::EN;

		// we represent it as a string now
		echo "$queryC\n";
		echo Pq::getStringRepOfCats($base, $tokenLim, $numWords);
		echo "\n\n";


        // TODO at this point we have a list of movies $movieList
        // and a list of constraints .. run them
	}

	/* ---------------------------------------------------- */
	/* Utilities...                                         */
	/* ---------------------------------------------------- */

	public static function updateData(&$arrayUpdate, $add) {
		$arrayUpdate = array_merge($arrayUpdate, $add);
	}

	public static function getStringRepOfCats($base, $tokenLim, $numWords) {
		$toRet = "";
		for ($i = 0; $i < $numWords; $i += 1) {
			$desc = "";
			if (($tokenLim[$i] & Pq::ST) != 0) $desc .= "[";
			$tmp = $base[$i];
			if (is_array($tmp))
				$desc .= implode("|", $tmp);
			else
				$desc .= $tmp;
			if (($tokenLim[$i] & Pq::EN) != 0) $desc .= "]";
			$toRet .= " $desc";
		}
		return $toRet;
	}
	
	public static function cleanQuery($queryC) {
        $toRemove = array(
            "movies " => "",
            " movies" => "",
            "movie " => "", 
            " movie" => "", 
            " the " => " ",
            " a " => " ", 
            " by " => " ", 
            " of " => " ", 
            " an " => " "
        );
        foreach ($toRemove as $pat => $gone) {
            $queryC = str_replace($pat, $gone, $queryC);
        }
        return $queryC;
	}
	
	// returns an array of constraint (related to date)
	// removes the found constraints from the argument
	public static function findDateRangeConstraints(&$query) {
        $patterns = array(
            "/before ('\d{2}|\d{4})/"=>0,
            "/after ('\d{2}|\d{4})/"=>1,
            "/between ('\d{2}|\d{4}) and ('\d{2}|\d{4})/"=>2,
            "/from ('\d{2}|\d{4}) to ('\d{2}|\d{4})/"=>2
        );
        $toRet = array();
        // look for these
        foreach ($patterns as $pattern => $id) {
            if (preg_match($pattern, $query, $matches) == 1) {
                $start = null;
                $end = null;
                $date1 = Pq::fixDate($matches[1]);
                switch ($id) {
                    case 0:
                        $end = $date1;
                        break;
                    case 1:
                        $start = $date1;
                        break;
                    case 2:
                        $start = $date1;
                        $end = Pq::fixDate($matches[2]);
                        break;
                }
                // now delete this stuff!
                $query = preg_replace($pattern, "", $query);
                
                array_push($toRet, new Constraint("dateRange", array("start"=>$start, "end"=>$end)));
            }
        }
        return $toRet;
	}
	
	// $date is either 1942 or '85 style... change the second into first
	public static function fixDate($date) {
        if ($date[0] == "'") {
            $num = substr($date, 1);
            if (intval($num)<20) // TODO this is horrible
                return (2000+intval($num));
            else
                return (1900+intval($num));
        }
        return intval($date);
	}
}



class Constraint {
    public static $types = array(
        "dateRange"=>array("start"=>null, "end"=>null),
        "dateExact"=>array("year"=>null),
        "length"=>array("min"=>null, "max"=>null),
        "genre"=>array("acceptable"=>array()),
        "famRating"=>array("lastAcceptable"=>array())
    );
    
    // returns a list of all ratings that are ok
    public static function getFamilyFriendly($lastAcceptable) {
        $okay = array($lastAcceptable);
        foreach (Pq::$familyRating as $rating) {
            if ($rating == $lastAcceptable) break;
            array_push($okay, $rating);
        }
    }
    
    // $type should be one of the listed ones, and
    // $data should be an array like what the $type is associated to
    public function __construct($type, $data) {
        if (!isset(Constraint::$types[$type]))
            trigger_error("Not a valid type of constraint", E_USER_ERROR);
        
        $this->type = $type;
        // $values = Constraint::$types[$type];
        $this->data = $data;
    }
}


?>

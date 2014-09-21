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
	
	const SEP = "!!"; // we insert this whereever we take stuff out from the query

	// there are a couple of categories of basic queries; we keep track of them
	public static $baseQueries = array(
		"year", "genre", "director", "actor", "rating", "rated", "length",
		"lOp", "sep" /* should these be here? : not queries, categories */
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
		"/^\d{1,3}\%?$/"=> array("base"=>array("length","rating")),
		"/^\d{1}\.\d+$/"=> array("base"=>"rating")
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
            if ($word == Pq::SEP) {
                Pq::updateData($toRet, array("base"=>"sep", "oLim"=>Pq::ST|Pq::EN));
            } elseif (isset(Pq::$keywords[$word])) {
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
        $pattern = '/(movies?)? like (.*)( with| and|\s*$)/i';
        if (preg_match($pattern, $query, $matches) == 1) {
            $myMovies = Query::byTitle($matches[1]);
            foreach ($myMovies as $movie){
                //collect the similar movies
                Pq::updateData($movieList, Query::bySimilarity($movie));
            }
            // also delete this section of the query
            $query = preg_replace($pattern, Pq::SEP, $query);
        }
		
		// -- we don't have a title;  analyze for tokens
		$queryC = strtolower($query); // TODO could find names by capital letter maybe
		$queryC = str_replace(" with ", " and ", $queryC);
        $queryC = str_replace(" but ", " and ", $queryC);
		$queryC = Pq::cleanQuery($queryC);
		
		Pq::updateData($constraints, Pq::findDateRangeConstraints($queryC));
		Pq::updateData($constraints, Pq::findLengthConstraints($queryC));
        Pq::updateData($constraints, Pq::findFamilyConstraints($queryC));
        Pq::updateData($constraints, Pq::findGenreConstraints($queryC));
        // clean some more again
		
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
		echo "\nconstraints:";
		foreach ($constraints as $const) {
            echo " $const->type";
		}
		echo "\n\n";

        return findMatches($movieList, $constraints);
	}

	// take a $movieList -- if not empty, we select things from there
	// if empty, we select things from the whole database
	// --------- always according to the constraints
	public static function findMatches($movieList, $constraints) {
        // if there is a movie list... for now we'll cheat and
        // just make an sql query that includes only those movies
        $query = "select * from movies where ";
        // now we specify each movie 
        $hadStuff = false;
        if (count($movieList) > 0) {
            $hadStuff = true;
            $query .= "(";
            foreach ($movieList as $movie) {
                $query .= "(name=" . $movie->mName . " and year=" . $movie->year . ") OR ";
            }
            $query .= "1=0 )"; // so this last condition is always false
        }
        if (count($constraints) > 0) {
            if (hadStuff) $query .= " AND ";
            foreach ($constraints as $con) {
                $query .= $con->getSQLCondition();
                $query .= " AND ";
            }
            $query .= "1=1;"; // so this last condition is always false
        }
        
        // now actually call the query
        $result = Db::query($query);
        $movies = array();
        for ($i = 0; $i < Db::getNumRows($result); $i++) {
            $row = Db::getNextRow($result);
            array_push($movies, Utils::createMovieFromDbRow($row));
        }
        return $movies;
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
            "/before ('\d{2}|\d{4})/i"=>0,
            "/since ('\d{2}|\d{4})/i"=>1,
            "/after ('\d{2}|\d{4})/i"=>1,
            "/between ('\d{2}|\d{4}) and ('\d{2}|\d{4})/i"=>2,
            "/from ('\d{2}|\d{4}) to ('\d{2}|\d{4})/i"=>2
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
                $query = preg_replace($pattern, Pq::SEP, $query);
                
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
	
	// returns an array of constraints 
	public static function findLengthConstraints(&$query) {
        $toRet = array();
        $pattern = "/(longer|shorter) than (\d+)\s*(\w+)\b/i";
        $min = null;
        $max = null;
        if (preg_match($pattern, $query, $matches) == 1) {
            // delete the matched thing 
            $query = preg_replace($pattern, Pq::SEP, $query);
 
            // we don't deal with hours
            if ($matches[3][0] != "m")
                return $toRet;
            
            $mins = intval($matches[2]);
            if ($matches[1][0] == "l")
                $min = $mins;
            else
                $max = $mins;
            
            array_push($toRet, new Constraint("length", array("min"=>$min, "max"=>$max)));
        }
        return $toRet;
	}
	
    // returns an array of constraints 
    public static function findFamilyConstraints(&$query) {
        $toRet = array();
        // take every word, see if there is an intersection
        $qWords = explode(" ", strtoupper($query));
        $selected = array_intersect(Pq::$familyRating, $qWords);
        foreach ($selected as $famRating) {
            // make constraint and delete
            array_push($toRet, new Constraint("famRating", array("acceptable"=>$famRating)));
            $query = str_replace(strtolower($famRating), Pq::SEP, $query);
        }
        return $toRet;
    }

    // returns an array of constraints:
    // we assume all the genre information is in the same location
    // -- can have or, or just list of things
    public static function findGenreConstraints(&$query) {
        $toRet = array();
        $qWords = explode(" ", $query);
        $numWords = count($qWords);
        
        $index = 0;
        $toDelete = array();
        while ($index < $numWords) {
            // TODO this will not be good logic-wise
            $negated = false;
            $appearing = array();
            // find the first word in the query that is a genre
            for (; $index < $numWords; $index += 1) {
                if (in_array($qWords[$index], Pq::$genres)) {
                    array_push($appearing, $qWords[$index]);
                    $qWords[$index] = Pq::SEP;
                    array_push($toDelete, $index);
                    
                    
                    // check if there was a "not" before it
                    if ($index > 0 && $qWords[$index-1] == "not") {
                        $negated = true;
                        $qWords[$index-1] = "";
                    }
                    while ($index+2 < $numWords && $qWords[$index+1] == "or" && in_array($qWords[$index+2], Pq::$genres)) {
                        array_push($appearing, $qWords[$index+2]);
                        $qWords[$index+1] = "";
                        $qWords[$index+2] = "";
                        $index += 2;
                    } 
                    // now we can create a constraint
                    $listN = "acceptable"; // default
                    if ($negated) $listN = "not_acceptable";
                    array_push($toRet, new Constraint("genre", array($listN=>$appearing)));
                    // now we can restart
                    break;
                }
            }
        }
        // all parsed --- recompile query and return
        $query = implode(" ", $qWords);
        return $toRet;
    }
    
}



class Constraint {
    public static $types = array(
        "dateRange"=>array("start"=>null, "end"=>null), // done
        "dateExact"=>array("year"=>null),
        "length"=>array("min"=>null, "max"=>null), // done
        "genre"=>array("acceptable"=>array(), "not_acceptable"=>array()), // done
        "famRating"=>array("lastAcceptable"=>array()) //done
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
    
    // returns in string the condition represented by this instance
    // eg.: (year > start and year < end)
    public function getSQLCondition() {
        $q = "(";
        switch ($this->type) {
            case "dateRange":
                $other = false;
                if (!is_null($this->data["start"])) {
                    $q .= "year >= " . $this->data["start"];
                    $other = true;
                }
                if (!is_null($this->data["end"])) {
                    if ($other) $q .= " AND ";
                    $q .= "year <= " . $this->data["end"];
                }
                break;
            case "length":
                $other = false;
                if (!is_null($this->data["min"])) {
                    $q .= "runtime >= " . $this->data["min"];
                    $other = true;
                }
                if (!is_null($this->data["max"])) {
                    if ($other) $q .= " AND ";
                    $q .= "runtime <= " . $this->data["max"];
                }
                break;
            case "famRating":
                $oklist = Constraint::getFamilyFriendly($this->data["lastAcceptable"]);
                foreach ($oklist as $ok) {
                    $q .= "fRating=" . $ok . " OR ";
                }
                $q .= "1=0";
                break;
            case "genre":
                // TODO at the moment only one is set any time... later make safer
                if (isset($this->data["acceptable"])) {
                    foreach ($this->data["acceptable"] as $okg) {
                        $q .= "genre LIKE '%" . $okg . "%' OR ";
                    }
                    $q .= "1=0";
                }
                
                if (isset($this->data["not_acceptable"])) {
                    foreach ($this->data["not_acceptable"] as $okg) {
                        $q .= "genre NOT LIKE '%" . $okg . "%' AND ";
                    }
                    $q .= "1=1";
                }
                break;
        }
        $q .= ")";
        return $q;
    }
    
    
}



?>

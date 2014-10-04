<?php
namespace parsing;
/*
	This file / class takes care of "natural language" query parsing into
	the predefined set of base queries.
*/

echo "opened up parseQueries.php\n";

require_once(__DIR__ . "/../queries.php");
require_once(__DIR__ . "/parseQueryConstants.php");

class ParseQuery {
    
	/*  This function is given a word and we categorize it by everything we can:
		- if we know it starts or ends its own expression ("oLim")
		- if we know it delimits the previous ("pLim") / next ("nLim") expression
		- if it infers the basequery type ("base")
	 */
	public static function categorize($word, $prevData) {
		echo "analyzing $word\n";
		$word = strtolower($word);
		$toRet = array("base"=>"----", "oLim"=>0, "nLim"=>0, "pLim"=>0);
		// first check if it's one of the static keywords
		if (is_numeric($word)) {
			foreach ($regexRules as $rule=>$data) {
				if (preg_match($rule, $word) == 1) {
					self::updateData($toRet, $data);
				}
			}
		} else {
            if ($word == SEP) {
                self::updateData($toRet, array("base"=>"sep", "oLim"=>ST|EN));
            } elseif (isset($keywords[$word])) {
				self::updateData($toRet, $keywords[$word]);
			} elseif (isset($rateSites[$word])) {
				self::updateData($toRet, ( array("base"=>"rating") + $rateSites[$word]));	
			} elseif (in_array($word, $genres)) {
				self::updateData($toRet, array("base"=>"genre", "oLim"=>ST|EN));
			} elseif (isset($logicOps[$word])) {
				self::updateData($toRet, array("base"=>"lOp", "pLim"=>EN, "nLim"=>ST, "oLim"=>ST|EN));
			} elseif (in_array($word, $familyRating)) {
				self::updateData($toRet, array("base"=>"rated", "oLim"=>EN));
			} elseif (isset($connectors[$word])) {
				self::updateData($toRet, array("base"=>"connector") + 
						($connectors[$word]) );
			} else {
				// there's some other rules: digits
				foreach ($regexRules as $rule=>$data) {
					if (preg_match($rule, $word) == 1) {
						self::updateData($toRet, $data);
					}
				}
			}
		}

		// now we can automatically add previous and next delimiting properties
		if (isset($toRet["oLim"])) {
			if (($toRet["oLim"] & ST) != 0) // start 
				$toRet["pLim"] = EN; //end
			if (($toRet["oLim"] & EN) != 0) // end 
				$toRet["nLim"] = ST; //end
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
	
	/* OVERKILL?
		// We change this so that we keep a TREE of possible parses.
		$root = new ParseNode($query, 1.0, array(), array());
	*/

		// first thing we do is see if this is a movie title:
		$movieListToUnion = \Query::byTitle($query);
		$results = self::getRelevanceByTitle($movieListToUnion, $query); // TODO test this

        return $results; // tODO testing

		// rest of the stuff we compose / intersect movie lists
        $movieListToFilter = array(); // QResult 's
        $constraints = array(); // Constraint 's
        
        // look for "movie[s] like _stuff_"; try with longest string possible,
		// getting shorter and shorter. ATM report only longest match
		$pattern = '/(movies?)? like (.*)$)/i'; // the pattern we look for
		$queryCopy = substr($query, 0); // make a copy
		while (strlen($queryCopy) > 0) {
			if (preg_match($pattern, $queryCopy, $matches) == 1) {
				// collect these movies, with relevances
				$likeTitles = Query::byTitle($matches[1]);
				if (count($likeTitles) === 0) continue;

				// not there may be multiple movies that matched... search through all
				$likeResults = self::getRelevanceByTitle($likeTitles, $matches[1]);
				foreach ($likeResults as $similarTo){
					// similarTo is (movie, relevance) pair
					$simMovies = Query::bySimilarity($similarTo->movie);
					// we use the same relevance for all similar movies
					self::updateData($movieListToFilter,
						QResult::getResults($simMovies, $similarTo->relevance) ) ;
				}
				// also delete this section of the query
				//         -- this is ok here, since we already "continue'd" if this code does not apply
				$query = preg_replace($pattern, SEP, $query);
				break;
			}
			$queryCopy = Utils::removeLastWord($queryCopy);
			// TODO test this. Also move a bunch of this code into other files it's getting annoying!!
		}
		
		// -- we don't have a title;  analyze for tokens
		$queryC = strtolower($query); // TODO could find names by capital letter maybe
//		$queryC = str_replace(" with ", " and ", $queryC);
        $queryC = str_replace(" but ", " and ", $queryC);
		$queryC = self::cleanQuery($queryC);
		
		self::updateData($constraints, Constraint::findDateRangeConstraints($queryC));
		self::updateData($constraints, Constraint::findLengthConstraints($queryC));
        self::updateData($constraints, Constraint::findFamilyConstraints($queryC));
        self::updateData($constraints, Constraint::findGenreConstraints($queryC));
        // clean some more again


        $words = explode(" ", $queryC);
        $numWords = count($words);
		$tokenLim = array();
		$base = array();
		$prevRes = array("base"=>"----", "oLim"=>0, "nLim"=>0, "pLim"=>0);
		for ($i = 0; $i < $numWords; $i += 1) {
			$res = self::categorize($words[$i], $prevRes);

			// There's special rules for non-first words (relying on the prev word)
			if ($i > 0) {
				/*   
					if the current word we got is a connector, we need to
					investigate it in relation to the previous word
				*/
				if ($res["base"] == "connector") {
					
				}

				/*
					we have some rules for how delimiters from previous / next 
					words affect each other; that's implemented here:
				*/
				// see if we have a suggestion for the previous one
				if (($res["oLim"] & ST) != 0) {
					$tokenLim[count($tokenLim)-1] |= EN;
				}
				// see if the previous one had a suggestion for us
				if (($prevRes["nLim"] & EN) != 0)
					$res["oLim"] |= EN;
				// see if the previous one ended, we start 
                if (($prevRes["oLim"] & EN) != 0)
                    $res["oLim"] |= ST;
			} else {
                // first one always starts! :)
                $res["oLim"] |= ST;
			}

			// ok we are all done dealing with this word
			array_push($base, $res["base"]);
			array_push($tokenLim, $res["oLim"]);

			$prevRes = $res;
		}
		// last one always finishes
		$tokenLim[$numWords-1] |= EN;

		// we represent it as a string now
		echo "$queryC\n";
		echo self::getStringRepOfCats($base, $tokenLim, $numWords);
		echo "\nconstraints:";
		foreach ($constraints as $const) {
            echo " $const->type";
		}
		echo "\n\n";

		// now find continuous sections
		self::updateData($constraints, self::ExamineSections($base, $tokenLim, $numWords));

		$filtered = self::findMatches($movieListToFilter, $constraints); // TODO changing type of filter
		return array_merge($movieListToUnion, $filtered);	
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
            if ($hadStuff) $query .= " AND ";
            foreach ($constraints as $con) {
                $query .= $con->getSQLCondition();
                $query .= " AND ";
            }
            $query .= "1=1;"; // so this last condition is always false
        }
       
//	   	echo "$query\n";
        // now actually call the query
        $result = Db::query($query);
        $movies = array();
        for ($i = 0; $i < Db::getNumRows($result); $i++) {
            $row = Db::getNextRow($result);
            array_push($movies, Utils::createMovieFromDbRow($row));
        }
        return $movies;
	}

	// this code looks at running sections in the code to attempt to 
	// find people's names
	public static function examineSections($base, $tokenLim, $numWords) {
		$continous = array(); // will consist of start-end pairs
		$inside = false;
		$start = -1;
		for ($i = 0; $i < $numWords; $i++) {
			if (!$inside) {
				if (($tokenLim[$i] & ST) == 1) {
					$start = $i;
				}
				// we should try right now if it ends (to catch one long)
			}
		}
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
			if (($tokenLim[$i] & ST) != 0) $desc .= "[";
			$tmp = $base[$i];
			if (is_array($tmp))
				$desc .= implode("|", $tmp);
			else
				$desc .= $tmp;
			if (($tokenLim[$i] & EN) != 0) $desc .= "]";
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
            " of " => " ", 
            " an " => " "
        );
        foreach ($toRemove as $pat => $gone) {
            $queryC = str_replace($pat, $gone, $queryC);
        }
        return $queryC;
	}
	
	/*
		Takes a list of movies and the title of the movie
		and calculates relevances by how much of the $title
		actually appears in the title of the movies listed.
		(relevance will range between 0.5 and 1)		

		Returns a list of QResult 's.
	*/
	public static function getRelevanceByTitle($movieList, $title) {
		$toRet = array();
		foreach ($movieList as $movie) {
			$total = str_word_count($movie->mName);
			$mine = str_word_count($title);
			array_push($toRet, new QResult($movie, ($mine/$total)*0.5+0.5) );
		}
		return $toRet;
	}
}



class QResult {
	/* Hold a movie matching the query, and the associated relevance. */
	public function __construct($movie, $relevance) {
		$this->movie = $movie;
		$this->relevance = $relevance;
	}

	public static function getResults($movieList, $relevance) {
		$ret = array();
		foreach ($movieList as $movie) {
			array_push($ret, new QResult($movie, $relevance));
		}
	}
}

?>

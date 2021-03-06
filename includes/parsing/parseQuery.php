<?php
namespace parsing;
/*
    This file / class takes care of "natural language" query parsing into
    the predefined set of base queries.
*/

// TODO all the "self::" should probably be "static::", just in case this class
// is subclassed

require_once(__DIR__ . "/../env.php");
if ($DEBUG) echo "opened up parseQueries.php\n";
require_once(__DIR__ . "/../queries.php");
require_once(__DIR__ . "/../utils.php");
require_once(__DIR__ . "/parseQueryConstants.php");
require_once(__DIR__ . "/constraint.php");

use \Utils, \Db, \Query;

if ($DEGUB) echo "Loaded everything\n";

class ParseQuery {
    
    /*  This function is given a word and we categorize it by everything we can:
        - if we know it starts or ends its own expression ("oLim")
        - if we know it delimits the previous ("pLim") / next ("nLim") expression
        - if it infers the basequery type ("base")
     */
    public static function categorize($word, $prevData) {
        global $DEBUG;
        if ($DEBUG) {
            if ($word === " ")
                trigger_error("This is not optimal..", E_USER_WARNING);
        }
        $word = strtolower($word);
        $toRet = array("base"=>"----", "oLim"=>0, "nLim"=>0, "pLim"=>0);
        // first check if it's one of the static keywords
        if (is_numeric($word)) {
            foreach (C::$regexRules as $rule => $data) {
                if (preg_match($rule, $word) == 1) {
                    self::updateData($toRet, $data);
                }
            }
        } else {
            if ($word == SEP) {
                self::updateData($toRet, array("base"=>"sep", "oLim"=>ST|EN));
            } elseif (isset(C::$keywords[$word])) {
                self::updateData($toRet, C::$keywords[$word]);
            } elseif (isset(C::$rateSites[$word])) {
                self::updateData($toRet, ( array("base"=>"rating") + C::$rateSites[$word]));    
            } elseif (in_array($word, C::$genres)) {
                self::updateData($toRet, array("base"=>"genre", "oLim"=>ST|EN));
            } elseif (isset(C::$logicOps[$word])) {
                self::updateData($toRet, array("base"=>"lOp", "pLim"=>EN, "nLim"=>ST, "oLim"=>ST|EN));
            } elseif (in_array($word, C::$familyRating)) {
                self::updateData($toRet, array("base"=>"rated", "oLim"=>EN));
            } elseif (isset(C::$connectors[$word])) {
                self::updateData($toRet, array("base"=>"connector") + 
                        (C::$connectors[$word]) );
            } else {
                // there's some other rules: digits
                foreach (C::$regexRules as $rule => $data) {
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
//      echo "$word\n";
//      var_dump($toRet);

        return $toRet;
    }
    
    /* TODO: things to remember for later
    - most of the time 'movie' / 'movies' can be removed
    - most of the time 'with' really means 'and'
    */

    /* 
        Takes a query and returns a list of appropriate movies. 
        If an empty query is sent, we return all movies.

        Paging is supported, $pageSize and $pageNum are fairly descriptive,
        except that $pageSize=-1 to return all results.
     */
    public static function parseQuery($query, $pageSize=-1, $pageNum=0) {
        global $DEBUG;
    /* OVERKILL?
        // We change this so that we keep a TREE of possible parses.
        $root = new ParseNode($query, 1.0, array(), array());
    */

        $query = trim($query);	
        
        // first thing we do is see if this is a movie title:
        $movieList = \Query::byTitle($query);
        $resultsToUnion = self::getRelevanceByTitle($movieList, $query); // TODO test this

        // rest of the stuff we compose / intersect movie lists
        $movieListToFilter = array(); // QResult 's
        $constraints = array(); // Constraint 's


        # TODO commented out 'cuz I can't connect to rotten tomatoes
#        self::updateData($movieListToFilter, self::trySimilarMovies($query));

    
        // -- we don't have a title;  analyze for tokens
        $queryC = strtolower($query); // TODO could find names by capital letter maybe
//      $queryC = str_replace(" with ", " and ", $queryC);
        $queryC = str_replace(" but ", " and ", $queryC);
        
        // first turn emotions to genres (this should be done before cleaning)
        self::updateData($constraints, self::emotionsToGenres($queryC));
        self::applyGenreAliases($queryC);

        $queryC = self::cleanQuery($queryC);
        $queryC = addslashes($queryC);
        
        self::updateData($constraints, Constraint::findDateRangeConstraints($queryC));
        self::updateData($constraints, Constraint::findLengthConstraints($queryC));
        self::updateData($constraints, Constraint::findFamilyConstraints($queryC));
        self::updateData($constraints, Constraint::findGenreConstraints($queryC));
        // TODO these two should be moved down for the more intense type of analysis.
//        self::updateData($constraints, Constraint::findDirectorConstraints($queryC));
//        self::updateData($constraints, Constraint::findActorConstraints($queryC));
        
        // clean some more again ?
        
        list($words, $tokenLim, $categories) = self::tokenize($queryC);
        $numWords = count($words);

        if ($DEBUG) {
            // we represent it as a string now
            echo "$queryC\n";
            echo self::getStringRepOfCats($categories, $tokenLim, $numWords);
            echo "\nconstraints:\n";
            foreach ($constraints as $const) {
                echo "- $const->type: " . $const->getSQLCondition() . "\n";
            }
        }

        // now find continuous sections
        self::updateData($constraints, self::examineSections($words, $tokenLim, $categories));
        
        $filteredResults = self::findMovieByConstraint($movieListToFilter, $constraints); // TODO changing type of filter
        $results = array_merge($resultsToUnion, $filteredResults);

        return self::getPage($results, $pageSize, $pageNum);
    }

   public static function getPage($results, $pageSize, $pageNum) { 
        // No paging when we havent asked for it, ok?
        if ($pageSize == -1)
            return $results;

        // Now we will check to see if $results satisfy paging requirements.
        $numEntries = count($results);
        if ($numEntries <= $pageSize) {
            return $results;
        }
        else {
            $startIdx = $pageNum * $pageSize;
            // Takes care of the case when the slice is shorter than pagesize
            return array_slice($results, $startIdx, $pageSize);
        }
    }

    /** Try to tokenize the query, finding delimiters and categories.

        Returns the array (number of entries, limit info, categories).
    */
    public static function tokenize($queryC) {
#       $words = preg_split('/\s+/', $queryC); // TODO decide which one to use
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
                    // TODO still to do.    
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
        
        return array($words, $tokenLim, $base);
    }


    /*
    This function applies a set of search constraints to a set of movies.

    If $movieList is non-empty, we search in the movies specified in it,
    otherwise we search in the set of all movies in our database / on netflix.
    The constraints are contained in $constraints.

    Special case: if there are no movies to filter, or constraints given,
                    we return an empty array.
    */
    public static function findMovieByConstraint($resultList, $constraints) {
        // if there is a movie list... for now we'll cheat and
        // just make an sql query that includes only those movies
        $query = "select * from movies where ";
        // now we specify each movie 
        $hadStuff = false;
        if (count($movieList) > 0) {
            $hadStuff = true;
            $query .= "(";
            foreach ($resultList as $result) {
                $movie = $result->movie;
                $query .= "(name=" . $movie->mName . " and year=" . $movie->year . ") OR ";
            }
            $query .= SQL_FALSE . " )";
        }
        $numConstraints = count($constraints);
        if ($numConstraints > 0) {
            if ($hadStuff) $query .= " AND ";
            foreach ($constraints as $con) {
                $query .= $con->getSQLCondition();
                $query .= " AND ";
            }
            $query .= SQL_TRUE;
        } else {
            // if there's no movies to pick from
            // and no contrainst to apply,
            // ---> just return an empty list
            if (!$hadStuff) return array(); // to make valid syntax.
        }
        $query .= ";";
        if ($DEBUG) echo "$query\n";
        
        // now actually call the query
        $moviesWithRelevances = array();
        $dbResult = \Db::query($query);
        for ($i = 0; $i < \Db::getNumRows($dbResult); $i++) { // TODO temporary
            $row = \Db::getNextRow($dbResult);
            $movie = \Utils::createMovieFromDbRow($row);
            array_push($moviesWithRelevances, 
                        new QResult($movie, min(0.0, 1.0/(1.0+numConstraints) )) );
        }
        return $moviesWithRelevances;
    }

    
    /* Returns constaints based on tokenization (attempt).

    Infers additional constrains from the non-complete tokenization.
    */
    public static function examineSections($base, $tokenLim, $numWords) {
        return array(); // TODO working on this ... infinite loop below

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

    /*
    Look for "movie[s] like _stuff_"; try with longest _stuff_ possible,
    getting shorter and shorter. ATM report only longest match.

    Returns a list (array) of matching QResults.
    */
    public static function trySimilarMovies(&$query) {
        $movieListToReturn = array();
        $pattern = '/(movies?)? like (.*)$/i'; // the pattern we look for
        $queryCopy = substr($query, 0); // make a copy
        $matches = array();
        
        for(;strlen($queryCopy) > 0; $queryCopy = \Utils::removeLastWord($queryCopy)) {
            if (preg_match($pattern, $queryCopy, $matches) == 1) {
                // collect these movies, with relevances
                $likeTitles = \Query::byTitle($matches[2]);
                if (count($likeTitles) === 0) continue;
                
                // not there may be multiple movies that matched... search through all
                $likeResults = self::getRelevanceByTitle($likeTitles, $matches[2]);
                
                foreach ($likeResults as $similarTo){
                    // similarTo is (movie, relevance) pair
                    $simMovies = Query::bySimilarity($similarTo->movie);
                    // we use the same relevance for all similar movies
                    self::updateData($movieListToReturn,
                        QResult::getResults($simMovies, $similarTo->relevance) ) ;
                }
                // also delete this section of the query
                //         -- this is ok here, since we already "continue'd" if this code does not apply
                $query = preg_replace($pattern, SEP, $query);
                break;
            }
        }
        
        return $movieListToReturn;
    }
    
    /* Applies the replacement rules as defined in the parseQueryConstraint
       file, under C::$emotions. Each rule matches some text (emotion) and
       maps to an array of genres (all OR'ed together).

       In fact, $emotionRegexPattern can and should be used to find emotions
       in queries.

       Returns a list of constraints.
     */
    public static function emotionsToGenres(&$query) {
        global $DEBUG;
        $constraints = array();
//        if ($DEBUG) echo "== query start: $query \n";
        
        while (preg_match(C::$emotionRegexPattern, $query, $matches)) {
            $selEmotion = $matches[1];
//            if ($DEBUG) echo "   found emotion: $selEmotion\n";
            $query = str_replace($selEmotion, "", $query); 
            $genreData = array(
                    "acceptable"=> C::$emotions[$selEmotion]
                    );

            array_push($constraints, new Constraint("genre", $genreData));
        }
//        if ($DEBUG) echo "== query end: $query \n"; 
        return $constraints;
    }

    /* There are certain variations in genres that we need to account for.

       These are recorded in C::$genreAliases.
     */
    public static function applyGenreAliases(&$query) {
        foreach (C::$genreAliases as $alias=>$genre) {
            $query = str_replace($alias, $genre, $query);
        }
    }


    /* ---------------------------------------------------- */
    /* Utilities...                                         */
    /* ---------------------------------------------------- */

    public static function updateData(&$arrayUpdate, $add) {
        global $DEBUG;  
        if ($DEBUG) {
            if (!is_array($add)) {
                debug_print_backtrace();
                echo "Second arg: \n";
                var_dump($add);
            }
        }
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
            array_push($toRet, new QResult($movie, (1.0*$mine/$total)*0.5+0.5) );
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
        return $ret;
    }
}

?>

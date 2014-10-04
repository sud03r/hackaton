<?php
namespace parsing;
/**
	This file contains the constraint class and methods related to it.

	The constraints are extracted from the queries we get, stored, and later
	can be translated into SQL constraints, or plain text, etc.
*/

echo "Going into constraint.php\n";

require_once(__DIR__ . "/parseQueryConstants.php" );
require_once(__DIR__ . "/../utils.php");
use \Utils;   // this is so that we can access it

class Constraint {
    public static $types = array(
        "dateRange"=>array("start"=>null, "end"=>null), // done
        "dateExact"=>array("year"=>null),
        "length"=>array("min"=>null, "max"=>null), // done
        "genre"=>array("acceptable"=>array(), "not_acceptable"=>array()), // done
        "famRating"=>array("lastAcceptable"=>array()), //done
                "director"=>array("name"=>null)
    );
    
   
    // $type should be one of the listed ones, and
    // $data should be an array like what the $type is associated to
    public function __construct($type, $data) {
        if (!isset(Constraint::$types[$type]))
            trigger_error("Not a valid type of constraint", E_USER_ERROR);
        
        $this->type = $type;
        // $values = Constraint::$types[$type];
        $this->data = $data;
    }


	// returns an array of constraint (related to date)
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
                $date1 = \Utils::fixYearFormat($matches[1]);
                switch ($id) {
                    case 0:
                        $end = $date1;
                        break;
                    case 1:
                        $start = $date1;
                        break;
                    case 2:
                        $start = $date1;
                        $end = \Utils::fixYearFormat($matches[2]);
                        break;
                }
                // now delete this stuff!
                $query = preg_replace($pattern, SEP, $query);
                
                array_push($toRet, new Constraint("dateRange", array("start"=>$start, "end"=>$end)));
            }
        }
        return $toRet;
 	}


    // returns an array of constraints 
    public static function findLengthConstraints(&$query) {
        $toRet = array();
        $pattern = "/(longer|shorter) than (\d+)\s*(\w+)\b/i";
        $min = null;
        $max = null;
        if (preg_match($pattern, $query, $matches) == 1) {
            // delete the matched thing 
            $query = preg_replace($pattern, SEP, $query);
 
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
        $selected = array_intersect(C::$familyRating, $qWords);
        foreach ($selected as $famRating) {
            // make constraint and delete
            array_push($toRet, new Constraint("famRating", array("lastAcceptable"=>$famRating)));
                        $pattern = "/(rated )?\b$famRating\b/i";
            $query = preg_replace($pattern, SEP, $query);
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
                if (in_array($qWords[$index], C::$genres)) {
                    array_push($appearing, $qWords[$index]);
                    $qWords[$index] = SEP;
                    array_push($toDelete, $index);
                    
                    
                    // check if there was a "not" before it
                    if ($index > 0 && $qWords[$index-1] == "not") {
                        $negated = true;
                        $qWords[$index-1] = "";
                    }
                    while ($index+2 < $numWords && $qWords[$index+1] == "or" && in_array($qWords[$index+2], C::$genres)) {
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

    // returns a list of all ratings that are ok
    public static function getFamilyFriendly($lastAcceptable) {
                $okay = array($lastAcceptable);
        foreach (C::$familyRating as $rating) {
            if ($rating === $lastAcceptable) break;
            array_push($okay, $rating);
        }
                return $okay;
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
                        case "director":
                                $q .= "directors LIKE '%" . $this->data["name"] . "%'";
                                break;
        }
        $q .= ")";
        return $q;
    }    
}

echo "Read through constraint.php\n";


?>

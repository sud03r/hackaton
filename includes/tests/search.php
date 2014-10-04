<?php
/*
	This file does some tests on the search functionality.
*/

trigger_error("Removed functionality.", E_USER_ERROR);

require_once(__DIR__ . "/../search.php");
echo "-- First tests check the use of the Netflix API ---------------------------------\n";

// assert("false");

echo "[one result]\n";
$moviesTitan = basicSearch("title=Attack%20on%20titan");
assert(count($moviesTitan) === 1);
assert($moviesTitan[0]->mName === "Attack on Titan");

echo "[multiple results]\n";
$moviesTarantino = basicSearch("director=Quentin%20Tarantino");
assert(count($moviesTarantino) === 6);

echo "[query with no results]\n";
$moviesBad = basicSearch("title=asdfjifis");
assert(count($moviesBad) === 0);

echo "[conjuction query]\n";
$moviesConj = basicSearch("title=The%20Boondocks&year=2005");
assert($moviesConj[0]->mName === "The Boondocks");


echo "-- Next we check the addition info from imdb ------------------------------------\n";
echo "[1]\n";
assert($moviesTitan[0]->plot === "2000 years from now, humans are nearly exterminated by titans. Titans are typically several stories tall, seem to have no intelligence, devour human beings and, worst of all, seem to do it ...");
echo "[2]\n";
assert($moviesConj[0]->genres == array("Animation", "Action", "Comedy"));


echo "-- Next we check the addition info from rotten tomatoes -------------------------\n";
echo "[1]\n";
assert(!isset($moviesTitan[0]->rating['critics_rating']));
echo "[2]\n";
assert($moviesTarantino[0]->rating['critics_score'] == 84);



echo "\n\nFinished running all tests.\n";

?>

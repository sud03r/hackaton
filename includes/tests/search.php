<?php
/*
	This file does some tests on the search functionality.
*/
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
var_dump($moviesTitan);
assert($moviesTitan[0]->plot === "After his hometown is destroyed, young Eren Jaegar vows to cleanse the earth of the giant humanoid Titans that have brought humanity to the brink of extinction.");

echo "[2]\n";
assert($moviesConj[0]->genres == array("Animation", "Action", "Comedy"));



echo "\n\nFinished running all tests.\n";

?>

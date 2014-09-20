<?php
/*
	This file does some tests on the search functionality.
*/
require_once(__DIR__ . "/../search.php");
echo "Some basic query tests --------------------------------------\n";


assert("false");

echo "\n[one result] ";
$moviesTitan = basicSearch("title=Attack%20on%20titan");
assert(count($moviesTitan) === 1);
assert($moviesTitan[0]->mName === "Attack on Titan");

echo "\n[multiple results] ";
$moviesTarantino = basicSearch("director=Quentin%20Tarantino");
assert(count($moviesTarantino) === 6);

echo "\n[query with no results] ";
$moviesBad = basicSearch("title=asdfjifis");
// TODO add assertion

echo "\n[conjuction query] ";
$moviesConj = basicSearch("title=The%20Boondocks&year=2005");
assert($moviesConj[0]->mName === "The Boondocks");


?>

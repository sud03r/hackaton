<?php
/*
	This file tests basic rotten tomato functionality.
*/
require_once(__DIR__ . "/../search.php");
require_once(__DIR__ . "/../movie.php");

echo "-- Some tests on the methods accessing rotten tomatoes API --------------\n";

$movieGood = new Movie("Ghostbusters", '6.5', '541018', '1984');
$movieBad = new Movie("Attack on Titan", 5, 70299043, 2013);

$data = getRottenDataByName($movieBad->mName);
assert(count($data) === 0);

$data = getRottenDataByName($movieGood->mName);
assert(count($data) > 0);

$similarTitles = array("fakeEntry" => 42343);
$ourMovie = selectOurMovieFromRotten($data, $movieGood, $similarTitles);
assert($ourMovie['year'] == $movieGood->year);
$movieGood->populateFromRottenTomatoes($ourMovie["ratings"]);
$movieGood->addSimilarTitles($similarTitles);
assert(count($movieGood->otherTitles) == count($data)); # this is because we have a fake one in otherTitles!!
assert($movieGood->rating['audience_score'] == 88);


echo "Finished all tests.\n"

?>

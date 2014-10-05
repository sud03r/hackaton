<?php

require_once(dirname(__FILE__) . "/../../includes/utils.php");

function findMovieInArray($movieArr, $name, $year) {
	foreach ($movieArr as $movie) {
		$description = $movie["description"];
		$year = str_replace('"', "", $year);
		if (strpos($description, $year) !== false) {
			return $movie["id"];
		}
	}
	return "";
}

$netflixData = fopen("../movieData.txt", "r") or die("Unable to open file!");
#$netflixData = fopen("try.data", "r") or die("Unable to open file!");

while (($fLine = fgets($netflixData)) !== false) {
	$line = explode('",', $fLine);
	$name = str_replace('"', "", $line[2]);
	$year = str_replace('"', "", $line[3]);
	
	# first try to see if there is an exact match
	$result = json_decode(Utils::getWebData("http://www.imdb.com/xml/find?json=1&nr=1&q=".$name), true);
	$popularMatch = (isset($result["title_popular"]) ? $result["title_popular"] : array());
	$exactMatch = (isset($result["title_exact"]) ? $result["title_exact"] : array());
	$subsMatch = (isset($result["title_substring"]) ? $result["title_substring"] : array());
	$aproxMatch = (isset($result["title_approx"]) ? $result["title_approx"] : array());

	$title = findMovieInArray($popularMatch, $name, $year);
	if (!$title)
		$title = findMovieInArray($exactMatch, $name, $year);
	if (!$title)
		$title = findMovieInArray($subsMatch, $name, $year);
	if (!$title)
		$title = findMovieInArray($aproxMatch, $name, $year);
	
	# if we still can't find it, give up, use the name!
	$url = "http://www.omdbapi.com/?";
	if ($title)
		$url = $url."i=$title";
	else
		$url = $url."&t=$name";
	echo Utils::getWebData($url);
	echo "\n";
}

fclose($netflixData);
?>

<?php

/**
	Endpoint. Returns results that match the query passed to the file with $_GET.

	The returned results are in json format, and have the following structure:
		["success" = <true/false>, "data"=<array of results>],
	where the array of results are generated from the QResult class found in /parsing/parseQuery.php,
	and at the moment of writing this documentation will result in an array like
		["movie"=<movie class>, "relevance"=<double between 0 and 1>]
*/

header('Content-Type: application/json');
require_once(__DIR__ . "/../includes/parsing/parseQuery.php");
use \parsing\ParseQuery as Pq;


/* if started from commandline, wrap parameters to $_POST and $_GET */
if (!isset($_SERVER["HTTP_HOST"])) {
	parse_str($argv[1], $_GET);
#	parse_str($argv[1], $_POST);
}

$response = array("success"=>false);
if(isset($_GET['q']))
{
    $query = $_GET['q'];
#	echo "$query\n";
	try {
		# Check if the front-end needs a specific page. If so, return the appropriate chunk
		$pageSize = -1;
		$pageNum = 0;
		if (isset($_GET['p']) && isset($_GET['s'])) {
			$pageSize = $_GET['s'];
			$pageNum = $_GET['p'];
		}
		
		$movies = Pq::parseQuery($query, $pageSize, $pageNum);
#		print_r($movies);
		$response["success"] = true;
		$response["data"] = $movies;
	} catch (Exception $e) {
		// TODO this doesn't seem right..
	}
}

echo json_encode($response);
?>

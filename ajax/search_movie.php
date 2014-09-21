<?php
// Call this file if you want to do a basic search -- FOR TESTING
// For the syntax see ../includes/search.php
header('Content-Type: application/json');
require_once(__DIR__ . "/../includes/search.php");
require_once(__DIR__ . "/../includes/parseQuery.php");


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
		$movies = Pq::parseQuery($query);
#		print_r($movies);
		$response["success"] = true;
		$response["data"] = $movies;
	} catch (Exception $e) {
		// pass
	}
}

echo json_encode($response);
?>

<?php
// Call this file if you want to do a basic search -- FOR TESTING
// For the syntax see ../includes/search.php
header('Content-Type: application/json');
require_once(__DIR__ . "/../includes/search.php");

/* if started from commandline, wrap parameters to $_POST and $_GET */
if (!isset($_SERVER["HTTP_HOST"])) {
	parse_str($argv[1], $_GET);
#	parse_str($argv[1], $_POST);
}

$response = array("success"=>false);
if(isset($_GET['q']))
{
    $query = $_GET['q'];
	echo "The query is '$query'\n";
	$movies = basicSearch("Fake Data");
	$response["success"] = true;
	$response["data"] = $movies;
}

echo json_encode($response);
?>

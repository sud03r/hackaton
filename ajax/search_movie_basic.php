<?php
// Call this file if you want to do a basic search -- FOR TESTING
// For the syntax see ../includes/search.php
header('Content-Type: application/json');
require_once("../includes/search.php");

# TODO
$movies = basicSearch( THIS IS WHERE QUERY WILL GO )

echo json_encode(movies);
?>

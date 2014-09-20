<?php
/*
	This file does some tests on the search functionality.
*/
require_once("../search.php");

echo "The first search.. [one result]\n";
basicSearch("title=Attack%20on%20titan");

echo "\nThe second search.. [array]\n";
basicSearch("director=Quentin%20Tarantino");



?>

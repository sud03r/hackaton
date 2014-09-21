<?php

require_once(__DIR__ . "/../parseQuery.php");

$queries = array(
	"Scary movies rated higher than 7 on imdb"=>"mhm",
	"movies directed by Tom Hanks with rating higher than 61% on rotten tomato"=>"this should check both critic and audiance and if matches either, report",
	"scary movie"=>"return both the comedy and thrillers/horrors",
	"before sunset"=>"return the movie",
	"movies like the expandables"=>"pretty clear..",
	"Movies with Ryan Gosling before 2000"=>"claer...",
	"quentin tarantino movies"=>"ja..",
	"quenton tarantino movies"=>"typo in his name",
	"Hitchhiker's Guide to the Galaxy"=>"missing 'the' from the beginning",
	"The Princess Bride"=>"easy",
	"tarantino 2009"=>"ahh"
);

$iter = 0;
foreach ($queries as $query => $descr) {
	if ($iter > 3) break;
	$iter += 1;
	echo "-- query: $query\n";
	echo "          $descr\n";
	$movieMatching = Pq::parseQuery($query);
	# TODO var_dump($movieMatching);
	echo "\n";
}



?>
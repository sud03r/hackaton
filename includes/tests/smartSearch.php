<?php
/*
	This file does some tests on the search functionality.
*/
require_once(dirname(__FILE__). "/../smartSearch.php");


$movieList = array();

$exampleJsonData = '{"Title":"Forrest Gump","Year":"1994","Rated":"PG-13","Released":"06 Jul 1994","Runtime":"142 min","Genre":"Drama, Romance","Director":"Robert Zemeckis","Writer":"Winston Groom (novel), Eric Roth (screenplay)","Actors":"Tom Hanks, Rebecca Williams, Sally Field, Michael Conner Humphreys","Plot":"Forrest Gump, while not intelligent, has accidentally been present at many historic moments, but his true love, Jenny Curran, eludes him.","Language":"English","Country":"USA","Awards":"Won 6 Oscars. Another 42 wins & 53 nominations.","Poster":"http://ia.media-imdb.com/images/M/MV5BMTQwMTA5MzI1MF5BMl5BanBnXkFtZTcwMzY5Mzg3OA@@._V1_SX300.jpg","Metascore":"82","imdbRating":"8.8","imdbVotes":"875,526","imdbID":"tt0109830","Type":"movie","Response":"True"}';
$movie = new Movie("Forrest Gump", '9', '12345', '1994');
$movie->populateFromIMDB($exampleJsonData);
array_push($movieList, $movie);

$exampleJsonData = '{"Title":"Sleepless in Seattle","Year":"1993","Rated":"PG","Released":"25 Jun 1993","Runtime":"105 min","Genre":"Comedy, Drama, Romance","Director":"Nora Ephron","Writer":"Jeff Arch (story), Nora Ephron (screenplay), David S. Ward (screenplay), Jeff Arch (screenplay)","Actors":"Tom Hanks, Ross Malinger, Rita Wilson, Victor Garber","Plot":"A recently widowed man\'s son calls a radio talk-show in an attempt to find his father a partner.","Language":"English","Country":"USA","Awards":"Nominated for 2 Oscars. Another 4 wins & 10 nominations.","Poster":"http://ia.media-imdb.com/images/M/MV5BNzc0MDkwNjI0NF5BMl5BanBnXkFtZTgwMTY1MjEyMDE@._V1_SX300.jpg","Metascore":"72","imdbRating":"6.8","imdbVotes":"102,376","imdbID":"tt0108160","Type":"movie","Response":"True"}';
$movie = new Movie("Sleepless in Seattle", '9', '12345', '1994');
$movie->populateFromIMDB($exampleJsonData);
array_push($movieList, $movie);

$filtered = SmartSearch::filterByGenre($movieList, "Romance");
#foreach ($filtered as $movie) {
#	echo "$movie\n";
#}*/

?>

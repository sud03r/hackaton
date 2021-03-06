<?php
/*
	This file does some tests on the movie functionality.
*/

require_once(dirname(__FILE__). "/../movie.php");

$exampleJsonData = '{"Title":"Forrest Gump","Year":"1994","Rated":"PG-13","Released":"06 Jul 1994","Runtime":"142 min","Genre":"Drama, Romance","Director":"Robert Zemeckis","Writer":"Winston Groom (novel), Eric Roth (screenplay)","Actors":"Tom Hanks, Rebecca Williams, Sally Field, Michael Conner Humphreys","Plot":"Forrest Gump, while not intelligent, has accidentally been present at many historic moments, but his true love, Jenny Curran, eludes him.","Language":"English","Country":"USA","Awards":"Won 6 Oscars. Another 42 wins & 53 nominations.","Poster":"http://ia.media-imdb.com/images/M/MV5BMTQwMTA5MzI1MF5BMl5BanBnXkFtZTcwMzY5Mzg3OA@@._V1_SX300.jpg","Metascore":"82","imdbRating":"8.8","imdbVotes":"875,526","imdbID":"tt0109830","Type":"movie","Response":"True"}';

$movie = new Movie("Forrest Gump", 'rat=4', 'id=12345', '1994', null);
$movie->populateFromIMDB($exampleJsonData);
if ($movie->rating['imdb'] != 8.8 || $movie->mtype !== "movie") {
	trigger_error("There must be an issue with parsing imdb data", E_USER_ERROR); 
}
var_dump($movie);

?>

<?php
/* 
	this file creates a long list of movies so that we don't need to run the basic queries anymore for testing.
*/

require_once(__DIR__ . "/../movie.php");


/* Pretends to do a basic search. Ignores search string, always returns the same thing. */
function getMovies() {

$data = <<<EOT
{
	"0":{
		"args":["Forrest Gump", 9, 12345, 1994], 
		"data":{"Title":"Forrest Gump","Year":"1994","Rated":"PG-13","Released":"06 Jul 1994","Runtime":"142 min","Genre":"Drama, Romance","Director":"Robert Zemeckis","Writer":"Winston Groom (novel), Eric Roth (screenplay)","Actors":"Tom Hanks, Rebecca Williams, Sally Field, Michael Conner Humphreys","Plot":"Forrest Gump, while not intelligent, has accidentally been present at many historic moments, but his true love, Jenny Curran, eludes him.","Language":"English","Country":"USA","Awards":"Won 6 Oscars. Another 42 wins & 53 nominations.","Poster":"http://ia.media-imdb.com/images/M/MV5BMTQwMTA5MzI1MF5BMl5BanBnXkFtZTcwMzY5Mzg3OA@@._V1_SX300.jpg","Metascore":"82","imdbRating":"8.8","imdbVotes":"875,526","imdbID":"tt0109830","Type":"movie","Response":"True"
		},
		"rotten":{
			"ratings":{ 
				"critics_rating": "Certified Fresh",
			  	"critics_score": 97,
                "audience_rating": "Upright",
			   	"audience_score": 88 
			},
			"similarTitles":{
				"Forest Fire":3434412,
				"Gump and Gumper":341
			}
		}
	},
	"1":{
		"args":["Forrest Gump2", 9, 12321, 1995], 
		"data":null,
		"rotten":{
			"ratings":{ 
				"critics_rating": "Certified Fresh",
			  	"critics_score": 97,
                "audience_rating": "Upright",
			   	"audience_score": 88 
			},
			"similarTitles":{
				"Forest Fire":3434412,
				"Gump and Gumper":341
			}
		}
	},
	"2":{
		"args":["Forrest Gump", 9, 12345, 1994], 
		"data":{"Title":"Forrest Gump","Year":"1994","Rated":"PG-13","Released":"06 Jul 1994","Runtime":"142 min","Genre":"Drama, Romance","Director":"Robert Zemeckis","Writer":"Winston Groom (novel), Eric Roth (screenplay)","Actors":"Tom Hanks, Rebecca Williams, Sally Field, Michael Conner Humphreys","Plot":"Forrest Gump, while not intelligent, has accidentally been present at many historic moments, but his true love, Jenny Curran, eludes him.","Language":"English","Country":"USA","Awards":"Won 6 Oscars. Another 42 wins & 53 nominations.","Poster":"http://ia.media-imdb.com/images/M/MV5BMTQwMTA5MzI1MF5BMl5BanBnXkFtZTcwMzY5Mzg3OA@@._V1_SX300.jpg","Metascore":"82","imdbRating":"8.8","imdbVotes":"875,526","imdbID":"tt0109830","Type":"movie","Response":"True"
		},
		"rotten":{
			"ratings":{ 
				"critics_rating": "Certified Fresh",
			  	"critics_score": 97,
                "audience_rating": "Upright",
			   	"audience_score": 88 
			},
			"similarTitles":{
			}
		}
	},
	"3":{
		"args":["Forrest Gump", 9, 12345, 1994], 
		"data":{"Title":"Forrest Gump","Year":"1994","Rated":"PG-13","Released":"06 Jul 1994","Runtime":"142 min","Genre":"Drama, Romance","Director":"Robert Zemeckis","Writer":"Winston Groom (novel), Eric Roth (screenplay)","Actors":"Tom Hanks, Rebecca Williams, Sally Field, Michael Conner Humphreys","Plot":"Forrest Gump, while not intelligent, has accidentally been present at many historic moments, but his true love, Jenny Curran, eludes him.","Language":"English","Country":"USA","Awards":"Won 6 Oscars. Another 42 wins & 53 nominations.","Poster":"http://ia.media-imdb.com/images/M/MV5BMTQwMTA5MzI1MF5BMl5BanBnXkFtZTcwMzY5Mzg3OA@@._V1_SX300.jpg","Metascore":"82","imdbRating":"8.8","imdbVotes":"875,526","imdbID":"tt0109830","Type":"movie","Response":"True"
		},
		"rotten":null
	}
}
EOT;

	echo "TOY DATA IS BEING USED\n";

	$movies = array();
	$dat = json_decode($data, true);
	foreach ($dat as $id => $entry) {
		$args = $entry["args"];
		$mov = new Movie($args[0], $args[1], $args[2], $args[3]);
		$imdb = $entry["data"];
		$rotten = $entry["rotten"];
		if (isset($imdb)) $mov->populateFromIMDB($imdb);
		if (isset($rotten)) {
			$mov->populateFromRottenTomatoes($rotten["ratings"]);
			$mov->addSimilarTitles($rotten["similarTitles"]);
		}
		array_push($movies, $mov);
	}
	return $movies;
}

print_r(getMovies());

?>

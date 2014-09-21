DROP TABLE IF EXISTS movies;
CREATE TABLE movies (
    id             INTEGER UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name			VARCHAR(100),
	year			VARCHAR(20),
	runtime			INTEGER,
	actors			VARCHAR(1024),
	directors		VARCHAR(1024),
	genre			VARCHAR(256),
	fRating			VARCHAR(20),
	imageURL		VARCHAR(512),
	
	rNetflix		FLOAT,
	rImdb			FLOAT,
	rRotTomCritic	FLOAT,
	rRotTomViewer	FLOAT,
	
	imdbJSON		TEXT,
	netflixJSON		TEXT,
	rottenJSON		TEXT
);


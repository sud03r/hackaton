DROP DATABASE IF EXISTS influx;
CREATE DATABASE influx;

GRANT USAGE ON *.* TO 'influx'@'localhost';
DROP USER 'influx'@'localhost';

CREATE USER 'influx'@'localhost' IDENTIFIED BY 'influx';
GRANT SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON influx.* TO 'influx'@'localhost';

USE influx;
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


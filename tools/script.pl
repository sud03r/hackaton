#! /usr/bin/perl -w

$fileName = open(DATA, "<movieData.txt");

while(<DATA>) {
	@fields = split(",");
	#print "$fields[2]\n";
	chomp $fields[2];
	print "wget http://api.rottentomatoes.com/api/public/v1.0/movies.json?q=$fields[2]&page_limit=3&page=1&apikey=y9ycwv778uspxkj6g4txme2h\n";
	#`cat result.html >> rottenTomotoesData.txt`;
	#`echo "" >> rottenTomotoesData.txt`;
}


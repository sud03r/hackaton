#! /usr/bin/perl -w

if ($#ARGV != 2) 
{
	print "Usage $0 netflix.txt imdb.txt rotten.txt\n";
	exit;
}

open(NETFLIX, "<$ARGV[0]") or die "Cant open $ARGV[0]";
open(IMDB, "<$ARGV[1]") or die "Cant open $ARGV[1]";
open(ROTTEN, "<$ARGV[2]") or die "Cant open $ARGV[2]";

print "USE influx;\n";
while($jNflix = <NETFLIX>) {
	# There is a one-to-one correspondence here
	if ($jNflix =~ m/".*?","(.*?)","(.*?)","(.*?)","(.*?)"/)
	{
		$imageURL = $1;
		$name = $2;
		$year = $3;
		$nRating = $4;
	}

	chomp $jNflix;
	$jImdb = <IMDB>;
	chomp $jImdb;
	$jRotten = <ROTTEN>;
	chomp $jRotten;
	$rImdb = -1;
	if ($jImdb =~ m/"imdbRating":"(.*?)"/)
	{
		$rImdb = $1;
	}
	$runtime = -1;
	if ($jImdb =~ m/"Runtime":"(.*?) min"/)
	{
		$runtime = $1;
	}

	$actors = "";
	if ($jImdb =~ m/"Actors":"(.*?)",/) {
		$actors = $1;
		$actors =~ s/'/\\'/g;
	}

	$directors = "";
	if ($jImdb =~ m/"Director":"(.*?)",/) {
		$directors = $1;
		$directors =~ s/'/\\'/g;
	}

	$genre = "";
	if ($jImdb =~ m/"Genre":"(.*?)",/) {
		$genre = $1;
		$genre =~ s/'/\\'/g;
	}

	$fRating = "";
	if ($jImdb =~ m/"Rated":"(.*?)",/) {
		$fRating = $1;
		$fRating =~ s/'/\\'/g;
	}

	$rotCritic = -1;
	if ($jRotten =~ m/"critics_score":(.*?),/)
	{
		$rotCritic = $1;
	}
	
	$rotAudience = -1;
	if ($jRotten =~ m/"audience_score":(.*?)},/)
	{
		$rotAudience = $1;
	}

	$jImdb =~ s/'/\\'/g;
	$jNflix =~ s/'/\\'/g;
	$jRotten =~ s/'/\\'/g;
	
	print "INSERT INTO movies (name, year, runtime, actors, directors, genre, fRating, imageURL,". 
							"rNetflix, rImdb, rRotTomCritic, rRotTomViewer, imdbJSON, netflixJSON, rottenJSON) VALUES (".
							"\"$name\", '$year', '$runtime', '$actors', '$directors', '$genre', '$fRating', \"$imageURL\",".
							"'$nRating', '$rImdb', '$rotCritic', '$rotAudience', '$jImdb', '$jNflix', '$jRotten');\n";
}

close(NETFLIX);
close(IMDB);
close(ROTTEN);

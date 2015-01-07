<?php
/*
 * This file includes some information about the product.
 */

 include 'includes/env.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>About <?php echo $APP_NAME ?> - The smart search for movies </title>

    <!-- CSS Files -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/results.css" rel="stylesheet">
	<link href="css/details.css" rel="stylesheet">
	
	<link href='http://fonts.googleapis.com/css?family=Lato:400,300,100,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open Sans">
	
	<!-- Library javascript files -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="js/lib/bootstrap.min.js"></script>
	<script src="js/lib/underscore-min.js"></script>
	<script src="js/lib/backbone-min.js"></script>
	<script src="js/lib/masonry.pkgd.min.js"></script>
	<script src="js/lib/jquery.dotdotdot.min.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	<script>
		PAGE_ROOT = "<?= implode("/", explode("/",$_SERVER['PHP_SELF'],-1)) ?>";
	</script>
  </head>
  <body>
    <div class="wrapper">
		<div class="container" >
			<a id="about"></a>
			<h2>About - What is Medley</h2>
			<p>After a long day of hacking we love to just collapse on the couch and watch some Netflix. But it is always such a struggle to find good movies on Netflix unless you spend an hour browsing through their content and persistently checking back and forth between IMDb, Rotten Tomatoes, and Netflix. So we set out to solve this problem to save ourselves and others from the frustration. </p>

			<p>We made a search engine that collects data from Netflix, IMDb, and Rotten Tomatoes, and presents it in a neat little all-inclusive package. But this wasn't enough for us! We wanted to make searching for content easier as well and so we built a "smart-search". Essentially, you can make complex queries to reflect what kind of movies you are in the mood for. For example, you can type in "Movies with Ryan Gosling after 2005 and rated higher than 7.0 on IMDb". It doesn't have to be that complex of course! You can always just browse and sort the content by different variables.</p>
			
			
			<br />
			<a id="help"></a>
			<h2>Help - How to Use Medley</h2>
			<p>Note: Medley is currently in <i>beta</i> stages. This means some of the functionality is still (unfortunately) broken. </p>
			
			<p>Although Medley is in Beta, you can still make some interesting queries to find movies on netflix! On the main page, type in a search query such as "Thriller", "Quentin tarrantino movies", "Ryan Gosling Movies", "2012 Movies" or "Movies Rated higher than 6.5". Try these out!</p>
			
			<p>We soon hope to complete the "Just Browse" feature, and also add spell-check and auto-completion. Once done, these features will make natural search browsing much easier!</p>
			
			
			<br />
			<a id="team"></a>
			<a id="contact"></a>
			<h2>Contact - The Medley Team</h2>
			Medley was created by four University of Waterloo students in the Fall of 2014.
			<ul>
				<li><b><a href="http://www.rekaszepesvari.me">Reka</a></b> - the Designer and the "mind behind the product"! This 4th-year Fine Arts major first identified the need for the product, helped decide what features to build first, and created the initial design and interface.</li>
				<li><b><a href="http://sud03r.github.io/">Neeraj</a></b> - the resident Data Engineer ("Data Guy" as we like to call him)! This Master's Computer Systems expert is responsible for data infrastructure and back-end</li>
				<li><b><a href="http://szepi1991.github.io/">David</a></b> - the AI (Artificial Intelligence) Expert! This Master's Machine Learning guru is responsible for the search query infrastructure and the fundamental Natural Language Processing magic</li>
				<li><b><a href="https://ca.linkedin.com/pub/deon-nicholas/67/607/400">Deon</a></b> - the front-end wizard! This 4th-year Computer Science major is responsible for realizing the front-end designs using fancy JavaScript libraries such as Backbone.js and jQuery.</li>
			</ul>
			<p>
			We've still got a long way to go, but we're confident that we can make something great here. Who knows? We have ideas that we hope will change the way you stream your movies!</p>
			
			<p>If you'd like to get a hold of the Medley team, visit our webites/profiles! (Click on any of our names to get to the respective profile)</p>
			
			<br />
			<a id="copyright"></a>
			<h2>Copyright</h2>
			Medley&trade;, the Medley logo, the Medley slogan ("a smart search for your movies"&trade;), www.medleymovies.me, and all associated Medley branding are trademarks of the Medley team. The text, code, and assets on www.medleymovies.me (collectively, the "website") is Copyright &copy; 2014-2015 the Medley Team. It is a joint work by the authors. All rights reserved. <br /><br />
			
			Netflix, IMDB, and RottenTomatoes are their own entities. All data we use is either attained via an API or via publically available means.
			
			<br />
			<br />
			<br />
			<br />

		</div>
		
		<div class="footer">
			<div class="footer-centered">
				<div class="footer-style"> <a href="index.php">Home</a> </div>
				<div class="footer-style"> <a href="info.php#about">About</a> </div>
				<div class="footer-style"> <a href="info.php#help">Help</a> </div>
				<div class="footer-style"> <a href="info.php#contact">Contact</a> </div>
				<div class="footer-style"><a href="info.php#copyright">Copyright</a></div>
			</div>
		</div>
	</div>
  </body>
</html>
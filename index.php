<?php
/*
 * This file is responsible for the overall page. There may be
 * common components that are loaded, and this page is responsible
 * for loading them. It also includes boiler-plate code
 * like the page title, etc.
 */

 include 'includes/env.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $APP_NAME ?> - The smart search for movies </title>

    <!-- CSS Files -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/results.css" rel="stylesheet">
	<link href="css/details.css" rel="stylesheet">
	
	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open Sans">

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
		<div class="container" id="app-container">
			<!-- The main page is loaded here-->
			<div id="page-content"></div>
		</div>

		<div class="footer">
			<div class="footer-centered">
				<div class="footer-style"> <a href="/help">Help</a> </div>
				<div class="footer-style"> <a href="/contact">Contact</a> </div>
				<div class="footer-style"> <a href="/about">About</a> </div>
				<div class="footer-style"><a href="/copyright">Copyright</a></div>
			</div>
		</div>
	</div>
	
    <!-- Library javascript files -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="js/lib/bootstrap.min.js"></script>
	<script src="js/lib/underscore-min.js"></script>
	<script src="js/lib/backbone-min.js"></script>

	<!-- Base classes and components -->
	<script src="js/base/page-base.js"></script>
	<script src="js/base/search-base.js"></script>
	<script src="js/base/movie-model.js"></script>

	<!-- Pages -->
	<script src="js/search-view.js"></script>
	<script src="js/results-view.js"></script>
	<script src="js/details-view.js"></script>

	<!-- The app (should be included last) -->
	<script src="js/app.js"></script>
  </body>
</html>
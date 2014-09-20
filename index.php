<?php
/*
 * This file is responsible for the overall page. There may be
 * common components that are loaded, and this page is responsible
 * for loading them. It also includes boiler-plate code
 * like the page title, etc.
 */

 include '/includes/env.php';
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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  	<div class="container" id="app-container">
		<!-- The main page is loaded here-->
		<div id="page-content"> </div>

		<div class="footer">
		Copyright stuff here.
		</div>
	</div>

    <!-- Library javascript files -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="js/underscore-min.js"></script>
	<script src="js/backbone-min.js"></script>

	<!-- Custom javascript files -->
	<script src="js/custom/app.js"></script>

	<!-- Views -->
	<script src="js/custom/search-view.js"></script>
  </body>
</html>


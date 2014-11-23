[1mdiff --git a/css/results.css b/css/results.css[m
[1mindex ef3727f..2c25404 100644[m
[1m--- a/css/results.css[m
[1m+++ b/css/results.css[m
[36m@@ -12,6 +12,10 @@[m
 	padding: 0px;[m
 }[m
 [m
[32m+[m[32m.no-result-msg {[m
[32m+[m	[32mdisplay: none;[m
[32m+[m[32m}[m
[32m+[m
 .search-title-container {[m
 	position: fixed;[m
 	width: 100%;[m
[1mdiff --git a/js/results-view.js b/js/results-view.js[m
[1mindex b666953..1a50094 100644[m
[1m--- a/js/results-view.js[m
[1m+++ b/js/results-view.js[m
[36m@@ -42,15 +42,21 @@[m [mvar ResultsView = PageBase.extend({[m
 	},[m
 	[m
 	render: function(callback) {[m
[32m+[m		[32mvar self = this;[m
 		PageBase.prototype.render.call(this, function(){[m
 			// Once the page is loaded, apply masonry to layout everything[m
 			var $container = $('.results-collection');[m
[31m-		[m
[31m-			$container.masonry({[m
[31m-			  isFitWidth: true,[m
[31m-			  gutter: 10,[m
[31m-			  itemSelector: '.movie-result'[m
[31m-			});[m
[32m+[m[41m			[m
[32m+[m			[32mif (self.collection.isEmpty()) {	// first check if container is empty[m
[32m+[m				[32m$(".no-result-msg").show();[m
[32m+[m			[32m} else {[m
[32m+[m				[32m$(".no-result-msg").hide();[m
[32m+[m				[32m$container.masonry({[m
[32m+[m				[32m  isFitWidth: true,[m
[32m+[m				[32m  gutter: 10,[m
[32m+[m				[32m  itemSelector: '.movie-result'[m
[32m+[m				[32m});[m
[32m+[m			[32m}[m
 			[m
 			// Just in case another callback was specified[m
 			if (_.isFunction(callback)) callback();	[m
[1mdiff --git a/layouts/results.html b/layouts/results.html[m
[1mindex 9bc4833..2c9630a 100644[m
[1m--- a/layouts/results.html[m
[1m+++ b/layouts/results.html[m
[36m@@ -28,6 +28,11 @@[m
 [m
 <div class="container">[m
 <div class="results-collection">[m
[32m+[m
[32m+[m	[32m<div class="no-result-msg">[m
[32m+[m		[32mSorry, no movies on Netflix match your search.[m[41m [m
[32m+[m	[32m</div>[m
[32m+[m
 	<!-- See underscore.js and backbone.js -->[m
 	<!-- One of these for each individual movie item -->[m
 	<% _.each(movies, function(movie,index) { %>[m

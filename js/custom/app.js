/* This file is responsible for initially loading things.
 * It is also responsible for setting up the URLs (routes),
 * and the mechanisms for switching between pages seemlessly.
 *
 * See backbonejs.org and backbonejs.org/examples/todos/todos.js 
*/

// Wait for jquery to load
$(function(){
	var UrlMapper = Backbone.Router.extend({
		routes: {
			"search"        : "search"
			//"help":                 "help",    // #help
			//"search/:query":        "search",  // #search/kiwis
			//"search/:query/p:page": "search"   // #search/kiwis/p7
		},

		help: function() {
		
		},

		// Go to search page with a fixed query
		search: function(query, page) {
		}

	});

	// Our overall **AppView** is the top-level piece of UI.
	var AppView = Backbone.View.extend({
		// Instead of generating a new element, bind to the existing skeleton of
		// the App already present in the HTML.
		el: $("#app-container"),

		// TODO: Fill in the events if necessary
		events: {
		  //"click #toggle-all": "toggleAllComplete"
		},

		// At initialization we bind to the relevant events on the `Todos`
		// collection, when items are added or changed. Kick things off by
		// loading any preexisting todos that might be saved in *localStorage*.
		initialize: function() {
		  // TODO: Fill this in somehow
		},

		// Re-rendering the App just means refreshing the statistics -- the rest
		// of the app doesn't change.
		render: function() {
		  // TODO: Render function
		  $("#page-content").load("layouts/search.html");
		},
	});

	// Finally, we kick things off by creating the **App**.
	var app = new AppView;
	var urlMapper = new UrlMapper;

	// Start the history
	ROOT = "/deon";
	Backbone.history.start({root: ROOT});
	urlMapper.navigate("/search");

	app.render()
});

/* This file is responsible for initially loading things.
 * It issialso responsible for setting up the URLs (routes),
 * and the mechanisms for switching between pages seemlessly.
 *
 * See backbonejs.org and backbonejs.org/examples/todos/todos.js 
*/

// Wait for jquery to load
$(function(){
	// Our overall **AppView** is the top-level piece of UI.

	var AppView = Backbone.View.extend({
		// Instead of generating a new element, bind to the existing skeleton of
		// the App already present in the HTML.
		el: "#app-container",

		urlMapper: new Backbone.Router(),
		
		pageViews : {
			"search" : new SearchView()
		},
		
		// TODO: Fill in the events if necessary
		events: {
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
		},

		loadPage: function(pageName) {
			pageView = this.pageViews[pageName];

			if (pageView == null) {
				throw ("Could not find page: " + pageName);
			}
			
			// Change views if needed
			$("#page-content").empty();
			$("#page-content").append(pageView.$el);
			pageView.render();

			// Pass this to the URL mapper if needed
			this.urlMapper.navigate("/" + pageName);
		}
	});

	// Finally, we kick things off by creating the **App**.
	var app = new AppView;

	// Start the history
	app.loadPage("search");
	app.render();
});

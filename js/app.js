/* This file is responsible for initially loading things.
 * It is also responsible for setting up the URLs (routes),
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
		
		// TODO: Fill in the events if necessary
		events: {
		},

		// At initialization we bind to the relevant events on the `Todos`
		// collection, when items are added or changed. Kick things off by
		// loading any preexisting todos that might be saved in *localStorage*.
		initialize: function() {
		  Backbone.View.prototype.initialize.apply(this,arguments);
		  $.ajaxSetup({timeout:10000}); //in milliseconds

		  movieCollection = new MovieCollection;
		  this.pageViews = {
			"search"  : new SearchView({app: this}),	// HACK: The first page acts as the default page
			"results" : new ResultsView({app: this}),
			"details" : new DetailsView({app: this})
		  }
		  
		  // Setup the backbone.js router (handles browers navigation, e.g.: the "back" button)
		  me = this;	// use "me", since "this" will be overwritten in the callback scope
		  _.each(this.pageViews, function(view,key) {
			me.urlMapper.route(key + "(/)",key,function(){
				me.loadPage(key);
			});
		  })
		  var defaultKey = _.chain(this.pageViews).keys().first().value();
		  me.urlMapper.route("(/)", defaultKey, function(){
			me.loadPage(defaultKey);
		  });
		},

		// Re-rendering the App just means refreshing the statistics -- the rest
		// of the app doesn't change.
		render: function() {
		  // TODO: Render function
		},

		searchCallback: function(movieCollection) {
			resultsPage = this.pageViews["results"];
			resultsPage.collection = movieCollection;
			this.loadPage("results");
		},

		showMovieDetails: function(movieModel) {
			detailsPage = this.pageViews["details"];
			detailsPage.model = movieModel;
			this.loadPage("details");
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
			
			$("body").removeClass();
			$("body").addClass(pageName + "-page-body");

			// Pass this to the URL mapper if needed
			this.urlMapper.navigate(pageName, {trigger: true});
		}
	});

	// Finally, we kick things off by creating the **App**.
	var app = new AppView;

	// Start the history
	app.loadPage("search");	// TODO: CHANGE THIS BACK
	app.render();
	Backbone.history.start({root: PAGE_ROOT, silent: true});
});

// The search results.
// A collection of movies will be given (and can also be updated dynamically)
// These movies will be displayed in a list

var ResultsView = Backbone.View.extend({
	template  : "results.html",
	className : "results-page",
	
	events: {
		//"click .movie-result a": "selectMovie"
	},

	loadTemplate: function(callback) {
		if (this.loaded) return callback.apply(this,arguments);

		// Converts template (URL) into underscore.js template (function)
		self = this;
		$.get("layouts/" + this.template, function (data) {
			if (self.loaded) return;
			self.template = _.template(data);
			self.loaded = true;
			if (callback != null) callback.apply(self,arguments);
		});
	},

	initialize: function(options) {
		_.extend(this,options);
		this.collection = new MovieCollection;
		this.loaded = false;
	},

	render: function() {
		this.loadTemplate(function () {
			this.$el.html(this.template({movies: this.collection.toJSON()}));
		});
	},

	selectMovie: function(obj) {
		$el = $(obj.currentTarget);

		// Get a reference to the movie object (model)
		var movieIndex = $el.data("movie-id");
		if (!_.isNumber(movieIndex) || movieIndex < 0 || movieIndex >= this.collection.length) {
			throw "Invalid movie id selected. Something went wrong.";
		}
		var movie = this.collection.at(movieIndex);

		// Apply the parent application's call-back function
		if (_.isFunction(this.app.showMovieDetails)) {
			this.app.showMovieDetails(movie);
		}

		// Return the movie, just in case someone else needs it
		return movie;
	}
});

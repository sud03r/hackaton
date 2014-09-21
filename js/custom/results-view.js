// The search results.
// A collection of movies will be given (and can also be updated dynamically)
// These movies will be displayed in a list

var ResultsView = Backbone.View.extend({
	template  : "results.html",
	className : "results-page",
	
	events: {
		//"click .movie-result a": "selectMovie"
		"click #sort-by-list li" : "sortCollection",
		"click .glyphicon-search" : "handler",
		"keypress" : "searchOnEnter"
	},

	searchOnEnter: function(e) {
		if(e.keyCode == 13){
			this.handler();
		}
	},

	handler: function(e) {
		var args = $('#search-text').val();
		self = this;
		$.get('ajax/search_movie.php', {q : args}, function(result) {
			if (result.success) {
				var movies = new MovieCollection;
				var movieInfo = result.data;
				var MAX_MOVIES = Math.min(100,movieInfo.length);
				for (var i = 0; i < MAX_MOVIES; i++) 
				{
					parsed = _.pick(movieInfo[i], function (value) { 
						if (!!!value) return false;
						if (_.isArray(value) && (_.isEmpty(value) || value[0] == "")) return false;
						return true;
					});
					movies.add(new MovieModel(parsed));
				}

				// Apply the parent application's call-back function
				if (_.isFunction(self.app.searchCallback)) {
					self.app.searchCallback(movies);
				}
			}
		}, 'json');
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

	sortCollection: function(type) {
		if (!_.isString(type)) {
			type = $(type.currentTarget).data("field");
		}

		this.collection.setComparator(type);
		this.render();
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

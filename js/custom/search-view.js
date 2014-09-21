// The search page (this is usually the default page)
// Contains a search bar, and buttons, etc

var SearchView = Backbone.View.extend({
	tagName: "div",
	template: "search.html",
	
	events: {
		"click #search-submit" : "search"
	},

	search: function() {
		var args = $('#search-text').val();
		self = this;
		$.get('ajax/search_movie.php', {q : args}, function(result) {
			alert("Got something");
			console.log(result);
			if (result.success) {
				var movies = new MovieCollection;
				var movieInfo = result.data;
				for (var i = 0; i < movieInfo.length; i++) 
				{
					movies.add(movieInfo[i]);
				}

				// Apply the parent application's call-back function
				if (_.isFunction(self.app.searchCallback)) {
					self.app.searchCallback(movies);
				}
			}
		}, 'json');
	},

	initialize: function(options) {
		_.extend(this,options);
		this.$el.load("layouts/" + this.template);
	}
});

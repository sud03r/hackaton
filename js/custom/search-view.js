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
		$.get('ajax/search_movie.php?q=', args, function(result) {
			if (result.success) {
				var movies = new MovieCollection;
				var movieInfo = result.data;
				for (var i = 0; i < movieInfo.length; i++) 
				{
					movies.add(movieInfo[i]);
				}
				alert(movies.at(6).get('mName'));
				return movies;
			}
		}, 'json');
	},

	initialize: function() {
		this.$el.load("layouts/" + this.template);
	}
});

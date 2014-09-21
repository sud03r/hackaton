// The search results.
// A collection of movies will be given (and can also be updated dynamically)
// These movies will be displayed in a list

var MovieCollection = Backbone
var ResultsView = Backbone.View.extend({
	template: "search.html",

	
	events: {
		"click #search-submit" : "search"
	},

	search: function() {
		alert("SEAR");
	},

	initialize: function() {
		this.$el.append($("<div>").load("layouts/" + this.template));
	}
});

// The search page (this is usually the default page)
// Contains a search bar, and buttons, etc

var SearchView = Backbone.View.extend({
	tagName: "div",
	template: "search.html",
	
	events: {
		"click #search-submit" : "search"
	},

	search: function() {
		alert("SEAR");
	},

	initialize: function() {
		this.$el.load("layouts/" + this.template);
	}
});

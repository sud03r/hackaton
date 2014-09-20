// The search page (this is usually the default page)
// Contains a search bar, and buttons, etc

var SearchView = Backbone.View.extend({
	tagName: "div",

	template: "search.html",
	
	events: {
		"click #submit-search" : "search"
	},

});

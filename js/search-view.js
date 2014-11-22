// The search page (this is usually the default page)
// Contains a search bar, and buttons, etc

var SearchView = PageBase.extend({
	tagName: "div",
	template: "search.html",
	
	events: {
		"click #search-submit" : "search",
		"keypress"             : "searchOnEnter"
	},

	searchOnEnter: function(e) {
		if(e.keyCode == 13) {
			this.search();
		}
	},

	search: function() {
		NUM_MOVIES = 100;		// Default number of movies
		PAGE_NUMBER = 0;		// Default page
		text = $('#search-text').val();
		
		SearchBase.search(text,PAGE_NUMBER, NUM_MOVIES, this);
	}
});

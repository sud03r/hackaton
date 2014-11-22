// The search results.
// A collection of movies will be given
// These movies will be displayed in a list

var ResultsView = PageBase.extend({
	template  : "results.html",
	className : "results-page",
	
	collection : new MovieCollection,
	
	getData : function() {
		return { movies: this.collection.toJSON() };
	},
	
	events: {
		"click #sort-by-list li" : "sortCollection",
		"click .glyphicon-search" : "search",
		"keypress" : "searchOnEnter"
	},
	
	searchOnEnter: function(e) {
		if (e.keyCode == 13){
			return this.search();
		}
	},
	
	search : function() {
		PAGE_NUMBER = 0;	// TODO: Incorporate actual page-numbering
		PAGE_SIZE = 100;
		
		var args = $('#search-text').val();
		SearchBase.search(args, PAGE_NUMBER, PAGE_SIZE, this);
	},

	sortCollection: function(type) {
		if (!_.isString(type)) {
			type = $(type.currentTarget).data("field");
		}

		this.collection.setComparator(type);
		this.render();
	}
});

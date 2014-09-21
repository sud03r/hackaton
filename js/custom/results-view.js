// The search results.
// A collection of movies will be given (and can also be updated dynamically)
// These movies will be displayed in a list

var ResultsView = Backbone.View.extend({
	template  : "results.html",
	className : "results-page",
	
	events: {
		"click #search-submit" : "search"
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

	initialize: function() {
		this.collection = [
			{ title: "Random crap" }
		];

		this.loaded = false;
	},

	render: function() {
		this.loadTemplate(function () {
			this.$el.html(this.template({movies: this.collection}));
		});
	}
});

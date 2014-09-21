// The details view.
// Shows all the details of a movie
// Uses a MovieModel. Shows suggestions, reviews, watch-link.

var DetailsView = Backbone.View.extend({
	template  : "details.html",
	className : "details-page",
	
	events: {
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
		this.collection = new MovieCollection;
		this.loaded = false;
	},

	render: function() {
		this.loadTemplate(function () {
			this.$el.html(this.template({movies: this.collection.toJSON()}));
		});
	}
});

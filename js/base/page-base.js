// The base class for a "page"
// Contains the code necessary to load the template and register events
// Derived from Backbone.View

// To override/extend:
//  a) specify a "template" (string)
//  b) override the "getData" function
var PageBase = Backbone.View.extend({
	tagName: "div",
	
	template: "",
	getData: function(){ return {} },
	
	initialize: function(options) {
		_.extend(this,options);
		this.loaded = false;
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
	
	render: function() {	// render page with dataObject as the model
		this.loadTemplate(function () {
			this.$el.html(this.template(this.getData()));
		});
		this.delegateEvents();
	}
});

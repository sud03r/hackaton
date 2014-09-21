// A movie is a movie. We describe the model and related classes.
// For example, we define a MovieCollection class too!

var MovieModel = Backbone.Model.extend({

});


var MovieCollection = Backbone.Collection.extend({
	model: MovieModel
});

// A reusable class for "search" functionality
// Extend any page with the functions from this class on constructor/initalize
// Or simply call SearchBase.search(...) where needed
var SearchBase = {
	search: function(text, pageNumber, pageSize, self) {
		if (_.isEmpty(text)) return false;
		if (!(_.isNumber(pageNumber) && _.isNumber(pageSize))) return false;
		if (_.isNull(self)) return false;
		
		$.get('ajax/search_movie.php', {q : text, p: pageNumber, s: pageSize}, function(result) {
			if (result.success) {
				var movies = new MovieCollection;
				var movieInfo = result.data;
				var MAX_MOVIES = Math.min(NUM_MOVIES, movieInfo.length);
				for (var i = 0; i < MAX_MOVIES; i++) 
				{
					parsed = _.pick(movieInfo[i].movie, function (value) {
						if (!value) return false;
						if (_.isArray(value) && (_.isEmpty(value) || _.isEmpty(value[0]))) return false;
						return true;
					});
					parsed.relevance = movieInfo[i].relevance;
					
					// We clean up the "writers" data.
					// HACK: This probably shouldn't be here.
					parsed.writers = _.map(parsed.writers, function (writer) {
						var idx = writer.indexOf("(");
						if (idx == -1) return writer;
						else return writer.substr(0,idx).trim();
					});
					
					movies.add(new MovieModel(parsed));
				}
				
				// Apply the parent application's call-back function
				if (_.isFunction(self.app.searchCallback)) {
					self.app.searchCallback(movies);
				}
			}
		}, 'json').fail( function(reason) {
			console.log("Could not search for movies. Reason: " + reason.statusText + ".");
		});
		
		return true;
	}
}
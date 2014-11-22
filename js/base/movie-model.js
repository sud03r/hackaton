// A movie is a movie. We describe the model and related classes.
// For example, we define a MovieCollection class too!

var MovieModel = Backbone.Model.extend({
	defaults: {
		image: "images/assets/poster-not-available.jpg",
		actors: ["(Not Available)"],
		mName: "(Not Available)",
		awards: "None",
		country: "USA",
		date: Date.now(),
		directors: ["(Not Available)"],
		genres: [],
		imdbVotes: 0,
		language: "English",
		mType: "movie",
		netflixId: -1,
		otherTitles: {},
		plot: "",
		rFamily: "PG-13",
		rating: {},
		runtime: "0 mins",
		similarLink: {},
		writers: ["(Not Available)"],
		year: 1999,
		relevance: 0
	}
});


var MovieCollection = Backbone.Collection.extend({
	model: MovieModel,
	comp_field: "relevance",
	comp_multiplier: -1,

	comparator: function(model) {
		if (_.isEmpty(this.comp_field)) return 0;
		var val = 0;
		var f = this.comp_field;
		if (f == "rating.imdb")
			val = model.get("rating").imdb;
		else if (f == "rating.rotten")
			val = model.get("rating").critics_score;
		else
			val = model.get(f);
		
		if (val == null) val = 0;
		return val*this.comp_multiplier;
	},

	setComparator: function(field) {
		this.comp_field = field;
		this.sort();
	},

	flipDirection: function() {
		this.comp_multiplier *= -1;
		this.sort();
	}
});

/*movies.add([
	{"mName":"Forrest Gump","rating":{"netflix":9,"imdb":"8.8","critics_rating":"Certified Fresh","critics_score":97,"audience_rating":"Upright","audience_score":88},"netflixId":12345,"year":1994,"otherTitles":{"Forest Fire":3434412,"Gump and Gumper":341},"rFamily":"PG-13","date":{"date":"1994-07-06 21:31:15","timezone_type":3,"timezone":"America\/New_York"},"runtime":"142 min","genres":["Drama","Romance"],"directors":["Robert Zemeckis"],"writers":["Winston Groom (novel)","Eric Roth (screenplay)"],"actors":["Tom Hanks","Rebecca Williams","Sally Field","Michael Conner Humphreys"],"plot":"Forrest Gump, while not intelligent, has accidentally been present at many historic moments, but his true love, Jenny Curran, eludes him.","language":"English","country":"USA","awards":"Won 6 Oscars. Another 42 wins & 53 nominations.","image":"http:\/\/ia.media-imdb.com\/images\/M\/MV5BMTQwMTA5MzI1MF5BMl5BanBnXkFtZTcwMzY5Mzg3OA@@._V1_SX300.jpg","imdbVotes":"875,526","mType":"movie"},
	{"mName":"Sleepless in Seattle","rating":{"netflix":5,"imdb":"4.8","critics_rating":"Yuck","critics_score":32,"audience_rating":"Rotten","audience_score":56},"netflixId":4352,"year":1956,"otherTitles":[],"rFamily":"PG","date":{"date":"1956-06-25 21:31:15","timezone_type":3,"timezone":"America\/New_York"},"runtime":"105 min","genres":["Comedy","Drama","Romance"],"directors":["Nora Ephron"],"writers":["Jeff Arch (story)","Nora Ephron (screenplay)","David S. Ward (screenplay)","Jeff Arch (screenplay)"],"actors":["Tom Hanks","Ross Malinger","Rita Wilson","Victor Garber"],"plot":"A recently widowed man's son calls a radio talk-show in an attempt to find his father a partner.","language":"English","country":"USA","awards":"Nominated for 2 Oscars. Another 4 wins & 10 nominations.","image":"http:\/\/ia.media-imdb.com\/images\/M\/MV5BNzc0MDkwNjI0NF5BMl5BanBnXkFtZTgwMTY1MjEyMDE@._V1_SX300.jpg","imdbVotes":"12,376","mType":"movie"}
]);*/

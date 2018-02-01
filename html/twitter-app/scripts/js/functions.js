var nextSetOfTweets = "";

function buildTwitterString(tweetData) {
	tweetString = tweetData.full_text;

	//Search and set user mention links.
	tweetString = tweetString.replace(/(http[s]?[://]([\w./])+)/g, function(foundString) {
		return foundString.link(foundString);
	});

	//Search and set user mention links.
	tweetString = tweetString.replace(/@([A-Za-z0-9-_]+)/g, function(foundString) {
		return foundString.link("https://twitter.com/"+foundString.replace("@", ""));
	});

	//Search and set hashtags.
	tweetString = tweetString.replace(/#([A-Za-z0-9-_]+)/g, function(foundString) {
		return foundString.link("https://twitter.com/search?src=typd&q=%23"+foundString.replace("#", ""));
	});

	return tweetString;
}

function validate_input() {
	if($("#tweet_count").val() < 1) {
		$("#tweet_count").val(1);
	}else if($("#tweet_count").val() > 100) {
		$("#tweet_count").val(100);
	}
}

function buildTweetCard(tweetData) {
	var foundTweets = tweetData;
	$("#results").trigger("ss-destroy");

	if(tweetData.hasOwnProperty('statuses')) {
		var foundTweets = tweetData.statuses;
	}

	$.each(foundTweets, function(key, val) {
		if(!val.in_reply_to_status_id && !val.possibly_sensitive) {
			var rtweeted = false;
			var retweeted_text = "";
			var original_retweeter = "";
			var full_text = "";
			var mediaData = val.entities.hasOwnProperty('media') ? val.extended_entities.media : "";

			if(val.hasOwnProperty('retweeted_status')) {
				rtweeted = true;
				mediaData = val.retweeted_status.entities.hasOwnProperty('media') ? val.retweeted_status.extended_entities.media : "";
				original_retweeter = val.retweeted_status.user.screen_name;
				full_text = buildTwitterString(val.retweeted_status);
			}else {
				full_text = buildTwitterString(val);
			}

			var date = new Date(val.created_at);

			var cardData = {
				created_date: date.toLocaleDateString("en-US") + ' @ ' + date.toLocaleTimeString("en-US"),
				realname: val.user.name,
				screenname: '@'+val.user.screen_name,
				content: full_text,
				retweeted: rtweeted,
				profile_pic: val.user.profile_image_url_https,
				original_tweeter: original_retweeter,
				media: mediaData,
				retweet_count: val.retweet_count,
				tweet_id: val.id_str
			};

			var cardTemplate = $("#tweet-card-template").html();

			var card = Mustache.render(cardTemplate, cardData);

			$("#results").append(card);
		}
	});
	
	$("#results").shapeshift({
		animationSpeed: 100
	});
}

function get_search_params() {
	var username = $("#username").val().replace('@', '');
	var searchQuery = $("#search_box").val().replace("#", "%23");
	var geocodeStr = "";
	var tweetCount = $("#tweet_count").val();

	if($("#latitude").val().length > 0 && $("#longitude").val().length > 0) {
		geocodeStr = $("#latitude").val() + "," + $("#longitude").val() + ",5mi";
	}

	// console.log("user="+username+"&q="+searchQuery+"&geo="+geocodeStr);

	return "user="+username+"&q="+searchQuery+"&geo="+geocodeStr+"&count="+tweetCount;
}

function search_tweets() {
	$("#search_btn").addClass("is-loading");
	$("#results").html("");
	$("#results").trigger("ss-destroy");

	validate_input();

	var searchParameters = get_search_params();
	console.log(searchParameters);
	$.get("scripts/php/search_twitter.php?"+searchParameters, function(response) {
		// console.log(response);
		// $("#results").html(response);
		var jsonData = JSON.parse(response);
		$("#error_result").html("");

		console.log(JSON.parse(response));
		if(jsonData.hasOwnProperty('errors')) { 
			$.each(jsonData.errors, function(key, val) {
				$("#error_result").append(val.message + "<br>");
			});
		}else {
			nextSetOfTweets = jsonData.search_metadata.next_results;
			buildTweetCard(jsonData);
		}
	}).then(function() {
		$("#search_btn").removeClass("is-loading");
	});
}

function load_more_tweets() {
	$.get("scripts/php/search_twitter.php?next_results="+encodeURIComponent(nextSetOfTweets), function(response) {
		// console.log(response);
		var jsonData = JSON.parse(response);
		$("#error_result").html("");
		console.log(JSON.parse(response));
		if(jsonData.hasOwnProperty('errors')) { 
			$.each(jsonData.errors, function(key, val) {
				$("#error_result").append(val.message + "<br>");
			});
		}else {
			nextSetOfTweets = jsonData.search_metadata.next_results;
			buildTweetCard(jsonData);
		}
	});
}

//Callback function for the Google Places API.
function getGeolocation() {
	var locationInput = document.getElementById("loc_autocomplete");
	var locationBox = new google.maps.places.Autocomplete(locationInput);

	google.maps.event.addListener(locationBox, 'place_changed', function() {
		var place = locationBox.getPlace();
		var lat = place.geometry.location.lat();
		var long = place.geometry.location.lng();

		$("#latitude").val(lat);
		$("#longitude").val(long);
	});
}

function get_tweets_in_current_area() {
	//$("#results").trigger("ss-destroy");
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var lat_long_str = position.coords.latitude + "," + position.coords.longitude + ",5mi";
			$("#results").html("");
			$.get("scripts/php/search_twitter.php?geo="+lat_long_str, function(response) {
				jsonData = JSON.parse(response);
				// console.log(response);
				console.log(jsonData);
				nextSetOfTweets = jsonData.search_metadata.next_results;
				buildTweetCard(jsonData);
			});
		});
	}
}

$(document).ready(function() {
	$("#results").shapeshift();

	$(window).scroll(function() {
		if($(window).scrollTop() + $(window).height() >= $(document).height()) {
			load_more_tweets();
		}
	});

	//Bind the enter key to the search button upon typing into the search box.
	$('.search_input').keypress(function(event){
		if(event.keyCode == 13){
			$('#search_btn').click();
		}
	});

	$("#loc_autocomplete").on("keyup", function() {
		if(!$("#loc_autocomplete").val().trim().length > 0) {
			$("#latitude").val("");
			$("#longitude").val("");
		}
	});

	$("#search_btn").unbind().click(function() {
		$(window).scrollTop(0);
		search_tweets();
	});
});
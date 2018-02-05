/*
* This file is the main Javascript file for the index. It holds all of the functions needed on the page.
*
* Author: Bob Henley
*/

var nextSetOfTweets = "";	// Gloabal variable holds the next set of tweets to load for a given search.
var foundTweetCount = 0;	// Global variable holds the number of tweets found in a given search.

var MOBILE_WIDTH = 1024;	// Global constant defines what width the window needs to be below for mobile view.


/*
* This function takes the tweet text from a tweet, and parses it for mentions, links, and hashtags.
*
* @param tweetData The tweet text strint to input
*
* @return The parsed string containing links for hashtags, mentions, and links.
*/
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

/* This function sets the tweet count to be within (1-100) for the sake of searching on twitter */
function validate_input() {
	if($("#tweet_count").val() < 1) {
		$("#tweet_count").val(1);
	}else if($("#tweet_count").val() > 100) {
		$("#tweet_count").val(100);
	}
}

/*
* This function uses the Mustache templating library, and creates the tweet cards for display.
*
* @param tweetData The json object to parse for data to output to the tweet card.
*
*/
function buildTweetCard(tweetData) {
	var foundTweets = tweetData;

	// Check if the data coming in has a statuses object.
	if(tweetData.hasOwnProperty('statuses')) {
		var foundTweets = tweetData.statuses;
	}

	// Parse through the found tweets, and seperate them into a key value pair.
	$.each(foundTweets, function(key, val) {
		// Do not show tweets that are possibly sensitive.
		if(!val.possibly_sensitive) {
			var rtweeted = false;			// Stores whether the tweet is a retweet or not.
			var retweeted_text = "";		// Get the text of the retweet.
			var original_retweeter = "";	// The original user that was retweeted.
			var full_text = "";				// The full text of the tweet.
			var mediaData = val.entities.hasOwnProperty('media') ? val.extended_entities.media : "";	// The json object for media like pictures and videos.

			// Check if this tweet is, in fact, a retweet.
			if(val.hasOwnProperty('retweeted_status')) {
				rtweeted = true;
				mediaData = val.retweeted_status.entities.hasOwnProperty('media') ? val.retweeted_status.extended_entities.media : "";
				original_retweeter = val.retweeted_status.user.screen_name;
				full_text = buildTwitterString(val.retweeted_status);
			}else {
				full_text = buildTwitterString(val);
			}

			// Date that the tweet was created at.
			var date = new Date(val.created_at);

			// Dictionary that stores the data to be sent to the template.
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

			// Grab the template.
			var cardTemplate = $("#tweet-card-template").html();

			// Render the template.
			var card = Mustache.render(cardTemplate, cardData);

			// Append the card to the results div.
			$("#results").append(card);

			// Increment the number of tweets.
			foundTweetCount+=1;
		}
	});
	
	// Only run shapeshift if the screen is not of mobile size.
	if($(window).width() > MOBILE_WIDTH && foundTweetCount > 0) {
		$("#results").shapeshift({
			animationSpeed: 100
		});
	}
}

/*
* This function builds a search query string to be sent to the Twitter search api.
*
* @returns The formatted string that will be used for the query.
*/
function get_search_params() {
	var username = $("#username").val().replace('@', '');
	var searchQuery = $("#search_box").val().replace("#", "%23");
	var geocodeStr = "";
	var tweetCount = $("#tweet_count").val();

	if($("#latitude").val().length > 0 && $("#longitude").val().length > 0) {
		geocodeStr = $("#latitude").val() + "," + $("#longitude").val() + ",5mi";
	}

	return "user="+username+"&q="+searchQuery+"&geo="+geocodeStr+"&count="+tweetCount;
}

/*
* This function calls the twitter api, to make a search for the given parameters.
*/
function search_tweets() {
	foundTweetCount = 0;						// Reset the found tweets to 0.
	$("#search_btn").addClass("is-loading");	// Show the search button as loading.
	$("#results").html("");						// Set the html of the results to empty.
	$("#results").css("height", "100%");		// Reset the height of the results.
	$("#results").trigger("ss-destroy");		// If there is a shapeshift object, destroy it for the new one.
	nextSetOfTweets = "";						// Set the next set of tweets back to nothing.

	// Validate the input of the tweet number.
	validate_input();

	// Get the search parameters for the search.
	var searchParameters = get_search_params();

	// Make the get request to the back end and query the Twitter API.
	$.get("scripts/php/search_twitter.php?"+searchParameters, function(response) {
		// Grab the incoming json data, and parse it from the response.
		var jsonData = JSON.parse(response);

		// Clear any errors that were shown before.
		$("#error_result").html("");

		// Check if there were errors from the response and display them.
		if(jsonData.hasOwnProperty('errors')) { 
			$.each(jsonData.errors, function(key, val) {
				$("#error_result").append(val.message + "<br>");
			});
		}else {
			if($(window).width() < MOBILE_WIDTH) {
				toggle_search();
			}

			// Check if there are more results to load for the given search query.
			if(jsonData.search_metadata.hasOwnProperty("next_results")) {
				nextSetOfTweets = jsonData.search_metadata.next_results;
			}else {
				nextSetOfTweets = "";
			}

			// Build the tweet card, and append it to the results.
			buildTweetCard(jsonData);
		}
	}).then(function() {
		// When the request has finished, stop showing the button as loading, and set the number of results found at the top.
		$("#search_btn").removeClass("is-loading");

		$("#found_results").html("Showing "+foundTweetCount+" tweets (scroll down to load more).");
	});
}

/*
* This function loads more tweets on to the page if there are more tweets to load.
*/
function load_more_tweets() {
	//Check if there are more tweets to load.
	if(nextSetOfTweets) {
		// Make another search if there are more.
		$.get("scripts/php/search_twitter.php?next_results="+encodeURIComponent(nextSetOfTweets), function(response) {
			// Check for errors and parse the json data.
			var jsonData = JSON.parse(response);
			$("#error_result").html("");
			if(jsonData.hasOwnProperty('errors')) { 
				$.each(jsonData.errors, function(key, val) {
					$("#error_result").append(val.message + "<br>");
				});
				return false;
			}else {
				// Check if there are more results.
				if(jsonData.search_metadata.hasOwnProperty("next_results")) {
					nextSetOfTweets = jsonData.search_metadata.next_results;
				}else {
					nextSetOfTweets = "";
				}
				
				// Add more cards to the results.
				buildTweetCard(jsonData);
			}
		}).then(function() {
			$("#found_results").html("Showing "+foundTweetCount+" tweets (scroll down to load more).");
		});
	}else {
		// If there are no more tweets to load, hide the button for loading them.
		$("#loading_button").css("display", "none");
	}
}

/* 
* This function is a callback for the Google Places API. It shows the autocomplete for the location search box.
*/
function getGeolocation() {
	var locationInput = document.getElementById("loc_autocomplete");
	var locationBox = new google.maps.places.Autocomplete(locationInput);

	google.maps.event.addListener(locationBox, 'place_changed', function() {
		var place = locationBox.getPlace();
		var lat = place.geometry.location.lat();
		var long = place.geometry.location.lng();

		// Store the locations lat and long for later use.
		$("#latitude").val(lat);
		$("#longitude").val(long);
	});
}

/*
* This function is like the search function, however it uses HTML 5 Geolocation to search for tweets in the user's area.
*/
function get_tweets_in_current_area() {
	foundTweetCount = 0;
	$("#loading_button").css("display", "inline-block");

	// Check if the browser supports HTML 5 Geolocation.
	if(navigator.geolocation) {
		// If it does, get the user's current geolocation, and search for tweets in that area.
		navigator.geolocation.getCurrentPosition(function(position) {
			var lat_long_str = position.coords.latitude + "," + position.coords.longitude + ",5mi";
			$("#results").html("");
			$("#results").css("height", "100%");
			$.get("scripts/php/search_twitter.php?geo="+lat_long_str, function(response) {
				jsonData = JSON.parse(response);
				nextSetOfTweets = jsonData.search_metadata.next_results;
				buildTweetCard(jsonData);
			}).then(function() {
				$("#found_results").html("Showing "+foundTweetCount+" tweets (scroll down to load more).");
			});
		});
	}
}

/*
* This function will hide the modal for the tweet posting functionality.
*/
function hide_modal() {
    $("#post_tweet").removeClass("is-active");
}

/*
* This function will show the modal for the tweet posting functionality, and will post the input tweet.
*/
function show_modal() {
    $("#post_tweet").addClass("is-active");

    // If the post tweet button was hit, access the twitter api, and post the input tweet.
    $("#post_tweet_btn").unbind().click(function() {
    	$("#post_tweet_btn").prop("disabled", true);	// Disable the button to avoid a dulicate tweet/error.
    	$("#post_tweet_btn").addClass("is-loading");	// Show the button as loading.
    	
    	// Send the POST request to the twitter api to post the tweet.
    	$.post("scripts/php/post_tweet.php", {tweet: $("#status").val()}, function(response) {
    		// Parse the response from the API.
    		json = JSON.parse(response);
    	}).then(function() {
    		// When the request has finished, check for errors, and handle them accordingly.
    		if(json.hasOwnProperty("errors")) {
    			$("#post_err").html(json.errors[0].message);
    			$("#post_tweet_btn").prop("disabled", false);
	    		$("#post_tweet_btn").removeClass("is-loading");
    		}else {
	    		$("#post_tweet_btn").prop("disabled", false);
	    		$("#post_tweet_btn").removeClass("is-loading");
	    		$("#status").val("");
	    		$("#post_err").html("");
	    		hide_modal();
    		}
    	});
    });
}

/*
* This function will remove a tweet card from view when the 'x' is hit.
*/
function remove_tweet(ref) {
	foundTweetCount -= 1;

	$(ref.parentNode.parentNode).css('display', 'none'); $('#results').trigger('ss-rearrange');
	$("#found_results").html("Showing "+foundTweetCount+" tweets (scroll down to load more).");
}

/*
* Actions to take when the page has been loaded.
*/
$(document).ready(function() {
	// Load more tweets when the user has scrolled to the bottom of the page.
	$(window).scroll(function() {
		if($(window).scrollTop() + $(window).height() >= $(document).height()) {
			if(nextSetOfTweets) {
				load_more_tweets();
			}else {
				$("#loading_button").css("display", "none");
			}
		}
	});

	// Show a loading symbol when the page is making a request.
	$(document).ajaxStart(function() {
		$("#loading_button").addClass("is-loading");
	});

	$(document).ajaxStop(function() {
		$("#loading_button").removeClass("is-loading");
	});

	//Bind the enter key to the search button upon typing into the search box.
	$('.search_input').keypress(function(event){
		if(event.keyCode == 13){
			$('#search_btn').click();
		}
	});

	// Remove the lat, long values when the user erases the location input.
	$("#loc_autocomplete").on("keyup", function() {
		if(!$("#loc_autocomplete").val().trim().length > 0) {
			$("#latitude").val("");
			$("#longitude").val("");
		}
	});

	// Start a search when the search button is hit.
	$("#search_btn").unbind().click(function() {
		$("#loading_button").css("display", "inline-block");
		$(window).scrollTop(0);
		search_tweets();
	});
});
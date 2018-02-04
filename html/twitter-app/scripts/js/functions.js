var nextSetOfTweets = "";
var foundTweetCount = 0;
var neededTweets = 0;

var MOBILE_WIDTH = 1024;

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
		if(!val.possibly_sensitive) {
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

			foundTweetCount+=1;
		}
	});
	
	if($(window).width() > MOBILE_WIDTH) {
		$("#results").shapeshift({
			animationSpeed: 100
		});
	}
}

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

function search_tweets() {
	foundTweetCount = 0;
	$("#search_btn").addClass("is-loading");
	$("#results").html("");
	$("#results").css("height", "0px");
	$("#results").trigger("ss-destroy");
	nextSetOfTweets = "";

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
			if($(window).width() < MOBILE_WIDTH) {
				toggle_search();
			}

			if(jsonData.search_metadata.hasOwnProperty("next_results")) {
				nextSetOfTweets = jsonData.search_metadata.next_results;
			}else {
				nextSetOfTweets = "";
			}

			buildTweetCard(jsonData);
		}
	}).then(function() {
		$("#search_btn").removeClass("is-loading");

		$("#found_results").html("Showing "+foundTweetCount+" tweets (scroll down to load more).");
	});
}

function load_more_tweets() {
	if(nextSetOfTweets) {
		$.get("scripts/php/search_twitter.php?next_results="+encodeURIComponent(nextSetOfTweets), function(response) {
			// console.log(response);
			var jsonData = JSON.parse(response);
			$("#error_result").html("");
			// console.log(JSON.parse(response));
			if(jsonData.hasOwnProperty('errors')) { 
				$.each(jsonData.errors, function(key, val) {
					$("#error_result").append(val.message + "<br>");
				});
				return false;
			}else {
				if(jsonData.search_metadata.hasOwnProperty("next_results")) {
					nextSetOfTweets = jsonData.search_metadata.next_results;
				}else {
					nextSetOfTweets = "";
				}
				
				buildTweetCard(jsonData);
			}
		}).then(function() {
			$("#found_results").html("Showing "+foundTweetCount+" tweets (scroll down to load more).");
		});
	}else {
		$("#loading_button").css("display", "none");
	}
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
	foundTweetCount = 0;
	$("#loading_button").css("display", "inline-block");

	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var lat_long_str = position.coords.latitude + "," + position.coords.longitude + ",5mi";
			$("#results").html("");
			$("#results").css("height", "0px");
			$.get("scripts/php/search_twitter.php?geo="+lat_long_str, function(response) {
				jsonData = JSON.parse(response);
				// console.log(response);
				// console.log(jsonData);
				nextSetOfTweets = jsonData.search_metadata.next_results;
				buildTweetCard(jsonData);
			}).then(function() {
				$("#found_results").html("Showing "+foundTweetCount+" tweets (scroll down to load more).");
			});
		});
	}
}

function hide_modal() {
    $("#post_tweet").removeClass("is-active");
}

function show_modal() {
    $("#post_tweet").addClass("is-active");

    $("#post_tweet_btn").unbind().click(function() {
    	$("#post_tweet_btn").prop("disabled", true);
    	$("#post_tweet_btn").addClass("is-loading");
    	
    	$.post("scripts/php/post_tweet.php", {tweet: $("#status").val()}, function(response) {
    		// console.log(response);
    		json = JSON.parse(response);
    	}).then(function() {
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

function remove_tweet(ref) {
	foundTweetCount -= 1;

	$(ref.parentNode.parentNode).css('display', 'none'); $('#results').trigger('ss-rearrange');
	$("#found_results").html("Showing "+foundTweetCount+" tweets (scroll down to load more).");
}

$(document).ready(function() {
	$(window).scroll(function() {
		if($(window).scrollTop() + $(window).height() >= $(document).height()) {
			if(nextSetOfTweets) {
				load_more_tweets();
			}else {
				$("#loading_button").css("display", "none");
			}
		}
	});

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

	$("#loc_autocomplete").on("keyup", function() {
		if(!$("#loc_autocomplete").val().trim().length > 0) {
			$("#latitude").val("");
			$("#longitude").val("");
		}
	});

	$("#search_btn").unbind().click(function() {
		$("#loading_button").css("display", "inline-block");
		$(window).scrollTop(0);
		search_tweets();
	});
});
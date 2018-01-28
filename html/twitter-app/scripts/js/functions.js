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

	//Search and set user mention hashtags.
	tweetString = tweetString.replace(/#([A-Za-z0-9-_]+)/g, function(foundString) {
		return foundString.link("https://twitter.com/search?src=typd&q=%23"+foundString.replace("#", ""));
	});

	return tweetString;
}

function buildTweetCard(jsonData) {
	tweetData = JSON.parse(jsonData);

	$.each(tweetData.statuses, function(key, val) {
		if(val.in_reply_to_status_id == null) {
			rtweeted = false;
			retweeted_text = "";
			original_retweeter = "";
			rt_id = "";
			full_text = "";

			if(val.hasOwnProperty('retweeted_status')) {
				rtweeted = true;
				original_retweeter = val.retweeted_status.user.screen_name;
				full_text = buildTwitterString(val.retweeted_status);
			}else {
				full_text = buildTwitterString(val);
			}

			cardData = {
				realname: val.user.name,
				screenname: '@'+val.user.screen_name,
				content: full_text,
				retweeted: rtweeted,
				profile_pic: val.user.profile_image_url_https,
				original_tweeter: original_retweeter,
				media: val.entities.hasOwnProperty('media') ? val.extended_entities.media : "",
				retweet_count: val.retweet_count,
				tweet_id: val.id_str
			};

			cardTemplate = $("#tweet-card-template").html();

			card = Mustache.render(cardTemplate, cardData);

			$("#results").append(card);
		}
	});
}

function get_tweets_in_area() {
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			lat_long_str = position.coords.latitude + "," + position.coords.longitude + ",5mi";

			$.get("scripts/php/search_twitter.php?geo="+lat_long_str, function(response) {
				console.log(JSON.parse(response));
				buildTweetCard(response);
			});
		});
	}
}

$(document).ready(function() {
	//Bind the enter key to the search button upon typing into the search box.
	$('#search_box').keypress(function(event){
		if(event.keyCode == 13){
			$('#search_btn').click();
		}
	});

	get_tweets_in_area()

	$("#search_btn").unbind().click(function() {
		$("#search_btn").addClass("is-loading");
		$("#results").html("");
		$.get("scripts/php/search_twitter.php?q="+$("#search_box").val(), function(response) {
			// $("#results").html(response);
			console.log(JSON.parse(response));
			buildTweetCard(response);
		}).then(function() {
			$("#search_btn").removeClass("is-loading");
		});
	});
});
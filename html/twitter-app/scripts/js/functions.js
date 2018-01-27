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
				retweeted_text = val.retweeted_status.full_text;
				$.each(val.retweeted_status.entities.user_mentions, function(k, v) {
					retweeted_text = retweeted_text.slice(0, v.indices[0]) + "<a href='https://twitter.com/"+v.screen_name+"'>"+retweeted_text.slice(v.indices[0], v.indices[1]) + "</a>"+retweeted_text.slice(v.indices[1]);
				});
				original_retweeter = val.retweeted_status.user.screen_name;
				rt_id = val.retweeted_status.id_str;
			}

			full_text = val.full_text;

			$.each(val.entities.user_mentions, function(k, v) {
				full_text = full_text.slice(0, v.indices[0]) + "<a href='https://twitter.com/"+v.screen_name+"'>"+full_text.slice(v.indices[0], v.indices[1]) + "</a>"+full_text.slice(v.indices[1]);
			});
			cardData = {
				realname: val.user.name,
				screenname: '@'+val.user.screen_name,
				content: full_text,
				retweeted: rtweeted,
				rt_text: retweeted_text,
				profile_pic: val.user.profile_image_url_https,
				original_tweeter: original_retweeter,
				rt_id: rt_id,
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
function buildTweetCard(jsonData) {
	tweetData = JSON.parse(jsonData);

	$.each(tweetData.statuses, function(key, val) {
		rtweeted = false;
		retweeted_text = "";
		original_retweeter = "";
		rt_id = "";

		if(val.hasOwnProperty('retweeted_status')) {
			rtweeted = true;
			retweeted_text = val.retweeted_status.full_text;
			original_retweeter = val.retweeted_status.user.screen_name;
			rt_id = val.retweeted_status.id_str;
		}

		cardData = {
			realname: val.user.name,
			screenname: '@'+val.user.screen_name,
			content: val.full_text,
			retweeted: rtweeted,
			rt_text: retweeted_text,
			profile_pic: val.user.profile_image_url_https,
			original_tweeter: original_retweeter,
			rt_id: rt_id
		};

		cardTemplate = $("#tweet-card-template").html();

		card = Mustache.render(cardTemplate, cardData);

		$("#results").append(card);
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
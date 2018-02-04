<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	session_name('twitter-app');
	session_start();

	include("../../twitter-app/Auth.php");

	if(isset($_GET["logout"]) && $_GET["logout"] == 1) {
		unset($_SESSION["oauth_user_token"]);
		unset($_SESSION["oauth_user_token_secret"]);
		unset($_SESSION["username"]);
	}

	$auth = new Auth();

	//Check if the access token has been acquired, if not, then request it and store it.
	if(!isset($_SESSION['access_token'])) { 
		$auth->request_app_only_access_token(); 
	}
?>

<!DOCTYPE html>

<html class="has-navbar-fixed-top">
	<head>
		<title>DM-Twitter-App</title>
		<meta charset='utf-8' />
		<link rel="stylesheet" type="text/css" href="styles/bulma.css">
		<link rel="stylesheet" type="text/css" href="styles/style.css">

		<script src="https://use.fontawesome.com/d7cec055af.js"></script>
		<script src="scripts/js/jquery-3.3.1.min.js"></script>
		<script src="scripts/js/jquery-ui.min.js"></script>
		<script src="scripts/js/jquery.shapeshift.min.js"></script>
		<script src="scripts/js/mustache.min.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAjb4PSWxisxqge658jRMA4AWlyRe5jeRc&libraries=places&callback=getGeolocation" async defer></script>
		<script src="scripts/js/functions.js"></script>
	</head>

	<body>
		<nav class="navbar is-dark is-fixed-top" role="navigation" aria-label="main-navigation">
			<div class="navbar-start">
				<div class="navbar-brand">
					<a href="index.php" class="navbar-item" style="font-size: 14pt;"><b>Twitter Viewer</b></a>

					<button class="button navbar-burger is-dark">
						<span></span>
						<span></span>
						<span></span>
					</button>
				</div>
				<a class="navbar-item is-hidden-touch" onclick="get_tweets_in_current_area();">View Tweets in my Area</a>
				<?php if(isset($_SESSION['oauth_user_token'])) { ?>
					<a onclick="show_modal();" class="navbar-item is-hidden-touch">Post a Tweet</a>
				<?php } ?>
			</div>

			<div class="navbar-end">
				<?php if(isset($_SESSION['oauth_user_token']) && isset($_SESSION["username"])) { ?>
					<span class="navbar-item is-hidden-touch">Logged in as <?php echo "@".$_SESSION["username"]; ?></span>&nbsp;<a href="index.php?logout=1" class="navbar-item is-hidden-touch"><i class="fa fa-twitter"></i>&nbsp;Sign Out?</a>
				<?php }else { ?>
					<a href="oauth/request_token.php" class="navbar-item is-hidden-touch"><i class="fa fa-twitter"></i>&nbsp;Sign in with Twitter</a>
				<?php } ?>
			</div>
		</nav>

		<div id="results_hero">
			<div id="search_results">

			</div>
		</div>

		<div id="search_menu" class="box">
			<div id="search_items">
				<h4 class="title is-4" style="color: white;">Search</h4>
				<div id="error_result" style="color: #ff2121; font-size: 11pt; margin-bottom: 10px;"></div>
				<div class="control has-icons-left">
					<input id="username" type="text" class="input search_input" placeholder="Twitter User">
					<span class="icon is-left"><i class="fa fa-at"></i></span>
				</div>

				<div class="control has-icons-left">
					<input id="search_box" type="text" class="input search_input" placeholder="Search Twitter">
					<span class="icon is-left"><i class="fa fa-search"></i></span>
				</div>

				<div class="control has-icons-left">
					<input id="loc_autocomplete" type="text" class="input search_input" placeholder="Location">
					<input id="latitude" type="hidden">
					<input id="longitude" type="hidden">
					<span class="icon is-left"><i class="fa fa-map-marker"></i></span>
				</div>
				<input id="tweet_count" type="number" min="1" max="100" class="input search_input" placeholder="Number of Tweets" value="25">
				<button id="search_btn" class="button navbar-item"><i class="fa fa-search"></i></button>
			</div>
		</div>

		<div id="main_container">
			<div id="content">
				<div id="results">
					<?php if(isset($_SESSION['access_token'])) { ?>
						<span style="font-size: 20pt;">No Data to show, please make a search.</span>
					<?php }else{ ?>
						<span style="font-size: 20pt; color: red;">You can not make a search at this time, please try again later.<br>If this problem persists, please contact the Administrator</span>
					<?php } ?>
				</div>
				<div style="text-align: center; margin: 0 auto; width: 400px"><button class="button is-outline">Load More</button></div>
			</div>
		</div>

		<div id="post_tweet" class="modal">
			<div class="modal-background" onclick="hide_modal()"></div>
			<div class="modal-content">
				<div class="box">
					<label class="label">Please enter your tweet below.</label>
					<textarea id="status" class="textarea" maxlength="280"></textarea>
					<hr>
					<button id="post_tweet_btn" class="button is-primary">Post Tweet</button>
					<button class="button is-danger" onclick="hide_modal()">Close</button>
				</div>
			</div>
			<button class="modal-close is-large" aria-label="close" onclick="hide_modal()"></button>
		</div>
	</body>

	<!-- Define a template for each card, This data will be used in Javascript. -->
	<script id="tweet-card-template" type="text/template">
		<div class="card tweet item">
			<div style="display: inline-block; float: right;">
				<a onclick="$(this.parentNode.parentNode).css('display', 'none'); $('#results').trigger('ss-rearrange');" class="card-header-icon" aria-label="remove">
					<span class="icon"><i style="color: red; font-size: 14pt;" class="fa fa-times"></i></span>
				</a>
			</div>
			<div class="card-content">
				<div class="media">
					<div class="media-left">
						<figure class="image is-48x48">
							<img src="{{ profile_pic }}" alt="Pic" style="border-radius: 50%">
						</figure>
					</div>
					<div class="media-content">
						<p class="title is-5">{{realname}}</p>
						<p class="subtitle is-6"><a href="https://twitter.com/{{screenname}}">{{screenname}}</a></p>
					</div>
				</div>
				<div class="content">
					{{#retweeted}}
						<b><i>Retweeted <a href="https://twitter.com/{{original_tweeter}}">@{{original_tweeter}}</a></i></b>
						<b><i><span class="retweet-content">{{{content}}}</span></i></b>
					{{/retweeted}}
					{{^retweeted}}
						<span class="tweet-content">{{{content}}}</span>
					{{/retweeted}}
					{{#media}}
						{{#video_info}}
							<video onloadeddata="$('#results').trigger('ss-rearrange');" class="media_video" onclick="this.paused ? this.play() : this.pause();" controls muted loop>
								{{#variants}}
									<source src="{{url}}" type="{{content_type}}">
								{{/variants}}
							  Your browser does not support video.
							</video>
						{{/video_info}}
						{{^video_info}}
							<a onclick="window.open('{{media_url_https}}');"><img onload="$('#results').trigger('ss-rearrange');" src="{{media_url_https}}" alt="{{media_url_https}}"></a>
						{{/video_info}}
					{{/media}}
				</div>
				<div style="margin-top: 10px;">
					{{created_date}}
				</div>
				<div style="margin-top: 10px;">
					<div style="display: inline-block;"><i class="fa fa-retweet"></i>&nbsp;{{retweet_count}}</div>
					<div style="display: inline-block; float: right"><a onclick="window.open('https://twitter.com/{{screenname}}/status/{{tweet_id}}');"><i class="fa fa-twitter"></i>Open in Twitter</a></div>
				</div>
			</div>
		</div>
	</script>
</html>
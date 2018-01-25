<?php
	session_name('twitter-app');
	session_start();

	include("../../twitter-app/Auth.php");

	use GuzzleHttp\Client;

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
		<script src="scripts/js/mustache.min.js"></script>
		<script src="scripts/js/functions.js"></script>
	</head>

	<body>
		<nav class="navbar is-dark is-fixed-top" role="navigation" aria-label="main-navigation">
			<div class="navbar-start">
				<div class="navbar-brand">
					<a href="index.php" class="navbar-item" style="font-size: 14pt;"><b>Twitter Viewer</b></a>
				</div>
				<a class="navbar-item" href="index.php">Post a Tweet</a>
			</div>

			<div class="navbar-end">
				<div class="navbar-item ">
					<input id="search_box" type="text" class="input" placeholder="Search Term">
					<button id="search_btn" class="button navbar-item"><i class="fa fa-search"></i></button>
				</div>
				<a href="#" class="navbar-item"><i class="fa fa-twitter"></i>&nbsp;Sign in with Twitter</a>
			</div>
		</nav>

		<div id="main_container">
			<div id="content">
				<div id="results">
				</div>
			</div>
		</div>
	</body>

	<!-- Define a template for each card, This data will be used in Javascript. -->
	<script id="tweet-card-template" type="text/template">
		<div class="card tweet">
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
						<b><i>Retweeted <a href="https://twitter.com/{{original_tweeter}}">@{{original_tweeter}}</a></i><b>
						<a href="https://twitter.com/{{original_tweeter}}/status/{{rt_id}}"><div class='retweet'>
							<p><i>"{{rt_text}}"</i></p>
						</div></a>
					{{/retweeted}}
					{{^retweeted}}
						<p>{{content}}</p>
					{{/retweeted}}
				</div>
			</div>
		</div>
	</script>
</html>
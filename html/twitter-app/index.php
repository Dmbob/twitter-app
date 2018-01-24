<?php
	session_name('twitter-app');
	session_start();

	include("../../twitter-app/Auth.php");
	include("../../twitter-app/TwitterRequest.php");

	use GuzzleHttp\Client;

	$auth = new Auth();

	if(!isset($_SESSION['access_token'])) { 
		$auth->request_app_only_access_token(); 
	}

	$twitter = new TwitterRequest();
?>

<!DOCTYPE html>

<html class="has-navbar-fixed-top">
	<head>
		<title>DM-Twitter-App</title>
		<meta charset='utf-8' />
		<link rel="stylesheet" type="text/css" href="styles/style.css">
		<link rel="stylesheet" type="text/css" href="styles/bulma.css">

		<script src="https://use.fontawesome.com/d7cec055af.js"></script>
		<script src="scripts/js/jquery-3.3.1.min.js"></script>
		<script src="scripts/js/functions.js"></script>
	</head>

	<body>
		<nav class="navbar is-dark is-fixed-top" role="navigation" aria-label="main-navigation">
			<div class="navbar-start">
				<div class="navbar-brand">
					<a href="index.php" class="navbar-item" style="font-size: 14pt;"><b>Twitter Viewer</b></a>
				</div>
				<a class="navbar-item" href="index.php">Post a Tweet</a>
				<div class="navbar-item">
					<div class="control has-icons-left">
						<input id="search_box" type="text" class="input" placeholder="Username">
						<span class="icon is-left" style="font-size: 14pt;"><i class="fa fa-at"></i></span>
					</div>
					<input id="tweet_number" type="number" class="input navbar-item" placeholder="# of Tweets">
					<button id="search_btn" class="button navbar-item"><i class="fa fa-search"></i></button>
				</div>
			</div>

			<div class="navbar-end">
				<a href="#" class="navbar-item"><i class="fa fa-twitter"></i>&nbsp;Sign in with Twitter</a>
			</div>
		</nav>

		<div id="main_container" class="columns">

			<!--<div id="menu" class="column is-one-fifth">
				<aside id="menu" class="menu">
					<ul class="menu-list">
						<li>
							<h4 class="title is-4" >Search</h4>
							<div class="control has-icons-left" style="display: inline-block; width: 75%; padding-right: 0;">
								<input id="search_box" type="text" class="input" placeholder="Username">
								<span class="icon is-left" style="height: 100%; font-size: 16pt;"><i class="fa fa-at"></i></span>
							</div>
							<button id="search_btn" class="button"><i class="fa fa-search"></i></button>
						</li>
					</ul>
				</aside>-->
				<!--<div id="search">
					<h4 class="title is-4" style="color: white">Search</h4>
					<div class="control has-icons-left" style="display: inline-block; width: 75%; padding-right: 0;">
						<input id="search_box" type="text" class="input is-dark" placeholder="Username">
						<span class="icon is-left" style="height: 100%; font-size: 16pt;"><i class="fa fa-at"></i></span>
					</div>
					<button id="search_btn" class="button"><i class="fa fa-search"></i></button>
				</div>
			</div>-->

			<div id="content" class="column">
				<div id="results">
					<?php //echo $twitter->get_user_timeline("notch"); ?>
				</div>
			</div>
		</div>
	</body>
</html>
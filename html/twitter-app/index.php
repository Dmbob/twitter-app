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

<html>
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
		<nav class="navbar is-dark" role="navigation" aria-label="main-navigation">
			<div class="navbar-start">
				<div class="navbar-brand">
					<a href="index.php" class="navbar-item" style="font-size: 14pt;"><b>Twitter Viewer</b></a>
				</div>
				<a class="navbar-item" href="index.php">Post a Tweet</a>
			</div>

			<div class="navbar-end">
				<a href="#" class="navbar-item"><i class="fa fa-twitter"></i>&nbsp;Sign in with Twitter</a>
			</div>
		</nav>

		<div id="search">
			<div class="control has-icons-left" style="display: inline-block; width: 75%">
				<input id="search_box" type="text" class="input is-dark" placeholder="Username">
				<span class="icon is-left" style="font-size: 16pt;">@</span>
			</div>
			<button id="search_btn" class="button">Search</button>
		</div>
		<div id="response"><?php //echo $twitter->get_user_timeline("notch"); ?></div>
	</body>
</html>
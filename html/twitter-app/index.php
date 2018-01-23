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
	</head>

	<body>
		<div id="response"><?php echo $twitter->make_application_request('search/tweets.json?q=@notch', 'GET'); ?></div>
	</body>
</html>
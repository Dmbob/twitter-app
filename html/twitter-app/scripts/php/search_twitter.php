<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	include("../../../../twitter-app/TwitterRequest.php");

	$twitter = new TwitterRequest();

	//Look at the first character of the search term and see if it is a special Twitter character.

/*	if(isset($_GET['user'])) {
		echo $twitter->get_user_timeline(urlencode($_GET['user']), urlencode($_GET['q']), urlencode($_GET['geo']));
	}else {
		echo $twitter->search_tweets(urlencode($_GET['q']), urlencode($_GET['geo']));
	}*/

	$user = isset($_GET['user']) ? $_GET['user'] : "";
	$query = isset($_GET['q']) ? $_GET['q']  : "";
	$geo = isset($_GET['geo']) ? $_GET['geo'] : "";

	echo $twitter->search_tweets(urlencode($user), urlencode($query), urlencode($geo));
?>
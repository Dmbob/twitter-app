<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	include("../../../../twitter-app/TwitterRequest.php");

	$twitter = new TwitterRequest();

	//Look at the first character of the search term and see if it is a special Twitter character.
	if(isset($_GET['q'])) {
		switch($_GET['q'][0]) {
			case '@':
				echo $twitter->search_tweets($_GET['q']);
				break;

			case '#':
				echo $twitter->search_tweets(urlencode($_GET['q']));
				break;

			default:
				echo $twitter->search_tweets($_GET['q']);
				//echo $twitter->search_tweets($_GET['q']);
				break;
		}
	}else if(isset($_GET["geo"])) {
		echo $twitter->search_tweets_by_geocode($_GET['geo']);
	}
?>
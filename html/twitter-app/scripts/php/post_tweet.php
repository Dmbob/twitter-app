<?php
/*
* This is more of a helper script to call the TwitterRequest class, and request to post a tweet.
*
* Author: Bob Henley
*/
	include("../../../../twitter-app/TwitterRequest.php");

	$twitter = new TwitterRequest();

	if(isset($_POST["tweet"])) {
		echo $twitter->post_tweet(htmlspecialchars($_POST["tweet"]));
	}
?>
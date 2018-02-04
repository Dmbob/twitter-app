<?php
	include("../../../../twitter-app/TwitterRequest.php");

	$twitter = new TwitterRequest();

	if(isset($_POST["tweet"])) {
		echo $twitter->post_tweet(htmlspecialchars($_POST["tweet"]));
	}
?>
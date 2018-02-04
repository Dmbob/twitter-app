<?php
	include("../../../../twitter-app/TwitterRequest.php");

	$twitter = new TwitterRequest();

	if(isset($_GET["next_results"])) {
		echo $twitter->load_more_tweets($_GET["next_results"]);
	}else {
		$user = isset($_GET['user']) ? htmlspecialchars($_GET['user']) : "";
		$query = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : "";
		$geo = isset($_GET['geo']) ? htmlspecialchars($_GET['geo']) : "";
		$count = isset($_GET['count']) ? htmlspecialchars($_GET["count"]) : "25";

		echo $twitter->search_tweets(urlencode($user), urlencode($query), urlencode($geo), urlencode($count));
	}
?>
<?php
/*
* This is more of a helper script to call the TwitterRequest class, and request the search data with the given search terms.
*
* Author: Bob Henley
*/
	include("../../../../twitter-app/TwitterRequest.php");

	$twitter = new TwitterRequest();

	// Check if we are loading more tweets or making an actual search.
	if(isset($_GET["next_results"])) {
		echo $twitter->load_more_tweets($_GET["next_results"]);
	}else {
		// Check if any of the data is empty.
		$user = isset($_GET['user']) ? htmlspecialchars($_GET['user']) : "";
		$query = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : "";
		$geo = isset($_GET['geo']) ? htmlspecialchars($_GET['geo']) : "";
		$count = isset($_GET['count']) ? htmlspecialchars($_GET["count"]) : "25";

		echo $twitter->search_tweets(urlencode($user), urlencode($query), urlencode($geo), urlencode($count));
	}
?>
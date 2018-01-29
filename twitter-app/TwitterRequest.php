<?php
/*
* This class will include methods to interact with the Twitter API.
*
* Author: Bob Henley
*/
session_name('twitter-app');
session_start();

require_once("config/config.php");
use GuzzleHttp\Client;

class TwitterRequest {

	protected $twitter_client;

	//Contructor that takes a GuzzleHttp client as a parameter and sets the global variable.
	public function __construct() {
		$this->twitter_client = new Client([
			'base_uri' => 'https://api.twitter.com/1.1/', 
			'timeout' => 2.0, 
			'headers' => [
				'User-Agent' => 'twitter-app/1.0',
				'Authorization' => 'Bearer '.$_SESSION['access_token'], 
				'Accept-Encoding' => 'gzip'
		]]);
	}

	//Function to make and authorize a request to the Twitter API from the application.
	protected function make_application_request($api_string, $api_method, $options = []) {
		$response = $this->twitter_client->request($api_method, $api_string, $options);

		return $response->getBody();
	}

	protected function build_search_query($user, $search_term, $geolocation) {
		// Check if the user or location are empty, and if they are, do not include them in the query.
		$user_param = empty($user) ? "" : "from%3A".$user."%20";
		$geo_param = empty($geolocation) ?  "" : "&geocode=".$geolocation;

		return $user_param.$search_term.$geo_param;
	}

	public function search_tweets_by_geocode($geocode_str) {
		return $this->make_application_request('search/tweets.json?geocode='.$geocode_str.'&tweet_mode=extended&lang=en', 'GET');
	}

	public function get_user_timeline($user) {
		return $this->make_application_request('statuses/user_timeline.json?screen_name='.$user.'&tweet_mode=extended&lang=en', 'GET');
	}

	public function search_tweets($user, $search_term, $geolocation) {
		$search_parameters = $this->build_search_query($user, $search_term, $geolocation);
		return $this->make_application_request('search/tweets.json?q='.$search_parameters.'&tweet_mode=extended&count=25&lang=en', 'GET');
	}
}

?>
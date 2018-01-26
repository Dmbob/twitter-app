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

	public function search_tweets($search_term) {
		return $this->make_application_request('search/tweets.json?q='.$search_term.'&tweet_mode=extended&count=100&lang=en', 'GET');
	}

	public function search_tweets_by_geocode($geocode_str) {
		return $this->make_application_request('search/tweets.json?geocode='.$geocode_str.'&tweet_mode=extended&lang=en', 'GET');
	}

	public function get_user_timeline($user) {
		return $this->make_application_request('statuses/user_timeline.json?screen_name='.$user.'&tweet_mode=extended&lang=en', 'GET');
	}
}

?>
<?php
/*
* This class will include methods to interact with the Twitter API.
*
* Author: Bob Henley
*/
session_name('twitter-app');
session_start();

require_once("config/config.php");
include("Auth.php");

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TwitterRequest {

	protected $url = 'https://api.twitter.com/1.1/';
	protected $twitter_client;
	protected $auth_client;

	//Contructor that takes a GuzzleHttp client as a parameter and sets the global variable.
	public function __construct() {
		$this->twitter_client = new Client([
			'base_uri' => $this->url, 
			'timeout' => 2.0, 
			'headers' => [
				'User-Agent' => 'twitter-app/1.0', 
				'Accept-Encoding' => 'gzip'
		]]);

		$this->auth_client = new Auth();
	}

	//Function to make and authorize a request to the Twitter API from the application.
	protected function make_application_request($api_string, $api_method) {
		try {
			$response = $this->twitter_client->request($api_method, $api_string, [
				'headers' => [
					'Authorization' => 'Bearer '.$_SESSION['access_token']
				]
			]);

			return $response->getBody();
		}catch(RequestException $e) {
			return $e->getResponse()->getBody();
		}
	}

	protected function make_user_request($api_string, $api_method) {
		if(isset($_SESSION["oauth_user_token"]) && isset($_SESSION["oauth_user_token_secret"])) {
			$oauth_user_token = $_SESSION["oauth_user_token"];
			$oauth_user_token_secret = $_SESSION["oauth_user_token_secret"];
			$params = explode("?", $api_string)[1];

			try {
				$response = $this->twitter_client->request($api_method, $api_string, [
					'headers' => [
						'body' => "status=This is a test",
						'Authorization' => $this->auth_client->generate_auth_header($this->url.$api_string, $oauth_user_token, $oauth_user_token_secret, "status=This is a test")
					]
				]);

				return $response->getBody();
			}catch(RequestException $e) {
				return $e->getResponse()->getBody();
			}
		}else {
			return json_encode(array("errors" => ["message" => "The user must be authorized"]));
		}
	}

	protected function build_search_query($user, $search_term, $geolocation) {
		// Check if the user or location are empty, and if they are, do not include them in the query.
		$user_param = empty($user) ? "" : "from%3A".$user."%20";
		$geo_param = empty($geolocation) ?  "" : "&geocode=".$geolocation;

		return $user_param.$search_term.$geo_param;
	}

	protected function user_exists($user) {
		if(empty($user)) {
			return json_encode("{'user_exists': true}");
		}else {
			$request = $this->make_application_request('users/lookup.json?screen_name='.$user, 'GET');
			return $request;
		}
	}

	public function search_tweets($user, $search_term, $geolocation, $count) {
		$user_exists = json_decode($this->user_exists($user), true);

		if(isset($user_exists["errors"])) {
			return json_encode($user_exists);
		}else {
			$search_parameters = $this->build_search_query($user, $search_term, $geolocation);
			return $this->make_application_request('search/tweets.json?q='.$search_parameters.'&count='.$count.'&tweet_mode=extended&lang=en', 'GET');
		}
	}

	public function load_more_tweets($params) {
		if(!empty($params)) {
			return $this->make_application_request('search/tweets.json'.$params.'&tweet_mode=extended', 'GET');
		}
	}

	public function post_tweet($tweet_message) {
		return $this->make_user_request("statuses/update.json", 'POST');
	}
}

?>
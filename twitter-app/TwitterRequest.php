<?php
/*
* This class includes functions to interact with the Twitter API.
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

	/*
	* Contructor that takes a GuzzleHttp client as a parameter and sets the global variable.
	*/
	public function __construct() {
		$this->twitter_client = new Client([
			'base_uri' => $this->url, 
			'timeout' => 2.0, 
		]);

		$this->auth_client = new Auth();
	}

	/*
	* Function to make and authorize a request to the Twitter API from the application.
	*
	* @param $api_string The function to call in the Twitter API.
	* @param $api_method The request method being used.
	*
	* @returns The response for the given request.
	*/
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

	/*
	* This function makes and authorizes a request on a user basis rather than an application basis.
	*
	* @param $api_string The function to call in the Twitter API.
	* @param $api_method The request method being used.
	*
	* @returns The response for the given request.
	*/
	protected function make_user_request($api_string, $api_method) {
		// Check if the user's token and token secret are set.
		if(isset($_SESSION["oauth_user_token"]) && isset($_SESSION["oauth_user_token_secret"])) {
			$oauth_user_token = $_SESSION["oauth_user_token"];
			$oauth_user_token_secret = $_SESSION["oauth_user_token_secret"];

			// Seperate the url from the parameters for encoding.
			$url = explode("?", $api_string)[0];
			$url_params = explode("?", $api_string)[1];

			$encoded_params = array();

			$auth_data = array("oauth_token" => $oauth_user_token);

			$data = array();

			// Encode the url parameters.
			foreach(explode("&", $url_params) as $params) {
				list($key, $value) = explode("=", $params);
				$data = array_merge($data, [$key => $value]);

				array_push($encoded_params, $key."=".rawurlencode($value));
			}

			$encoded_params_str = implode("&", $encoded_params);

			$request_url = $url . "?" . $encoded_params_str;
			
			try {
				$response = $this->twitter_client->request($api_method, $request_url, [
					'headers' => [
						'Accept' => '*/*',
						'Connection' => 'close',
						'User-Agent' => 'twitter-app/1.0',
						'Content-Type' => 'application/x-www-form-urlencoded',
						'Authorization' => $this->auth_client->generate_auth_header($this->url . $api_string, $oauth_user_token_secret, $auth_data, $data),
						'Accept-Encoding' => 'gzip'
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

	/*
	* This function will take search parameters, and make them into a Twitter API friendly string.
	*
	* @param $user The user for the specified search query.
	* @param $search_term The actual search term for the specified search query.
	* @param $geolocation The geolocation for the specified search query.
	*
	* @returns The Twitter API friendly search string.
	*/
	protected function build_search_query($user, $search_term, $geolocation) {
		// Check if the user or location are empty, and if they are, do not include them in the query.
		$user_param = empty($user) ? "" : "from%3A".$user."%20";
		$geo_param = empty($geolocation) ?  "" : "&geocode=".$geolocation;

		return $user_param.$search_term.$geo_param;
	}

	/*
	* Check if the user exists in Twitter.
	*
	* @param $user The username for the user to check.
	*
	* @returns Whether the user exists in Twitter or not.
	*/
	protected function user_exists($user) {
		if(empty($user)) {
			return json_encode("{'user_exists': true}");
		}else {
			$request = $this->make_application_request('users/lookup.json?screen_name='.$user, 'GET');
			return $request;
		}
	}

	/*
	* This function calls the Twitter API with the given search terms, and searches for tweets with them.
	* This function will automatically filter out replies.
	*
	* @param $user The username for the user to search.
	* @param $search_term The actual search query.
	* @param $geolocation The location of the search.
	* @param $count The amount of tweets to get for the search
	*
	* @returns The response from the request containing all of the Tweets.
	*/
	public function search_tweets($user, $search_term, $geolocation, $count) {
		$user_exists = json_decode($this->user_exists($user), true);

		if(isset($user_exists["errors"])) {
			return json_encode($user_exists);
		}else {
			$search_parameters = $this->build_search_query($user, $search_term, $geolocation);
			return $this->make_application_request('search/tweets.json?q='.$search_parameters.'%20exclude%3Areplies&count='.$count.'&tweet_mode=extended&lang=en', 'GET');
		}
	}

	/*
	* This function will load more tweets with the given parameter string.
	*
	* @param $params The parameter string which will load more tweets using for the query.
	*
	* @returns The response from the request containing all of the next Tweets.
	*/
	public function load_more_tweets($params) {
		if(!empty($params)) {
			return $this->make_application_request('search/tweets.json'.$params.'&tweet_mode=extended', 'GET');
		}
	}

	/*
	* This function will post a tweet using user credintials 
	*
	* @param $tweet_message The message to post to the user's Twitter statuses.
	*
	* @returns The response from the request.
	*/
	public function post_tweet($tweet_message) {
		return $this->make_user_request("statuses/update.json?status=".$tweet_message, 'POST');
	}
}

?>
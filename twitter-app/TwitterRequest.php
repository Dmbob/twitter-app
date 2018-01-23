<?php
/*
* This class will include methods to interact with the Twitter API.
*
* Author: Bob Henley
*/
session_name('twitter-app');

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
	public function make_application_request($api_string, $api_method, $options = []) {
		$response = $this->twitter_client->request($api_method, $api_string, $options);

		return $response->getBody();
	}
}

?>
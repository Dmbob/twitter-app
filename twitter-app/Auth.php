<?php
/*
* This class will include methods to interact with Twitter OAuth on a User and Application level.
*
* Author: Bob Henley
*/
session_name('twitter-app');

require_once("config/config.php");
use GuzzleHttp\Client;

class Auth {

	protected $auth_client;

	//Contructor that takes a GuzzleHttp client as a parameter and sets the global variable.
	public function __construct() {
		$this->auth_client = new Client(['base_uri' => 'https://api.twitter.com/', 'timeout' => 2.0]);
	}

	//This function will encode the consumer key and consumer secret into a Base64 string for use in OAuth.
	protected function encode_consumer_info() {
		$auth_string = CONSUMER_KEY.":".CONSUMER_SECRET;
		$encoded_auth_string = "Basic ".base64_encode($auth_string);

		return $encoded_auth_string;
	}

	//Function returns the access toekn generated by our Consumer Key and Consumer Secret.
	public function request_app_only_access_token() {
		$response = $this->auth_client->request('POST', "oauth2/token", [
			'body' => 'grant_type=client_credentials',

			'headers' => [
				'User-Agent' => 'twitter-app/1.0',
				'Authorization' => $this->encode_consumer_info(),
				'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
				'Content-Length' => '29',
				'Accept-Encoding' => 'gzip'
			]
		]);

		$json = json_decode($response->getBody(), true);

		$_SESSION['access_token'] = $json['access_token'];
	}
}

?>
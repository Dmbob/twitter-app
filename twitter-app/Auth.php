<?php
/*
* This class will include methods to interact with Twitter OAuth on a User and Application level.
*
* Author: Bob Henley
*/
session_name('twitter-app');

require_once("config/config.php");

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Auth {

	protected $auth_client;
	protected $url = 'https://api.twitter.com/';

	/*
	* Contructor that takes a GuzzleHttp client as a parameter and sets the global variable.
	*/
	public function __construct() {
		$this->auth_client = new Client(['base_uri' => $this->url, 'timeout' => 2.0]);
	}

	/* 
	* Build a string for the OAuth signature with the givn parameter list.
	* Make sure to percent encode every key/value.
	* 
	* @param $params An array of parameters in $key => $value format.
	* @param $data Extra data that needs to be added to the parameters.
	*
	* @returns The parameter string for the OAuth signature.
	*/
	protected function build_parameter_string($params = [], $data) {
		$param_string = "";
		$params = array_merge($params, $data);
		ksort($params);

		$last_parameter = end($params);

		foreach($params as $key => $value) {
			$end_char = ($value == $last_parameter) ? "" : "&";
			$param_string .= rawurlencode($key)."=".rawurlencode($value).$end_char;
		}

		return $param_string;
	}

	/*
	* Generate the signature used in the authentication header 
	*
	* @param $url The url of the request.
	* @param $oauth_token_secret Empty if not specified, this is the user's access token secret for making OAuth requests as a user.
	* @param $parameters The parameters of the OAuth signature.
	* @param $data Extra data that needs to be added to the parameters.
	*
	* @returns The unencoded OAuth signature for the request.
	*/
	protected function generate_oauth_signature($url, $oauth_token_secret = "", $parameters = [], $data) {
		$parameter_string = $this->build_parameter_string($parameters, $data);

		$signature_base_string = "POST&".rawurlencode(explode("?", $url)[0])."&".rawurlencode($parameter_string);

		$signing_key = rawurlencode(CONSUMER_SECRET)."&".rawurlencode($oauth_token_secret);
		
		$oauth_signature = base64_encode(hash_hmac('sha1', $signature_base_string, $signing_key, true));

		return $oauth_signature;
	}

	/*
	* This function uses generates the Authorization header for a given request.
	*
	* @param $url The url for the request.
	* @param $oauth_token_secret Empty if not specified, this is the user's access token secret for making OAuth requests as a user.
	* @param $parameters The parameters of the header/request.
	* @param $data Extra data that needs to be added to the parameters.
	*
	* @returns The Authorization header for the request.
	*/
	public function generate_auth_header($url, $oauth_token_secret = "", $parameters = [], $data = []) {
		$date = new DateTime();
		$timestamp = $date->getTimestamp();
		$auth_header = "OAuth ";

		$parameters = array_merge($parameters, array(
			"oauth_timestamp" => $timestamp,
			"oauth_nonce" => hash('sha256', $timestamp),
			"oauth_consumer_key" => CONSUMER_KEY,
			"oauth_signature_method" => "HMAC-SHA1",
			"oauth_version" => "1.0"
		));


		$oauth_signature = $this->generate_oauth_signature($url, $oauth_token_secret, $parameters, $data);

		$parameters = array_merge($parameters, ["oauth_signature" => $oauth_signature]); 

		ksort($parameters);

		$last_parameter = end($parameters);

		foreach($parameters as $key => $value) {
			$end_char = ($value == $last_parameter) ? '"' : '", ';
			$auth_header .= rawurlencode($key).'="'.rawurlencode($value).$end_char;
		}

		return $auth_header;
	}

	/*
	* This function makes a request to the Twitter API for an Application-Only access token for use with
	* searching tweets, and stores it into a session variable.
	*/
	public function request_app_only_access_token() {
		try{ 
			$auth_string = CONSUMER_KEY.":".CONSUMER_SECRET;
			$encoded_auth_string = "Basic ".base64_encode($auth_string);

			$response = $this->auth_client->request('POST', "oauth2/token", [
				'body' => 'grant_type=client_credentials',

				'headers' => [
					'User-Agent' => 'twitter-app/1.0',
					'Authorization' => $encoded_auth_string,
					'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
					'Accept-Encoding' => 'gzip'
				]
			]);

			$json = json_decode($response->getBody(), true);

			$_SESSION['access_token'] = $json['access_token'];
		}catch(RequestException $e) {
		}
	}

	/*
	* This function will request a token used for a user to sign in. Once the user signs in, this
	* token will be used to generate an access token.
	*
	* @returns The request token.
	*/
	public function request_user_auth_request_token() {
		$response = $this->auth_client->request('POST', "oauth/request_token", [
			'headers' => [
				'Authorization' => $this->generate_auth_header("https://api.twitter.com/oauth/request_token", "", ["oauth_callback" => CALLBACK_URL])
			]
		]);

		$data = $response->getBody();

		$_SESSION["oauth_user_token_secret"] = explode('=', explode('&', $data)[1])[1];

		return explode('=', explode('&', $data)[0])[1];
	}

	/*
	* This function will request the access token for a user, so that a user may make, user-based
	* API calls to Twitter.
	*
	* @returns The response string containg the user's oauth_token, oauth_token_secret, and username.
	*/
	public function request_user_access_token($oauth_token, $oauth_verifier) {
		$response = $this->auth_client->request('POST', 'oauth/access_token?oauth_verifier='.$oauth_verifier, [
			'headers' => [
				'Authorization' => $this->generate_auth_header($this->url, "", ["oauth_token" => $oauth_token, "oauth_verifier" => $oauth_verifier])
			]
		]);

		return $response->getBody();
	}
}
?>
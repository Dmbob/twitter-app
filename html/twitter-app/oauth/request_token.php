<?php
/*
* This is the starting portion of the Sign in with Twitter feature. It will get a request token for the sign in redirect.
*
* Author: Bob Henley
*/
	session_name("twitter-app");
	session_start();

	include("../../../twitter-app/Auth.php");

	$auth = new Auth();

	// Get a request token from the Twitter API.
	$request_token = $auth->request_user_auth_request_token();

	// Redirect the user to sign in and authorize the app.
	echo "<script>window.location = 'https://api.twitter.com/oauth/authenticate?oauth_token=".$request_token."';</script>";
?>
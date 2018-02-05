<?php
/*
* This is the callback portion of the Sign in with Twitter feature for getting user credentials.
*
* Author: Bob Henley
*/
	session_name("twitter-app");
	session_start();

	include("../../../twitter-app/Auth.php");

	$auth = new Auth();

	if(isset($_GET["oauth_token"])) {
		// Request the access token for the user after they have signed in, and parse the response string and store them in Session variables.
		$user_token_str =  $auth->request_user_access_token($_GET["oauth_token"], $_GET["oauth_verifier"]);
		$_SESSION["oauth_user_token"] = explode("=", explode("&", $user_token_str)[0])[1];
		$_SESSION["oauth_user_token_secret"] = explode("=", explode("&", $user_token_str)[1])[1];
		$_SESSION["username"] = explode("=", explode("&", $user_token_str)[3])[1];
	}

	// Return to the main page.
	echo "<script> window.location='../'; </script>";
?>
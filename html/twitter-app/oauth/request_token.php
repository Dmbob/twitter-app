<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	session_name("twitter-app");
	session_start();

	include("../../../twitter-app/Auth.php");

	$auth = new Auth();

	$request_token = $auth->request_user_auth_request_token();

	echo "<script>window.location = 'https://api.twitter.com/oauth/authorize?oauth_token=".$request_token."';</script>";
?>
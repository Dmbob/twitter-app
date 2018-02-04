<?php
	session_name("twitter-app");
	session_start();

	include("../../../twitter-app/Auth.php");

	$auth = new Auth();

	if(isset($_GET["oauth_token"])) {
		$user_token_str =  $auth->request_user_access_token($_GET["oauth_token"], $_GET["oauth_verifier"]);
		$_SESSION["oauth_user_token"] = explode("=", explode("&", $user_token_str)[0])[1];
		$_SESSION["oauth_user_token_secret"] = explode("=", explode("&", $user_token_str)[1])[1];
		$_SESSION["username"] = explode("=", explode("&", $user_token_str)[3])[1];
	}

	echo "<script> window.location='../'; </script>";
?>
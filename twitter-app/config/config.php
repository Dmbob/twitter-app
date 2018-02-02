<?php
/*
* This file stores all of the configuration information for the application, such as secrect keys, and the includes for external libraries.
*
* Author: Bob Henley
*/
	
	//Define the secret keys used in the application.
	defined("CONSUMER_KEY") or define("CONSUMER_KEY", "63Bh9wTvMVeFOkTXG2gn7qr2M");
	defined("CONSUMER_SECRET") or define("CONSUMER_SECRET", "zL0xXfaQNSgYTlVpqaI4JFTHPyTzrWtKw3C94rwWolGJDlnhrF");
	defined("CALLBACK_URL") or define("CALLBACK_URL", "https://dmbob.guru/twitter-app/oauth/callback.php");

	//Include any libraries, and their namespaces.
	require('vendor/autoload.php');

?>
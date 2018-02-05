<?php
/*
* This file stores all of the configuration information for the application, such as secrect keys, and the includes for external libraries.
*
* Author: Bob Henley
*/
	
	//Define the secret keys used in the application.
	defined("CONSUMER_KEY") or define("CONSUMER_KEY", "SUj2OlRmJt4r7cNaKHZ27dtcg");
	defined("CONSUMER_SECRET") or define("CONSUMER_SECRET", "krIeNq4Q3kzVzjvJHlc9f2tQ7kjwMhYn34MLSHHiOb9DZOx4jD");
	defined("CALLBACK_URL") or define("CALLBACK_URL", "https://dmbob.guru/twitter-app/oauth/callback.php");

	//Include any libraries, and their namespaces.
	require('vendor/autoload.php');

?>
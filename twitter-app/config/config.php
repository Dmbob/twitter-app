<?php
/*
* This file stores all of the configuration information for the application, such as secrect keys, and the includes for external libraries.
*
* Author: Bob Henley
*/
	
	//Define the secret keys used in the application.
	defined("CONSUMER_KEY") or define("CONSUMER_KEY", "o6s4FJWb3FbPHlMKRBCOSdvnn");
	defined("CONSUMER_SECRET") or define("CONSUMER_SECRET", "04qzC8laJbtffKnjTuupOEcnrsrHF7syO6Ezk4Y2QsFJYhRSnx");

	//Include any libraries, and their namespaces.
	require('vendor/autoload.php');
	use GuzzleHttp\Client;

?>
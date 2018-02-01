<?php
/*
* This file stores all of the configuration information for the application, such as secrect keys, and the includes for external libraries.
*
* Author: Bob Henley
*/
	
	//Define the secret keys used in the application.
	defined("CONSUMER_KEY") or define("CONSUMER_KEY", "o6s4FJWb3FbPHlMKRBCOSdvnn");
	defined("CONSUMER_SECRET") or define("CONSUMER_SECRET", "04qzC8laJbtffKnjTuupOEcnrsrHF7syO6Ezk4Y2QsFJYhRSnx");
	defined("OAUTH_ACCESS_TOKEN") or define("OAUTH_ACCESS_TOKEN", "4855739068-DRKxudeajvyrwvDMIhrLHBvxSCCH2Wbr7hbM1RW");
	defined("OAUTH_ACCESS_TOKEN_SECRET") or define("OAUTH_ACCESS_TOKEN_SECRET", "fExuvLDkYn9evUoxpgK5lIc8FdzAQxaNgL3AJjuX7hlvw");
	defined("CALLBACK_URL") or define("CALLBACK_URL", "https://dmbob.guru/twitter-app/scripts/oauth_callback.php");

	//Include any libraries, and their namespaces.
	require('vendor/autoload.php');

?>
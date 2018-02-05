# Twitter App #

Application that allows a user to search for tweets based on username, hastags, or location. Functionality also includes the ability to log into twitter and post Tweets.

### Overview ###

I chose to create the Twitter App as my project. The reason that I chose to do this is because Twitter has a fairly extensive API, and I wanted to learn more about it.

I am currently hosting this site live at https://dmbob.guru/twitter-app/

### Usage ###

The first thing you should do after downloading this application is update the config file in 'twitter-app/config/config.php'. 

This file is what contains the Consumer Key and Consumer Key Secret for the application in Twitter, as well as the callback url for user based authentication. 

I have left 2 keys in there as well as a callback URL as an example. These keys will work, and are for testing purposes only. **They differ from the ones that I am using on my live site.**

Using this applicaion is fairly straightforward. You load the page, and it authenticates with an Application-Only Auth token, which allows you to immediately start making searches.

On a larger screen like a Desktop, the search menu will be on the right. On smaller screens, such as a tablet or smartphone, there will be a hamburger menu on the top right that you can hit to bring down the search menu. The search menu includes these search options:
* Username: The twitter username to search for.
* Search Term: The specified search term (hashtag, etc...) to search for.
* Location: An autocompleting location to search for a tweet from a specified location.
* Number of Tweets: The number of tweets to display for the search (Up to 100 per the Twitter APIs restrictions).

The search terms will be filtered dependant on what is entered in the fields. For example if you search for a user "twitterapi" with a search term "#updates" with the location "San Francisco, CA", then it will display tweets from the user "twitterapi" that contain "#updates", and that were made in San Francisco.
Another example would be if you left the serch term "#udpates" blank. This would pull in any Tweets made by the twitter api in San Francisco.

You can click on the "View Tweets in My Area" button at the top of the page, and it will use HTML5 Geolocation to get your location, and search for any tweets that were made in your area.
**Note: You need to have this site hosted on a server using SSL with HTTPS in order to search via location or use the HTML5 Geolocation feature.**

Once you load in the first set of Tweeets, you can keep scrolling down, and it will load more, until it can't load anymore.

You can drag/drop the tweet cards to different spots, and the others will move out of the way accordingly. You can also click on the 'x' on the top right of each one to remove them from view.

There is a Sign In with Twitter button on the top right. When this button is clicked, it generates a token and brings you to Twitter to sign in, if you have authorized the app, it will immediatly return you to the application, if you have not authorized it, Twitter will prompt you to do so now.

Once you are signed into the app via Twitter, you can click on the "Post a Tweet" button at the top, which will prompt you to enter a tweet.

### Operating System Choices ###

This project was developed on a server running Debian 8.9 with Apache 2, and PHP 7.0.

Libraries for PHP are handled with [Composer](https://getcomposer.org/) and the dependencies are in the 'twitter-app/config/composer.json' file.

Unlike the client-side Javascript libraries, the PHP libraries are not included in the repository. You will need to install composer, and run composer install in the 'twitter-app/config' directory.
**Note: If this for some reason fails. Delete the composer.lock file and try again.**

These libraries are required for PHP:
* [Guzzle HTTP](http://docs.guzzlephp.org/en/stable/) - Web Request Library
* [PHPUnit](https://phpunit.de/) - Unit Testing Library

These libraries are required and included for CSS/Javascript:
* [JQuery 3.3.1](https://jquery.com/)
* [JQuery UI](https://jqueryui.com/)
* [JQuery Shapeshift](https://github.com/AshesOfOwls/jquery.shapeshift)
* [Mustache](https://mustache.github.io/)
* [Bulma](https://bulma.io/)
* [Animate CSS](https://daneden.github.io/animate.css/)

**More on the libraries below.**

### Design Choices ###

I chose to go with a type of Masonry design for this website, mainly because I thought that it looked nice. Since these tweets did not have to be sorted, or ordered, this also seemed like a good decision.

### Libraries used in this project ###
* PHP Libraries
    * Guzzle HTTP
	    * Version: 6.0
        * Purpose: To streamline the process of making Web Requests in PHP.
    	* License: MIT License
	    * Website: http://docs.guzzlephp.org/en/stable/
	    
	* PHPUnit
	    * Version: 6
        * Purpose: Framework for Unit Testing in PHP
    	* License: Creative Commons Attribution 3.0 Unported License
	    * Website: https://phpunit.de/

**Note: The Javascript and CSS libraries are included in the repo, the PHP libraries will need to be install with composer.**

* Javascript Libraries
    * JQuery
        * Version: 3.3.1
        * Purpose: Make the Javascript code more lightweight, and handle the AJAX calls.
        * License: MIT License
        * Website: https://jquery.com/
   
    * JQuery UI
        * Version: 1.12
        * Purpose: Dependency for JQuery Shapeshift
        * License: MIT License
        * Website: https://jqueryui.com/
   
    * JQuery Shapeshift
        * Version: 2.0
        * Purpose: Handle to sorting and arranging of the drag/drop cards.
        * License: MIT License
        * Website: https://github.com/AshesOfOwls/jquery.shapeshift
   
    * Mustache JS
        * Version: 2.3.0
        * Purpose: HTML/Javascript templating engine to generate the tweet card HTML.
        * License: MIT License
        * Website: https://mustache.github.io/

* CSS Libraries
    * Bulma
        * Version: 0.6.2
        * Purpose: CSS library for general design and mobile responsiveness.
        * License: MIT License
        * Website: https://bulma.io/
        
    * Animate CSS
        * Version: 3.5.2
        * Purpose: CSS library create animations on the webpage.
        * License: MIT License
        * Website: https://daneden.github.io/animate.css/


### Testing ###

I created 2 classes to test the application. They are located under the 'twitter-app/tests' directory. Once you get PHPUnit installed with composer, you can run the tests by going into the 'twitter-app/config' directory, and running:

~~~~
./vendor/bin/phpunit ../tests/AuthTest.php

./vendor/bin/phpunit ../tests/TwitterRequestTest.php
~~~~

This will then run through the tests for each class.
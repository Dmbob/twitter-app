<?php
	session_name("twitter-app-test");
	session_name();

	include("../TwitterRequest.php");
	
	use PHPUnit\Framework\TestCase;

	final class TwitterRequestTest extends TestCase {
		public function testSearchTweets() {
			$twitter = new TwitterRequest();
			$auth = new Auth();

			$auth->request_app_only_access_token();

			$return_data = $twitter->search_tweets("notch", "", "", 1);

			unset($_SESSION["access_token"]);

			$json_data = json_decode($return_data, true);

			$this->assertArrayHasKey("statuses", $json_data);
		}

		public function testLoadMoreTweets() {
			$twitter = new TwitterRequest();
			$auth = new Auth();
			
			$auth->request_app_only_access_token();

			$return_data = $twitter->load_more_tweets("?since_id=960257947417882624&q=from%3Anotch%20%20exclude%3Areplies&lang=en&include_entities=1");

			unset($_SESSION["access_token"]);

			$json_data = json_decode($return_data, true);

			$this->assertArrayHasKey("statuses", $json_data);
		}
	}
?>
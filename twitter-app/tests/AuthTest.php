<?php
	session_name("twitter-app-test");
	session_name();

	include("../Auth.php");
	
	use PHPUnit\Framework\TestCase;

	final class AuthTest extends TestCase {
		public function testAuthHeaderValidity() {
			$auth = new Auth();

			$auth_header = $auth->generate_auth_header(
				"https://dmbob.guru/oauth/callback.php?status=test&type=anothertest", 
				"aaaaaaaaaa", 
				["oauth_token" => "bbbbbbbbbb"], 
				["status" => "test", "type" => "anothertest"]
			);

			$auth_params = explode(" ", str_replace(",", "", $auth_header));

			$this->assertEquals("OAuth", $auth_params[0]);
			$this->assertEquals('oauth_consumer_key="'.CONSUMER_KEY.'"', $auth_params[1]);
			$this->assertRegExp('/oauth_nonce="[a-zA-Z0-9]+"/', $auth_params[2]);
			$this->assertRegExp('/oauth_signature="[a-zA-Z0-9+\/]+={0,2}"/', rawurldecode($auth_params[3]));
			$this->assertEquals('oauth_signature_method="HMAC-SHA1"', $auth_params[4]);
			$this->assertRegExp('/oauth_timestamp="[0-9]+"/', $auth_params[5]);
			$this->assertEquals('oauth_token="bbbbbbbbbb"', $auth_params[6]);
			$this->assertEquals('oauth_version="1.0"', $auth_params[7]);
		}

		public function testRequestAppOnlyAccessToken() {
			$auth = new Auth();
			
			$auth->request_app_only_access_token();

			$this->assertTrue(isset($_SESSION["access_token"]));

			unset($_SESSION["access_token"]);
		}

		public function testRequestUserAuthRequestToken() {
			$auth = new Auth();

			$token = $auth->request_user_auth_request_token();

			$this->assertTrue(isset($token));
			$this->assertTrue(isset($_SESSION["oauth_user_token_secret"]));

			unset($_SESSION["oauth_user_token_secret"]);


		}
	}
?>
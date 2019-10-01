<?php


use \papajin\ActiveCampaign\AC\Contact;
use \GuzzleHttp\Client;

class ACTest extends PHPUnit_Framework_TestCase {

	public function testMakeHTTPClient() {
		$client = Contact::makeHTTPClient(
			'https://account.api-us1.com',
			'ffjhgjg'
		);

		$this->assertTrue( $client instanceof Client );
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInstanceException() {
		Contact::instance( 'https://account.api-us1.com' );
	}

	/**
	 * @dataProvider instanceArgs
	 */
	public function testInstance( $http_client_or_url, $token = null ) {
		$this->assertTrue(
			Contact::instance( $http_client_or_url, $token ) instanceof Contact
		);
	}

	public function instanceArgs() {
		return [
			['https://account.api-us1.com', 'ffjhgjg'],
			[ new Client ]
		];
	}
}

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
}

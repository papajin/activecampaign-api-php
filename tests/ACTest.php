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
	 * @expectedException \InvalidArgumentException
	 */
	public function testConstructorException() {
		$c = new Contact( '' );
	}

	/**
	 * @dataProvider mockInstanceWithException
	 *
	 * @param $api
	 *
	 * @expectedException \RuntimeException
	 * @expectedExceptionCode 400
	 */
	public function testIsSuccess( $api ) {
		$api->show(7);
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function test__call() {
		Contact::instance( 'https://account.api-us1.com', 'ffjhgjg' )
			->badMethod();
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

	public function mockInstanceWithException() {
		$mock = new \GuzzleHttp\Handler\MockHandler( [ new \GuzzleHttp\Psr7\Response(204, [], '{}') ] );

		$handler = \GuzzleHttp\HandlerStack::create( $mock );

		return [
			[ Contact::instance( new \GuzzleHttp\Client([ 'handler' => $handler, 'debug' => true ])) ]
		];
	}
}

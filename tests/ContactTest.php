<?php


use papajin\ActiveCampaign\Http\Contact;


class ContactTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Contact
	 */
	static $contact;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$contact instanceof Contact
			OR static::$contact = Contact::instance( new \GuzzleHttp\Client([]) );
	}


	/**
	 * @dataProvider providerShow
	 * @param $id
	 * @param $response
	 */
	public function testShow( $id, $response ) {
		$api = static::$contact->withClient( $this->mockClient( $response ) );

		$result = $api->show( $id );

		$this->assertObjectHasAttribute('contact', $result );
		$this->assertEquals( $id, $result->contact->id );
	}

	/**
	 * @dataProvider providerShowException
	 * @expectedException \RuntimeException
	 */
	public function testShowNotFoundException( $id, $exception ) {
		$api = static::$contact->withClient( $this->mockClient( $exception ) );

		$api->show( $id );
	}

	/**
	 * @dataProvider providerCreate
	 * @param $data
	 * @param array $response_stack
	 */
	public function testCreate( $data, $response_stack ) {
		$api = static::$contact->withClient( $this->mockClient( $response_stack ) );

		$result = $api->create( $data );

		$this->assertObjectHasAttribute('contact', $result );

		$this->assertEquals( $data[ 'email' ], $result->contact->email );
	}

	/**
	 * @dataProvider providerCreateOrUpdate
	 * @param array $data
	 * @param array $response_stack
	 * @param int $code
	 */
	public function testCreateOrUpdate( $data, $response_stack, $code ) {
		$api = static::$contact->withClient( $this->mockClient( $response_stack ) );

		$result = $api->createOrUpdate( $data );

		$this->assertEquals( $code, $api->getRawResponse()->getStatusCode() );
		$this->assertObjectHasAttribute('contact', $result );
	}

	/**
	 * @dataProvider providerUpdateListStatus
	 * @param $params
	 * @param $response
	 */
	public function testUpdateListStatus( $params, $response ) {
		$api = static::$contact->withClient( $this->mockClient( $response ) );

		$result = $api->updateListStatus( ...$params );

		$this->assertObjectHasAttribute('contactList', $result );
	}

	public function testUpdate() {
		$api = static::$contact->withClient( $this->mockClient([
			new \GuzzleHttp\Psr7\Response(200, [], '{}')
		]));

		$api->update( 1, [] );

		$response = $api->getRawResponse();

		$this->assertEquals( 200, $response->getStatusCode() );
	}

	public function testDelete() {
		$api = static::$contact->withClient( $this->mockClient([
			new \GuzzleHttp\Psr7\Response(200, [], '[]')
		]));

		$res = $api->delete(1);
		$this->assertEmpty( (array)$res );
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testDeleteException() {
		$api = static::$contact->withClient( $this->mockClient([
			new \GuzzleHttp\Psr7\Response(404, [], '{"message":"No result found for contact with id 1"}')
		]));

		$api->delete(1);
	}

	/**
	 * @covers \papajin\ActiveCampaign\Http\Contact::_link
	 * @dataProvider providerLink
	 *
	 * @param $id
	 * @param $response
	 */
	public function testLink( $id, $response ) {
		$api = $this->getMockBuilder( Contact::class )
			->setConstructorArgs([$this->mockClient( $response )])
			->setMethods(['_link'])
			->getMock();

		$api->expects($this->once())
		          ->method('_link')
		          ->with($id, 'contactAutomations');

		$api->contactAutomations( $id );
	}

	/**
	 * @dataProvider providerShow
	 * @param $id
	 * @param $response
	 */
	public function testScoreValues( $id, $response ) {
		$api = static::$contact->withClient( $this->mockClient( $response ) );
		$api->scoreValues( $id );

		$this->assertEquals( 200, $api->getRawResponse()->getStatusCode() );
	}

	/**
	 * @dataProvider providerIndex
	 * @param array $response_stack
	 */
	public function testIndex( $response_stack ) {
		$api = static::$contact->withClient( $this->mockClient( $response_stack ) );
		$result = $api->index();

		$this->assertTrue( is_numeric( $result->meta->total ) );
		$this->assertEquals( 200, $api->getRawResponse()->getStatusCode() );
	}

	public function testUpdateLists() {
		$api = $this->getMockBuilder( Contact::class )
					->disableOriginalConstructor()
		            ->setMethods(['_updateListStatus'])
		            ->getMock();

		$api->expects($this->exactly( 3 ))
		    ->method('_updateListStatus')
			->withConsecutive(
				['list' => 1, 'contact' => 1, 'status' => 1],
				['list' => 2, 'contact' => 2, 'status' => 2],
				['list' => 3, 'contact' => 3, 'status' => 1]
			)
			->willReturnOnConsecutiveCalls(false, true, true);

		$res = $api->updateLists([
				['list' => 1, 'contact' => 1, 'status' => 1],
				['list' => 2, 'contact' => 2, 'status' => 2],
				['list' => 3, 'contact' => 3, 'status' => 1]
			]);

		for( $i = 0; $i < 3; $i++ )
			$i
				? $this->assertTrue( $res[ $i ][ 'result' ], "$i failed" )
				: $this->assertFalse( $res[ $i ][ 'result' ], "$i failed" );
	}

	public function testUpdateListsWithException() {
		$api = $this->getMockBuilder( Contact::class )
		            ->disableOriginalConstructor()
		            ->setMethods(['_updateListStatus'])
		            ->getMock();

		$api->expects( $this->once() )
		    ->method('_updateListStatus')
		    ->willThrowException( new \RuntimeException() );

		$res = $api->updateLists([ [] ]);

		$this->assertArrayHasKey( 'result', $res[0] );
		$this->assertFalse( $res[0][ 'result' ] );
	}

	/** Providers */

	public function providerShow() {
		$id = 2;
		return [[
			$id, [ new \GuzzleHttp\Psr7\Response(200, [], '{"contact":{"id":"' . $id . '"},"meta":{"total":"1","page_input":{}}}') ]
		]];
	}

	public function providerShowException() {
		$id = 2;
		return [[
			$id, [ new \GuzzleHttp\Exception\RequestException("No Result found for Subscriber with id $id", new \GuzzleHttp\Psr7\Request('GET', "contacts/$id" )) ]
		]];
	}

	public function providerCreate() {
		$email = 'some_weird@email.com';
		return [
			[
				[
					'email' => $email,
					'first_name' => 'Test',
					'last_name' => 'Developer'
				],
				[ new \GuzzleHttp\Psr7\Response(201, [], '{"contact":{"email":"' . $email . '"},"meta":{"total":"1","page_input":{}}}') ]
			]
		];
	}

	public function providerCreateOrUpdate() {
		$email = 'some_weird@email.com';
		return [
			[   // Create case
				['email' => $email],
				[ new \GuzzleHttp\Psr7\Response(201, [], '{"contact":{"email":"' . $email . '"},"meta":{"total":"1","page_input":{}}}') ],
				201
			],
			[   // Update case
				['email' => $email],
				[ new \GuzzleHttp\Psr7\Response(200, [], '{"contact":{"email":"' . $email . '"},"meta":{"total":"1","page_input":{}}}') ],
				200
			]
		];
	}

	public function providerIndex() {
		return [[
			[ new \GuzzleHttp\Psr7\Response(200, [], '{"contacts":[{},{}],"meta":{"total":"2","page_input":{}}}') ]
		]];
	}

	public function providerUpdateListStatus() {
		return [[
			[1, 2, 3],
			[ new \GuzzleHttp\Psr7\Response(200, [], '{"contactList":{}}') ]
		]];
	}

	public function providerLink() {
		$id = 2;
		return [[
			$id, [ new \GuzzleHttp\Psr7\Response(200, [], '{"contactAutomations":{}}') ]
		]];
	}

	/** end of Providers */

	private function mockClient( $response_stack ) {
		$mock = new \GuzzleHttp\Handler\MockHandler( $response_stack );

		$handler = \GuzzleHttp\HandlerStack::create( $mock );

		return new \GuzzleHttp\Client([ 'handler' => $handler, 'debug' => true ]);
	}
}

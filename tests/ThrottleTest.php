<?php


use papajin\ActiveCampaign\Throttle;

class ThrottleTest extends PHPUnit_Framework_TestCase {

	public function testDelayed() {
		$cnt = Throttle::LIMIT;

		for ( ; $cnt > 0; $cnt-- )
			Throttle::__();

		sleep( Throttle::PERIOD );

		$this->assertTrue( count( Throttle::__() ) == 1 );
	}

	/**
	 * @dataProvider provider__
	 *
	 * @param $cnt
	 * @param $duration
	 * @param $operator
	 */
	public function test__( $cnt, $duration, $operator ) {
		$start = microtime( true );

		for ( ; $cnt > 0; $cnt-- )
			Throttle::__();

		$finish = microtime( true );

		if( '<' == $operator )
			$this->assertLessThan( $duration, $finish - $start );
		elseif( '>' == $operator )
			$this->assertGreaterThan( $duration, $finish - $start );
	}

	public function provider__() {
		return[
			[ Throttle::LIMIT, Throttle::PERIOD, '<' ],
			[ Throttle::LIMIT * 2, Throttle::PERIOD, '>' ],
			[ Throttle::LIMIT * 3, Throttle::PERIOD * 3, '<' ],
			[ Throttle::LIMIT * 4, Throttle::PERIOD * 3, '>' ]
		];
	}
}

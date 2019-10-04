<?php


namespace papajin\ActiveCampaign;

/**
 * ActiveCampaign API has a limitation of max 5 requests per second.
 * @link https://developers.activecampaign.com/reference?_ga=2.90372441.273793142.1569778815-1266780364.1569778815#rate-limits Rate Limits
 *
 * This class is called to handle the API calls within this condition.
 * What is needed is just to call Throttle::__() before every API request.
 *
 * Class Throttle
 * @package papajin\ActiveCampaign
 */
class Throttle {
	/**
	 * MAX number of calls per PERIOD
	 */
	const LIMIT = 5;

	/**
	 * MIN time period for LIMIT calls, seconds.
	 */
	const PERIOD = 1;

	/**
	 * @var array Stack of the moments of the last LIMIT calls.
	 */
	private $calls = [];

	private function __construct() {}

	private function call()
	{
		$now = microtime( true );

		if( count( $this->calls ) == static::LIMIT ) {
			// If previous call was more than PERIOD ago, then reset the stack of calls.
			if( $now - end( $this->calls ) >= static::PERIOD ) {
				$this->calls = [ $now ];
				return;
			}

			// Extract first moment and check time difference with current moment.
			$delta = $now - array_shift( $this->calls ) - static::PERIOD;

			if( $delta < 0 )
				usleep( abs( $delta * 10E5 ) );
		}

		array_push( $this->calls, $now );
	}

	public static function __()
	{
		static $inst = null;

		if( is_null( $inst ) )
			$inst = new static;

		$inst->call();
	}
}
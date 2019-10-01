<?php


namespace papajin\ActiveCampaign;

use GuzzleHttp\Client;

abstract class AC {

	/**
	 * @var Client
	 */
	protected $http_client;

	public function __construct( $http_client )
	{
		$this->http_client = $http_client;
	}

	/**
	 * @param string $url The account url (like https://account.api-us1.com)
	 * @param string $token Unique API key
	 *
	 * @return Client
	 */
	public static function makeHTTPClient( $url, $token )
	{
		return new Client([
			'base_url' => ['{url}/api/3/', ['url' => $url]],
			'defaults' => [
				'headers' => ['Api-Token' => $token]
			]
		]);
	}

	/**
	 * @param Client|string $http_client_or_url A valid GuzzleHttp\Client or account url.
	 * @param string|null   $token Unique API key. Required, if $http_client_or_url is not a valid GuzzleHttp\Client (i.e. is url).
	 *
	 * @return static
	 * @throws \InvalidArgumentException
	 */
	public static function instance( $http_client_or_url, $token = null )
	{
		if( $http_client_or_url instanceof Client )
			return new static ( $http_client_or_url );

		if( !$token )
			throw new \InvalidArgumentException( '$token is required if first argument is not a valid GuzzleHttp\Client' );

		return new static(
			static::makeHTTPClient( $http_client_or_url, $token )
		);
	}
}
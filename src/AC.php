<?php


namespace papajin\ActiveCampaign;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

abstract class AC {

	const API_URI = '/api/3/';

	/**
	 * @var Client
	 */
	protected $http_client;

	/**
	 * @var ResponseInterface
	 */
	protected $http_response;

	public function __construct( $http_client )
	{
		$this->httpClient( $http_client );
	}

	/**
	 * @param Client|null $http_client
	 *
	 * @return Client|void
	 */
	public function httpClient( $http_client = null )
	{
		if( is_null( $http_client ) )
			return $this->http_client;
		elseif ( $http_client instanceof Client )
			$this->http_client = $http_client;
		else
			throw new \InvalidArgumentException( '$http_client must be an instance of GuzzleHttp\Client' );
	}

	public function withClient( $http_client, $clean = true )
	{
		$copy = clone $this;

		$copy->httpClient( $http_client );

		if( $clean ) $copy->flushResponse();;

		return $copy;
	}

	/**
	 * Erase previous request result
	 */
	public function flushResponse()
	{
		$this->http_response = null;
	}

	/**
	 * Original response instance
	 *
	 * @return ResponseInterface
	 */
	public function getRawResponse()
	{
		return $this->http_response;
	}

	/**
	 * @param ResponseInterface $result
	 * @param $expected_code
	 *
	 * @return mixed
	 */
	protected function isSuccess( $expected_code = 200 )
	{
		if( ! $this->http_response instanceof ResponseInterface )
		    return false;
		elseif(( is_array( $expected_code ) AND in_array( $this->http_response->getStatusCode(), $expected_code )) OR $expected_code == $this->http_response->getStatusCode())
			return json_decode( $this->http_response->getBody() );

		throw new \RuntimeException(
			$this->http_response->getReasonPhrase(),
			$this->http_response->getStatusCode()
		);
	}

	public function __call( $name, $arguments = [] )
	{
		$name = '_' . $name;

		if( is_callable([ $this, $name ])) {
			Throttle::__();

			$this->{$name}( ...$arguments );

			return $this->isSuccess( $this->expectedCode( ltrim( $name, '_' ) ) );
		}

		throw new \BadMethodCallException( '$name method has not been implemented.' );
	}

	protected function expectedCode( $function )
	{
		return 200;
	}

	/**
	 * @param string $url The account url (like https://account.api-us1.com)
	 * @param string $token Unique API key
	 *
	 * @return Client
	 */
	public static function makeHTTPClient( $url, $token )
	{
		$base_url = rtrim( $url, '/' ) . '/' . ltrim( static::API_URI, '/' );

		return new Client([
			'base_uri' => $base_url,
			'headers' => [ 'Api-Token' => $token ]
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
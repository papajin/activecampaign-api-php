<?php


namespace papajin\ActiveCampaign\AC;


use papajin\ActiveCampaign\AC;

/**
 * Class Contact
 * @package papajin\ActiveCampaign\AC
 * @method mixed index()
 * @method mixed show(integer $id)
 * @method mixed create(array $data)
 */
class Contact extends AC {

	const ENDPOINT = 'contacts';

	/**
	 * List all contacts
	 * @link https://developers.activecampaign.com/reference?_ga=2.90372441.273793142.1569778815-1266780364.1569778815#list-all-contacts List all contacts
	 *
	 * View many (or all) contacts by including their ID's or various filters.
	 * This is useful for searching for contacts that match certain criteria - such as being part of a certain list,
	 * or having a specific custom field value.
	 *
	 * @return mixed stdClass of the response body or false if response is not a ResponseInterface instance.
	 * @throws \RuntimeException (actually \GuzzleHttp\Exception\ClientException)
	 */
	protected function _index()
	{
		$this->http_response = $this->http_client->get( static::ENDPOINT );
	}

	/**
	 * Retrieve an existing contact
	 * @link https://developers.activecampaign.com/reference?_ga=2.90372441.273793142.1569778815-1266780364.1569778815#get-contact Retrieve an existing contact
	 *
	 * @param int $id
	 *
	 * @return mixed stdClass of the response body or false if response is not a ResponseInterface instance.
	 * @throws \RuntimeException (actually \GuzzleHttp\Exception\ClientException)
	 * @throws \InvalidArgumentException
	 */
	protected function _show( $id )
	{
		if( !filter_var( $id, FILTER_VALIDATE_INT ) )
			throw new \InvalidArgumentException( '$id must be an integer' );

		$this->http_response = $this->http_client->get( static::ENDPOINT . '/' . $id );
	}

	/**
	 * Create a contact
	 * @link https://developers.activecampaign.com/reference?_ga=2.90372441.273793142.1569778815-1266780364.1569778815#create-contact Create a contact
	 *
	 * @param array $data
	 * @return mixed
	 *              
	 * @throws \InvalidArgumentException
	 */
	protected function _create( $data )
	{
		if( !is_array( $data ) )
			throw new \InvalidArgumentException( '$data must be an array' );

		$this->http_response = $this->http_client->post(
			static::ENDPOINT,
			[ 'body' => json_encode([ 'contact' => $data ]) ]
		);
	}

	/**
	 * Create or update contact
	 * @link https://developers.activecampaign.com/reference?_ga=2.90372441.273793142.1569778815-1266780364.1569778815#create-contact-sync Create or update contact
	 *
	 * @param array $data
	 * @return mixed
	 */
	protected function _createOrUpdate( $data )
	{
		if( !is_array( $data ) )
			throw new \InvalidArgumentException( '$data must be an array' );

		$this->http_response = $this->http_client->post(
			'contact/sync',
			[ 'body' => json_encode([ 'contact' => $data ]) ]
		);
	}

	/**
	 * Subscribe a contact to a list or unsubscribe a contact from a list.
	 * @link https://developers.activecampaign.com/reference?_ga=2.90372441.273793142.1569778815-1266780364.1569778815#update-list-status-for-contact Update list status
	 *
	 * @param int $list
	 * @param int $contact
	 * @param int $status
	 *
	 * @return mixed
	 */
	protected function _updateListStatus( $list, $contact, $status )
	{
		$this->http_response = $this->http_client->post(
			'contactLists',
			[ 'body' => sprintf('{"contactList": {"list": %d, "contact": %d, "status": %d}}', $list, $contact, $status) ]
		);
	}

	/**
	 * Update a contact
	 */
	protected function _update()
	{

	}

	/**
	 * Delete an existing contact
	 * @link https://developers.activecampaign.com/reference?_ga=2.90372441.273793142.1569778815-1266780364.1569778815#delete-contact Delete an existing contact
	 *
	 * @param int $id
	 * @return mixed
	 */
	protected function _delete( $id )
	{
		$this->http_response = $this->http_client->delete( static::ENDPOINT . '/' . $id );
	}

	/**
	 * List all automations the contact is in
	 */
	protected function _listAutomations()
	{

	}

	/**
	 * Retrieve a contacts score value
	 */
	protected function _score()
	{

	}

	protected function expectedCode( $function )
	{
		switch ( $function )
		{
			case 'create':
				return 201;
			case 'createOrUpdate':
				return [ 200, 201 ];
			default:
				return parent::expectedCode( $function );
		}
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function updateLists( $data )
	{
		foreach ( $data as &$row ) {
			extract( $row );
			try {
				$row['result'] = (bool)$this->updateListStatus( $list, $contact, $status );
			}
			catch ( \RuntimeException $e ) {
				$row['result'] = false;
			}
		}

		return $data;
	}
}
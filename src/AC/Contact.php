<?php


namespace papajin\ActiveCampaign\AC;


use papajin\ActiveCampaign\AC;
use \GuzzleHttp\Exception\ClientException;

/**
 * Class Contact
 * @package papajin\ActiveCampaign\AC
 *
 * @method mixed index()
 * @method mixed show(integer $id)
 * @method mixed create(array $data)
 * @method mixed createOrUpdate(array $data)
 * @method mixed updateListStatus(integer $list, integer $contact, integer $status)
 * @method mixed update(integer $id, array $data)
 * @method mixed listAutomations(integer $id)
 * @method mixed bounceLogs(integer $id)
 * @method mixed contactAutomations(integer $id)
 * @method mixed contactData(integer $id)
 * @method mixed contactGoals(integer $id)
 * @method mixed contactLists(integer $id)
 * @method mixed contactLogs(integer $id)
 * @method mixed contactTags(integer $id)
 * @method mixed contactDeals(integer $id)
 * @method mixed deals(integer $id)
 * @method mixed fieldValues(integer $id)
 * @method mixed geoIps(integer $id)
 * @method mixed notes(integer $id)
 * @method mixed organization(integer $id)
 * @method mixed plusAppend(integer $id)
 * @method mixed trackingLogs(integer $id)
 * @method mixed scoreValues(integer $id)
 * @method mixed accountContacts(integer $id)
 * @method mixed automationEntryCounts(integer $id)
 */
class Contact extends AC {

	const ENDPOINT = 'contacts';

	private $_linkMethods = [
		'bounceLogs', 'contactAutomations', 'contactData', 'contactGoals', 'contactLists', 'contactLogs', 'contactTags',
		'contactDeals', 'deals', 'fieldValues', 'geoIps', 'notes', 'organization', 'plusAppend', 'trackingLogs',
		'scoreValues', 'accountContacts', 'automationEntryCounts'
	];

	/**
	 * List all contacts
	 * @link https://developers.activecampaign.com/reference?_ga=2.90372441.273793142.1569778815-1266780364.1569778815#list-all-contacts List all contacts
	 *
	 * View many (or all) contacts by including their ID's or various filters.
	 * This is useful for searching for contacts that match certain criteria - such as being part of a certain list,
	 * or having a specific custom field value.
	 *
	 * @throws ClientException
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
	 * @throws ClientException
	 */
	protected function _show( $id )
	{
		$this->http_response = $this->http_client->get( static::ENDPOINT . '/' . $id );
	}

	/**
	 * Create a contact
	 * @link https://developers.activecampaign.com/reference?_ga=2.90372441.273793142.1569778815-1266780364.1569778815#create-contact Create a contact
	 *
	 * @param array $data
	 *              
	 * @throws ClientException
	 */
	protected function _create( $data )
	{
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
	 *
	 * @throws ClientException
	 */
	protected function _createOrUpdate( $data )
	{
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
	 * @throws ClientException
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
	 * @link https://developers.activecampaign.com/reference?_ga=2.90372441.273793142.1569778815-1266780364.1569778815#update-a-contact Update a contact
	 *
	 * @param array $data
	 *
	 * @throws ClientException
	 */
	protected function _update( $id, $data )
	{
		$this->http_response = $this->http_client->put(
			static::ENDPOINT . '/' . $id,
			[ 'body' => json_encode([ 'contact' => $data ]) ]
		);
	}

	/**
	 * Delete an existing contact
	 * @link https://developers.activecampaign.com/reference?_ga=2.90372441.273793142.1569778815-1266780364.1569778815#delete-contact Delete an existing contact
	 *
	 * @param int $id
	 *
	 * @throws ClientException
	 */
	protected function _delete( $id )
	{
		$this->http_response = $this->http_client->delete( static::ENDPOINT . '/' . $id );
	}

	/**
	 * Retrieve a contacts data from the provided link ($param)
	 *
	 * @throws ClientException
	 */
	protected function _link( $id, $param )
	{
		$this->http_response = $this->http_client->get(
			sprintf( '%s/%d/%s', static::ENDPOINT, $id, $param )
		);
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function updateLists( $data )
	{
		foreach ( $data as &$row ) {
			extract( $row );
			/**
			 * @var $list
			 * @var $contact
			 * @var $status
			 */
			try {
				$row['result'] = (bool)$this->updateListStatus( $list, $contact, $status );
			}
			catch ( \RuntimeException $e ) {
				$row['result'] = false;
			}
		}

		return $data;
	}

	/** Overrides */

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

	protected function methodName( $method, &$params )
	{
		if( in_array( $method, $this->_linkMethods ) ) {
			array_push( $params, $method );
			$method = 'link';
		}

		return parent::methodName( $method, $params );
	}
}
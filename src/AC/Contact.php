<?php


namespace papajin\ActiveCampaign\AC;


use papajin\ActiveCampaign\AC;
use \GuzzleHttp\Exception\ClientException;

/**
 * Class Contact
 * @package papajin\ActiveCampaign\AC
 *
 * @method mixed index(array $filters = [])
 * @method mixed show(integer $id)
 * @method mixed create(array $data)
 * @method mixed createOrUpdate(array $data)
 * @method mixed updateListStatus(integer $list, integer $contact, integer $status)
 * @method mixed update(integer $id, array $data)
 * @method mixed listAutomations(integer $id)
 * @method mixed addToAutomation( integer $contact, integer $automation )
 * @method mixed removeFromAutomation( integer $contactAutomationId )
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

	protected $inst_name = 'contact';

	protected $_linkMethods = [
		'bounceLogs', 'contactAutomations', 'contactData', 'contactGoals', 'contactLists', 'contactLogs', 'contactTags',
		'contactDeals', 'deals', 'fieldValues', 'geoIps', 'notes', 'organization', 'plusAppend', 'trackingLogs',
		'scoreValues', 'accountContacts', 'automationEntryCounts'
	];

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
			[ 'body' => json_encode([ $this->inst_name => $data ]) ]
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

	protected function _addToAutomation( $contact, $automation )
	{
		$this->http_response = $this->http_client->post(
			'contactAutomations',
			[ 'body' => sprintf('{"contactAutomation": {"contact": %d, "automation": %d}}', $contact, $automation) ]
		);
	}

	protected function _removeFromAutomation( $id )
	{
		$this->http_response = $this->http_client->delete( 'contactAutomations/' . $id );
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
		if( in_array( $function, ['createOrUpdate', 'updateListStatus', 'create', 'addToAutomation'] ) )
			return [ 200, 201 ];

		return parent::expectedCode( $function );
	}
}
<?php


namespace papajin\ActiveCampaign\AC;


use papajin\ActiveCampaign\AC;

class CustomField extends AC {

	const ENDPOINT = 'fields';

	protected $inst_name = 'field';
	protected $_linkMethods = ['options', 'relations'];

	/* Specific methods */

	/**
	 * Create a custom field relationship to list(s)
	 *
	 * @link https://developers.activecampaign.com/reference?_ga=2.126002253.1303491888.1592414525-831184651.1592414525#create-a-custom-field-relationship-to-lists
	 * @param array $data
	 */
	protected function _createCustomFieldRelationshipToList( $data )
	{
		$this->http_response = $this->http_client->post(
			'fieldRels',
			[ 'body' => json_encode([ 'fieldRel' => $data ]) ]
		);
	}

	/**
	 * Create custom field options
	 *
	 * @link https://developers.activecampaign.com/reference?_ga=2.126002253.1303491888.1592414525-831184651.1592414525#create-a-custom-field-relationship-to-lists
	 * @param $data
	 */
	protected function _createCustomFieldOptions( $data )
	{
		$this->http_response = $this->http_client->post(
			'fieldOption/bulk',
			[ 'body' => json_encode([ 'fieldOptions' => $data ]) ]
		);
	}

	/** Overrides */

	protected function expectedCode( $function )
	{
		if( 'createCustomFieldRelationshipToList' == $function OR 'createCustomFieldOptions' == $function )
			return 201;

		return parent::expectedCode( $function );
	}
}
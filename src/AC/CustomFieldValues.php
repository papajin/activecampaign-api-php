<?php


namespace papajin\ActiveCampaign\AC;


use papajin\ActiveCampaign\AC;

class CustomFieldValues extends AC {

	const ENDPOINT = 'fieldValues';

	protected $inst_name = 'fieldValue';
	protected $_linkMethods = [
		'bounceLogs', 'contactAutomations', 'contactData', 'contactGoals', 'contactLists', 'contactLogs', 'contactTags',
		'contactDeals', 'deals', 'fieldValues', 'geoIps', 'notes', 'organization', 'plusAppend', 'trackingLogs',
		'scoreValues'
	];

	/** Overrides */

}
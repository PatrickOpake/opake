<?php
namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

class ST extends Header {

	public function __construct($asn_id)
	{
		$this->header_label = 'ST';
		$this->addElement('143', 3, 3, '270');
		$this->addElement('329', 4, 9, $asn_id);
		$this->addElement('329', 1, 35, '005010X279A1');
	}
}
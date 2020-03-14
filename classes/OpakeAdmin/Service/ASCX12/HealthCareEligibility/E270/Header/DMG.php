<?php
namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

class DMG extends Header {

	public function __construct($dob)
	{
		$this->header_label = 'DMG';
		$this->addElement('1250', 2, 3, 'D8');
		$this->addElement('1251', 1, 35, $dob);
	}
}
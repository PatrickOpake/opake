<?php
namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

class IEA extends Header {

	public function __construct($num_of_functional_groups, $interchange_control_number)
	{
		$this->header_label = 'IEA';
		$this->addElement('i16', 1, 5, $num_of_functional_groups);
		$this->addElement('i12', 9, 9, $interchange_control_number);
	}
}
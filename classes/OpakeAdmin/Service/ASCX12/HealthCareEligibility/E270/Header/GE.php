<?php
namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

class GE extends Header {

	public function __construct($number_of_transaction_sets, $group_control_num)
	{
		$this->header_label = 'GE';
		$this->addElement('97', 1, 6, $number_of_transaction_sets);
		$this->addElement('28', 1, 9, $group_control_num);
	}
}
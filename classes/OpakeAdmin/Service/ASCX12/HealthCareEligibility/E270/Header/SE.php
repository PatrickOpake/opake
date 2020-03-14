<?php
namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

class SE extends Header {

	public function __construct($num_of_segments, $transaction_set_control_num)
	{
		$this->header_label = 'SE';
		$this->addElement('96', 1, 10, $num_of_segments);
		$this->addElement('329', 4, 9, $transaction_set_control_num);
	}
}
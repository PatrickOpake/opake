<?php
namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

class EQ extends Header {

	public function __construct($code)
	{
		$this->header_label = 'EQ';
		$this->addElement('1365', 1, 2, $code);
	}
}
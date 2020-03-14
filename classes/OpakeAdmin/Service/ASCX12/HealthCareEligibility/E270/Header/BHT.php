<?php
namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

class BHT extends Header {

	public function __construct($date_edi_created, $time_edi_created)
	{
		$this->header_label = 'BHT';
		$this->addElement('1005', 4, 4, '0022');
		$this->addElement('353', 2, 2, '13');
		$this->addElement('127', 1, 50, '');
		$this->addElement('373', 8, 8, $date_edi_created);
		$this->addElement('337', 4, 8, $time_edi_created);
		//$this->addElement('640', 2, 2, '');
	}
}
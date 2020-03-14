<?php
namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

class GS extends Header {

	public function __construct($date_edi_created, $time_edi_created, $group_control_number)
	{
		$this->header_label = 'GS';
		$this->addElement('479', 2, 2, 'HS');
		$this->addElement('142', 2, 15, 'OPAKE');
		$this->addElement('124', 2, 15, 'NAVICURE');
		$this->addElement('373', 8, 8, $date_edi_created);
		$this->addElement('337', 4, 8, $time_edi_created);
		$this->addElement('28', 1, 9, $group_control_number);
		$this->addElement('455', 1, 2, 'X');
		$this->addElement('480', 1, 12, '005010X279A1');
	}
}
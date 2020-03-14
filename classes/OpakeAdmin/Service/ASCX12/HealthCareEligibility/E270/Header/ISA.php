<?php
namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

class ISA extends Header {

	public function __construct($date_edi_created, $time_edi_created, $interchange_control_number, $test_or_production)
	{
		$this->header_label = 'ISA';

		$this->addElement('I01', 2, 2, '00');
		$this->addElement('I02', 10, 10, '');
		$this->addElement('I03', 2, 2, '00');
		$this->addElement('I04', 10, 10, '');
		$this->addElement('I05a', 2, 2, '01');
		$this->addElement('I06', 15, 15, 'OPAKE');
		$this->addElement('I05b', 2, 2, 'ZZ');
		$this->addElement('I07', 15, 15, 'NAVICURE');
		$this->addElement('I08', 6, 6, $date_edi_created);
		$this->addElement('I09', 4, 4, $time_edi_created);
		$this->addElement('I65', 1, 1, '^');
		$this->addElement('I11', 5, 5, '00501');
		$this->addElement('I12', 9, 9, $interchange_control_number);
		$this->addElement('I13', 1, 1, '0');
		$this->addElement('I14', 1, 1, $test_or_production);
		$this->addElement('I15', 1, 1, ':');
	}

	protected function getElementString($element)
	{
		$return = str_pad($element['value'], $element['min']);
		$return = $return.$this->end_element_delimiter;
		return $return;
	}
}
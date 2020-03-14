<?php
namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

class NM1 extends Header {

	public function __construct($id_code, $type_qualifier, $last_name, $first_name, $id_code_qualifier, $identification_code)
	{
		$this->header_label = 'NM1';
		$this->addElement('98', 2, 3, $id_code);
		$this->addElement('1065', 1, 1, $type_qualifier);
		$this->addElement('1035', 1, 60, $last_name);
		$this->addElement('1036', 1, 35, $first_name);
		$this->addElement('1037', 1, 25, '');
		$this->addElement('1038', 1, 10, '');
		$this->addElement('1039', 1, 10, '');
		$this->addElement('66', 1, 2, $id_code_qualifier);
		$this->addElement('67', 2, 80, $identification_code);
	}
}
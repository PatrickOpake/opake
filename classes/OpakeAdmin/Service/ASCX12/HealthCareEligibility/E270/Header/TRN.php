<?php
namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

class TRN extends Header {

	public function __construct($trace_type_code, $ref_id, $company_id)
	{
		$this->header_label = 'TRN';
		$this->addElement('481', 1, 2, $trace_type_code);
		$this->addElement('127', 1, 50, $ref_id);
		$this->addElement('509', 10, 10, $company_id);
	}
}
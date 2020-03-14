<?php
namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header;

class HL extends Header {

	public function __construct($hl_id, $hl_parent_id, $hl_code, $child_node)
	{
		$this->header_label = 'HL';
		$this->addElement('628', 1, 12, $hl_id);
		$this->addElement('734', 1, 12, $hl_parent_id);
		$this->addElement('735', 1, 2, $hl_code);
		$this->addElement('736', 1, 1, $child_node);
	}
}
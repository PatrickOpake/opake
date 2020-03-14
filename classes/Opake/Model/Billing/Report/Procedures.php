<?php

namespace Opake\Model\Billing\Report;

use Opake\Model\AbstractModel;

class Procedures extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'billing_procedures_report';
	protected $_row = [
		'id' => null,
		'case_id' => null,
		'organization_id' => null,
		'at_top' => 0,
		'dos' => null,
		'last_name' => '',
		'first_name' => '',
		'id_number' => '',
		'location' => '',
		'fee_type' => '',
		'cpt' => '',
		'fee' => null,
		'pn1' => '',
		'dr_id' => '',
		'ins1' => '',
		'normalized_ins_id' => '',
		'insurance_name' => ''
	];

	protected $belongs_to = [
		'case' => [
			'model' => 'Cases_Item',
			'key' => 'case_id'
		]
	];

	public function save()
	{
		if (isset($this->dos) && $this->dos) {
			$this->at_top = false;
		}

		parent::save();
	}


	public function toArray()
	{
		$data = parent::toArray();

		$data['fee'] = number_format($this->fee, 2, '.', '');
		$data['dos'] = $this->dos ? date('D M d Y H:i:s O', strtotime($this->dos)) : null;

		if (isset($this->case_id) && $this->case_id) {
			$case = $this->case;
			$data['case'] = [
				'dos' => date('D M d Y H:i:s O', strtotime($case->time_start)),
				'last_name' => $case->registration->patient->last_name,
				'first_name' => $case->registration->patient->first_name,
				'id_number' => $case->registration->patient->getFormattedMrn()
			];
		}

		return $data;
	}
}

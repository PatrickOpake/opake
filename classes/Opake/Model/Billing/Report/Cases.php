<?php

namespace Opake\Model\Billing\Report;

use Opake\Model\AbstractModel;

class Cases extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'billing_cases_report';
	protected $_row = [
		'id' => null,
		'case_id' => null,
		'account_number' => '',
		'organization_id' => null,
		'at_top' => 0,
		'dos' => null,
		'last_name' => '',
		'first_name' => '',
		'id_number' => '',
		'doctor' => '',
		'insurance_modifiers' => '',
		'insurance' => '',
		'prefix' => '',
		'cd' => '',
		'cpt' => '',
		'charges' => null,
		'recent_payment' => null,
		'pmt' => null,
		'ins_adj' => null,
		'bs' => '',
		'deductible' => null,
		'co_pay' => null,
		'tfr_prov' => null,
		'balance' => null,
		'var_cost' => null,
		'or_time' => null,
		'notes' => ''
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

	public function fromArray($data)
	{
		if (isset($data->recent_payment) && $data->recent_payment) {
			$data->recent_payment = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($data->recent_payment));
		}
		return $data;
	}

	public function toArray()
	{
		$data = parent::toArray();

		$data['charges'] = number_format($this->charges, 2, '.', '');
		$data['pmt'] = number_format($this->pmt, 2, '.', '');
		$data['ins_adj'] = number_format($this->ins_adj, 2, '.', '');
		$data['deductible'] = number_format($this->deductible, 2, '.', '');
		$data['co_pay'] = number_format($this->co_pay, 2, '.', '');
		$data['tfr_prov'] = number_format($this->tfr_prov, 2, '.', '');
		$data['balance'] = number_format($this->balance, 2, '.', '');
		$data['var_cost'] = number_format($this->var_cost, 2, '.', '');
		$data['or_time'] = number_format($this->or_time, 2, '.', '');
		$data['dos'] = $this->dos ? date('D M d Y H:i:s O', strtotime($this->dos)) : null;
		$data['recent_payment'] = $this->recent_payment ? date('D M d Y H:i:s O', strtotime($this->recent_payment)) : null;

		if (isset($this->case_id) && $this->case_id) {
			$case = $this->case;
			$data['case'] = [
				'dos' => $case->time_start,
				'last_name' => $case->registration->patient->last_name,
				'first_name' => $case->registration->patient->first_name,
				'id_number' => $case->registration->patient->getFormattedMrn(),
				'doctor' => $case->getFirstSurgeon()->getFullName(),
				'insurance' => $case->registration->getPrimaryInsuranceTitle()
			];
		}

		return $data;
	}
}

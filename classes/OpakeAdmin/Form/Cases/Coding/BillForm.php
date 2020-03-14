<?php

namespace OpakeAdmin\Form\Cases\Coding;

use Opake\Form\AbstractForm;

class BillForm extends AbstractForm
{

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('amount')->rule('decimal')->error('Wrong format of Amount field');
		$validator->field('charge')->rule('decimal')->error('Wrong format of Charge field');
	}

	protected function getFields()
	{
		return array_merge(parent::getFields(), [
			'charge_master_entry',
			'quantity',
			'revenue_code',
			'amount',
			'charge',
			'sort',
		    'mod',
		    'diagnoses_rows'
		]);
	}

	protected function prepareValuesForModel($data)
	{
		if (!empty($data['charge_master_entry'])) {
			$data['charge_master_entry_id'] = $data['charge_master_entry']['id'];
		}

		if (empty($data['mod']['charge_master_entry']) && empty($data['mod']['id'])){
			$data['mod']['is_custom'] = true;
			if (empty($data['mod']['name'])){
				$data['mod']['name'] = ' ';
			}
		}

		$data['custom_modifier'] = null;

		if (isset($data['mod'])) {
			if (!empty($data['mod']['is_custom'])) {
				$data['custom_modifier'] = $data['mod']['name'];
			}
		}

		if (!empty($data['diagnoses_rows'])) {
			unset($data['diagnoses_rows']);
		}

		if (!isset($data['amount']) || $data['amount'] === '') {
			$data['amount'] = 0;
		}

		if (!isset($data['charge']) || $data['charge'] === '') {
			$data['charge'] = 0;
		}

		return $data;
	}

	public function save()
	{
		parent::save();

		if (isset($this->values['diagnoses_rows'])) {
			$this->model->updateDiagnosesNumbers($this->values['diagnoses_rows']);
		}
	}

}

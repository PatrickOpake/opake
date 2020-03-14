<?php

namespace Opake\Formatter\Cases\Coding;

use Opake\Formatter\BaseDataFormatter;

class BillFormatter extends BaseDataFormatter
{

	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
				'fields' => [
					'id',
					'coding_id',
					'charge_master_entry',
					'quantity',
					'modifiers',
					'mod',
					'revenue_code',
					'diagnoses_rows',
					'charge',
					'amount',
					'sort'
				],
				'fieldMethods' => [
					'id' => 'int',
					'coding_id' => 'int',
					'charge_master_entry' => 'chargeMasterEntry',
					'quantity' => 'int',
					'diagnoses_rows' => 'diagnosesRows',
					'charge' => 'money',
					'amount' => 'money',
					'sort' => 'int',
				    'modifiers' => 'modifiers',
				    'mod' => 'currentModifier'
				]
		]);
	}

	protected function formatModifiers($name, $options, $model)
	{
		return $this->getModifiers($model);
	}

	protected function formatCurrentModifier($name, $options, $model)
	{
		if ($model->custom_modifier) {
			return [
			    'name' => $model->custom_modifier,
			    'is_custom' => true
			];
		}

		$chargeMasterEntry = $model->getChargeMasterEntry();
		if ($chargeMasterEntry) {
			$modifiers = $this->getModifiers($model);

			foreach ($modifiers as $modifier) {
				if ($modifier['charge_master_entry']['id'] == $chargeMasterEntry->id()) {
					return $modifier;
				}
			}
		}

		return null;
	}

	protected function formatChargeMasterEntry($name, $options, $model)
	{
		$chargeMasterEntry = $model->getChargeMasterEntry();
		if ($chargeMasterEntry) {
			return $chargeMasterEntry->getFormatter('ListOption')->toArray();
		}

		return null;
	}

	protected function formatDiagnosesRows($name, $options, $model)
	{
		$opts = $model->getDiagnosesNumbers();

		foreach ($opts as $index => $num) {
			$opts[$index] = (int) $num;
		}

		return $opts;
	}

	protected function formatMoney($name, $options, $model)
	{
		return number_format((float) $model->$name, 2, '.', '');
	}

	protected function getModifiers($model)
	{
		$modifiers = [];

		$chargeMasterEntry = $model->getChargeMasterEntry();

		if ($chargeMasterEntry) {
			$similarRecords = $this->pixie->orm->get('Master_Charge')
				->where('cpt', $chargeMasterEntry->cpt)
				->where('site_id', $chargeMasterEntry->site_id)
				->find_all();

			foreach ($similarRecords as $record) {
				if ($record->cpt_modifier1 || $record->cpt_modifier2) {
					$modifiers[] = [
						'name' => $record->getModifiersTitle(),
						'charge_master_entry' => $record->getFormatter('ListOption')->toArray()
					];
				}
			}
		}

		return $modifiers;
	}

}

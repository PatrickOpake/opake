<?php

namespace Opake\Formatter\Billing\Navicure\Eligibility;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Navicure\Claim;

class ListEntryFormatter extends BaseDataFormatter
{

	private $reg;

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
			    'time_start',
			    'first_name',
			    'last_name',
			    'insurance_name',
			    'policy_number',
			],
			'fieldMethods' => [
			    'id' => 'int',
			    'time_start' => 'dos',
			    'first_name' => 'firstName',
			    'last_name' => 'lastName',
			    'insurance_name' => 'insuranceName',
			    'policy_number' => 'policyNumber'
			]
		]);
	}

	protected function formatFirstName($name, $options, $model)
	{
		if (!empty($this->reg) && $this->reg->loaded()) {
			return $this->reg->first_name;
		}
		$regId = $model->registration_id;
		if ($regId) {
			$this->reg = $this->pixie->orm->get('Cases_Registration', $regId);
			if ($this->reg->loaded()) {
				return $this->reg->first_name;
			}
		}
		return null;
	}

	protected function formatLastName($name, $options, $model)
	{
		if (!empty($this->reg) && $this->reg->loaded()) {
			return $this->reg->last_name;
		}
		$regId = $model->registration_id;
		if ($regId) {
			$this->reg = $this->pixie->orm->get('Cases_Registration', $regId);
			if ($this->reg->loaded()) {
				return $this->reg->last_name;
			}
		}
		return null;
	}

	protected function formatDos($name, $options, $model)
	{
		if (!empty($this->reg) && $this->reg->loaded()) {
			$date = TimeFormat::fromDBDatetime($this->reg->case->time_start);
			if ($date) {
				return TimeFormat::getDate($date);
			}
		}
		$regId = $model->registration_id;
		if ($regId) {
			$this->reg = $this->pixie->orm->get('Cases_Registration', $regId);
			if ($this->reg->loaded()) {
				$date = TimeFormat::fromDBDatetime($this->reg->case->time_start);
				if ($date) {
					return TimeFormat::getDate($date);
				}
			}
		}
		return null;
	}

	protected function formatInsuranceName($name, $options, $model)
	{
		return $model->getTitle();
	}

	protected function formatPolicyNumber($name, $options, $model)
	{
		$insuranceDataModel = $model->getInsuranceDataModel();
		if(isset($insuranceDataModel->policy_number)) {
			return $insuranceDataModel->policy_number;
		}
		return null;
	}
}
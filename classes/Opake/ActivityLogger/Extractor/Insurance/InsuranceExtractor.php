<?php

namespace Opake\ActivityLogger\Extractor\Insurance;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\Model\Insurance\AbstractType;

class InsuranceExtractor extends ModelExtractor
{

	protected function extractRelationsBeforeSave($relationsContainer = null)
	{
		if ($this->oldModel) {
			$this->oldModel->getInsuranceDataModel();
		}
		parent::extractRelationsBeforeSave($relationsContainer);
	}

	/**
	 * @return array
	 */
	public function extractArrays()
	{
		$this->extractRelationsAfterSave();

		$newInfo = [];
		if ($this->newModel) {
			$newInfo = array_replace(
				$this->extractInsuranceData($this->newModel),
				$this->extractInsurance($this->newModel)
			);
		}

		$oldInfo = [];
		if ($this->oldModel) {
			$oldInfo = array_replace(
				$this->extractInsuranceData($this->oldModel),
				$this->extractInsurance($this->oldModel)
			);
		}

		if (isset($newInfo['type'], $oldInfo['type'])) {
			if (!$this->isIncompatibleInsuranceTypes($newInfo['type'], $oldInfo['type'])) {
				$resultOldInfo = [];
				foreach ($newInfo as $fieldName => $value) {
					if (array_key_exists($fieldName, $oldInfo)) {
						$resultOldInfo[$fieldName] = $oldInfo[$fieldName];
					}
				}
				$oldInfo = $resultOldInfo;
			} else {
				$oldInfo = [
					'type' => $oldInfo['type'],
					'order' => $oldInfo['order']
				];
			}
		}

		$ignored = [
			'id',
			'patient_id',
			'registration_id',
			'selected_insurance_id',
			'insurance_data_id',
			'insurance_verified',
			'is_pre_authorization_completed'
		];

		foreach ($ignored as $fieldName) {
			if (array_key_exists($fieldName, $newInfo)) {
				unset($newInfo[$fieldName]);
			}
		}

		return [$newInfo, $oldInfo];
	}

	/**
	 * @param AbstractType $model
	 * @return array
	 */
	protected function extractInsuranceData($model)
	{
		$dataModel = $model->getInsuranceDataModel();
		$insuranceData = $dataModel->as_array();

		if ($model->isRegularInsurance()) {
			$relationshipToPatient = $insuranceData['relationship_to_insured'];


			if ($relationshipToPatient !== null && $relationshipToPatient !== '' && $relationshipToPatient == 0) {
				$fieldsToRemove = [
					'first_name', 'last_name', 'middle_name', 'suffix', 'dob',
					'gender', 'phone', 'address', 'apt_number', 'country_id',
					'state_id', 'custom_city', 'city_id', 'custom_state', 'zip_code'
				];

				foreach ($fieldsToRemove as $fieldName) {
					if (array_key_exists($fieldName, $insuranceData)) {
						unset($insuranceData[$fieldName]);
					}
				}
			}
		}

		if (!empty($insuranceData['custom_city'])) {
			if (array_key_exists('city_id', $insuranceData)) {
				unset($insuranceData['city_id']);
			}
		} else {
			if (array_key_exists('custom_city', $insuranceData)) {
				unset($insuranceData['custom_city']);
			}
		}

		if (!empty($insuranceData['custom_state'])) {
			if (array_key_exists('state_id', $insuranceData)) {
				unset($insuranceData['state_id']);
			}
		} else {
			if (array_key_exists('custom_state', $insuranceData)) {
				unset($insuranceData['custom_state']);
			}
		}

		return $insuranceData;
	}

	/**
	 * @param AbstractType $model
	 * @return array
	 */
	protected function extractInsurance($model)
	{
		return $model->as_array();
	}

	protected function isIncompatibleInsuranceTypes($newType, $oldType)
	{
		$groups = [
			[
				AbstractType::INSURANCE_TYPE_LOP,
				AbstractType::INSURANCE_TYPE_SELF_PAY
			],
			[
				AbstractType::INSURANCE_TYPE_COMMERCIAL,
				AbstractType::INSURANCE_TYPE_MEDICARE,
				AbstractType::INSURANCE_TYPE_MEDICAID,
				AbstractType::INSURANCE_TYPE_OTHER,
				AbstractType::INSURANCE_TYPE_CHAMPVA,
				AbstractType::INSURANCE_TYPE_FECA_BLACK_LUNG,
				AbstractType::INSURANCE_TYPE_TRICARE
			],
			[
				AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT,
				AbstractType::INSURANCE_TYPE_WORKERS_COMP
			],
		];

		foreach ($groups as $group) {
			if (in_array($newType, $group) && in_array($oldType, $group)) {
				return false;
			}
		}

		return true;
	}
}

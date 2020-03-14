<?php

namespace Opake\ActivityLogger\Extractor\CaseItem;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class CaseExtractor extends ModelExtractor
{
	public function extractStudiesOrdered($case)
	{
		return $case->getStudiesOrdered();
	}

	public function extractPreOpRequiredData($case)
	{
		return $case->getPreOpRequiredData();
	}

	public function extractSecondaryDiagnosis($case)
	{
		return $case->registration->secondary_diagnosis->find_all();
	}

	public function extractAdmittingDiagnosis($case)
	{
		return $case->registration->admitting_diagnosis->find_all();
	}

	public function extractPatient()
	{
		if ($patient = $this->getAdditionalInfo('patient')) {
			if ($patient->loaded()) {
				$app = \Opake\Application::get();
				return $app->orm->get($patient->model_name, $patient->id());
			}
		}

		return null;
	}

	protected function extractRelationsBeforeSave($relationsContainer = null)
	{
		parent::extractRelationsBeforeSave($relationsContainer);

		/*$insurances = $relationsContainer->getRelation('insurances');
		foreach ($insurances as $insurance) {
			$insurance->getInsuranceDataModel();
		}*/
	}

	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [
			'registration' => ModelRelationsContainer::HAS_ONE,
		    'patient' => [ModelRelationsContainer::CUSTOM_METHOD, [$this, 'extractPatient']],
			//'insurances' => ModelRelationsContainer::HAS_MANY,
			'users' => ModelRelationsContainer::HAS_MANY,
			'assistant' => ModelRelationsContainer::HAS_MANY,
			'additional_cpts' => ModelRelationsContainer::HAS_MANY,
			'admitting_diagnosis' => [ModelRelationsContainer::CUSTOM_METHOD, [$this, 'extractAdmittingDiagnosis']],
			'secondary_diagnosis' => [ModelRelationsContainer::CUSTOM_METHOD, [$this, 'extractSecondaryDiagnosis']],
			'other_staff' => ModelRelationsContainer::HAS_MANY,
			'equipments' => ModelRelationsContainer::HAS_MANY,
			'implant_items' => ModelRelationsContainer::HAS_MANY,
			'studies_ordered' => [ModelRelationsContainer::CUSTOM_METHOD, [$this, 'extractStudiesOrdered']],
			'pre_op_required_data' => [ModelRelationsContainer::CUSTOM_METHOD, [$this, 'extractPreOpRequiredData']]
		];
	}

	/**
	 * @param AbstractModel $model
	 * @param ModelRelationsContainer $relationsContainer
	 * @return array
	 */
	protected function modelToArray($model, $relationsContainer)
	{
		$result = parent::modelToArray($model, $relationsContainer);

		$registration = $relationsContainer->getRelation('registration');
		if ($registration) {
			$result = array_replace($registration->as_array(), $result);
		}

		if ($patient = $relationsContainer->getRelation('patient')) {
			$result['mrn'] = $patient->getFullMrn();
		}

		$result['users'] = $relationsContainer->getRelationArrayOfIds('users');
		$result['assistant'] = $relationsContainer->getRelationArrayOfIds('assistant');
		$result['other_staff'] = $relationsContainer->getRelationArrayOfIds('other_staff');
		$result['additional_cpts'] = $relationsContainer->getRelationArrayOfIds('additional_cpts');
		$result['admitting_diagnosis'] = $relationsContainer->getRelationArrayOfIds('admitting_diagnosis');
		$result['secondary_diagnosis'] = $relationsContainer->getRelationArrayOfIds('secondary_diagnosis');
		$result['equipments'] = $relationsContainer->getRelationArrayOfIds('equipments');
		$result['implant_items'] = $relationsContainer->getRelationArrayOfIds('implant_items');
		//$result['insurances'] = $relationsContainer->getRelation('insurances');

		//this value is already an array of ids
		$result['studies_ordered']= $relationsContainer->getRelation('studies_ordered');
		$result['pre_op_required_data'] = $relationsContainer->getRelation('pre_op_required_data');

		if (!empty($result['custom_home_city'])) {
			if (array_key_exists('home_city_id', $result)) {
				unset($result['home_city_id']);
			}
		} else {
			if (array_key_exists('custom_home_city', $result)) {
				unset($result['custom_home_city']);
			}
		}

		if (!empty($result['custom_home_state'])) {
			if (array_key_exists('home_state_id', $result)) {
				unset($result['home_state_id']);
			}
		} else {
			if (array_key_exists('custom_home_state', $result)) {
				unset($result['custom_home_state']);
			}
		}

		return $result;
	}
}

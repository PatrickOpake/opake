<?php

namespace Opake\ActivityLogger\Extractor\Booking;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class BookingExtractor extends ModelExtractor
{

	public function extractStudiesOrdered($booking)
	{
		return $booking->getStudiesOrdered();
	}

	public function extractPreOpRequiredData($booking)
	{
		return $booking->getPreOpRequiredData();
	}

	protected function extractRelationsBeforeSave($relationsContainer = null)
	{
		parent::extractRelationsBeforeSave($relationsContainer);
		$insurances = $relationsContainer->getRelation('insurances');
		foreach ($insurances as $insurance) {
			$insurance->getInsuranceDataModel();
		}
	}

	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [
			'patient' => ModelRelationsContainer::HAS_ONE,
		    'booking_patient' => ModelRelationsContainer::HAS_ONE,
			'insurances' => ModelRelationsContainer::HAS_MANY,
		    'users' => ModelRelationsContainer::HAS_MANY,
		    'assistant' => ModelRelationsContainer::HAS_MANY,
		    'additional_cpts' => ModelRelationsContainer::HAS_MANY,
		    'admitting_diagnosis' => ModelRelationsContainer::HAS_MANY,
		    'secondary_diagnosis' => ModelRelationsContainer::HAS_MANY,
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

		$patient = $relationsContainer->getRelation('booking_patient');
		if (!$patient) {
			$patient = $relationsContainer->getRelation('patient');
		}

		if ($patient) {
			$result = array_replace($patient->as_array(), $result);
		}

		$result['users'] = $relationsContainer->getRelationArrayOfIds('users');
		$result['assistant'] = $relationsContainer->getRelationArrayOfIds('assistant');
		$result['other_staff'] = $relationsContainer->getRelationArrayOfIds('other_staff');
		$result['additional_cpts'] = $relationsContainer->getRelationArrayOfIds('additional_cpts');
		$result['admitting_diagnosis'] = $relationsContainer->getRelationArrayOfIds('admitting_diagnosis');
		$result['secondary_diagnosis'] = $relationsContainer->getRelationArrayOfIds('secondary_diagnosis');
		$result['equipments'] = $relationsContainer->getRelationArrayOfIds('equipments');
		$result['implant_items'] = $relationsContainer->getRelationArrayOfIds('implant_items');
		$result['insurances'] = $relationsContainer->getRelation('insurances');

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
<?php

namespace OpakeAdmin\Form\Booking;

use Opake\Form\AbstractForm;

class BookingSaveForm extends AbstractForm
{
	protected function prepareValues($data)
	{
		$result = parent::prepareValues($data);
		if (isset($result['time_start'])) {
			$result['time_start'] = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($result['time_start']));
		}
		if (isset($result['time_end'])) {
			$result['time_end'] = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($result['time_end']));
		}

		return $result;
	}

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$timeEnd = $this->getValueByName('time_end');
		$validator->field('users')->rule('filled')->error('You must specify Surgeon');
		$validator->field('time_start')->rule('filled')->rule('date')->error('Incorrect date or time of start');
		$validator->field('time_end')->rule('filled')->rule('date')->error('Incorrect date or time of end');
		$validator->field('time_start')->rule('sequence_dates', $timeEnd)->error('Length of Case must be positive');
		//$validator->field('additional_cpts')->rule('filled')->error('You must specify Procedure');
		//$validator->field('admitting_diagnosis')->rule('filled')->error('You must specify Primary diagnosis');

	}

	protected function getFields()
	{
		return [
			'organization_id',
			'users',
			'assistant',
			'other_staff',
			'admission_type',
			'room',
			'point_of_origin',
			'time_start' ,
			'time_end' ,
			'unable_to_work_from' ,
			'unable_to_work_to' ,
			'additional_cpts',
			'location',
			'date_of_injury',
			'admitting_diagnosis',
			'secondary_diagnosis',
			'pre_op_required_data',
			'studies_ordered',
			'anesthesia_type',
			'special_equipment_implants',
			'transportation',
			'transportation_notes',
			'implants',
			'implants_flag',
			'description',
			'studies_other',
			'anesthesia_other',
			'special_equipment_flag',
			'referring_provider_name',
			'referring_provider_npi',
			'is_unable_to_work',
			'implant_items',
			'equipments',
		];
	}


}

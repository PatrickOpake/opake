<?php

namespace Opake\ActivityLogger\Action\Booking;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Comparer\BookingComparer;
use Opake\ActivityLogger\Extractor\Booking\BookingExtractor;

class BookingChangeAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();

		return [
			'booking_id' => $model->id
		];
	}

	protected function getFieldsForCompare()
	{
		return [
			'last_name',
			'first_name',
			'middle_name',
			'status_marital',
			'suffix',
			'parents_name',
			'home_address',
			'home_apt_number',
			'home_state_id',
			'home_city_id',
			'home_zip_code',
			'home_country_id',
			'home_phone',
			'home_phone_type',
			'custom_home_state',
			'custom_home_city',
			'additional_phone',
			'additional_phone_type',
			'home_email',
			'dob',
			'ssn',
			'gender',
			'status_martial',
			'ec_name',
			'ec_phone_number',
			'ec_phone_type',
			'time_start',
			'time_end',
			'room_id',
			'pre_op_required_data',
			'admission_type',
			'anesthesia_type',
			'location',
			'special_equipment_flag',
			'special_equipment_implants',
			'implants_flag',
			'implants',
			'studies_ordered',
			'transportation',
			'transportation_notes',
			'description',
			'users',
			'assistant',
			'additional_cpts',
			'admitting_diagnosis',
			'secondary_diagnosis',
		    'insurances',
		    'studies_other',
		    'other_staff',
		    'relationship',
		    'anesthesia_other',
		    'equipments',
		    'implant_items',
			'point_of_contact_phone',
			'point_of_contact_phone_type',
			'is_unable_to_work',
			'unable_to_work_from',
			'unable_to_work_to',
		];
	}

	protected function createExtractor()
	{
		return new BookingExtractor();
	}

	protected function createComparer()
	{
		return new BookingComparer();
	}
}
<?php

namespace Opake\Model;

use Opake\Model\Cases\Item;
use Opake\Model\Cases\Registration;
use Opake\Model\User;
use Opake\Helper\TimeFormat;

class Booking extends AbstractModel
{

	const STUDIES_ORDERED_NONE = 0;
	const STUDIES_ORDERED_CBC = 1;
	const STUDIES_ORDERED_CHEMS = 2;
	const STUDIES_ORDERED_EKG = 3;
	const STUDIES_ORDERED_PTT = 4;
	const STUDIES_ORDERED_CXR = 5;
	const STUDIES_ORDERED_LFT = 6;
	const STUDIES_ORDERED_DIG = 7;
	const STUDIES_ORDERED_OTHER = 9;

	public $id_field = 'id';
	public $table = 'booking_sheet';
	protected $_row = [
		'id' => null,
		'organization_id' => '',
		'status' => 0,
		'is_updated_by_satellite' => null,
		'booking_patient_id' => null,
		'patient_id' => null,
		'time_start' => null,
		'time_end' => null,
		'room_id' => null,
		'location' => 0,
		'studies_other' => '',
		'anesthesia_type' => Item::ANESTHESIA_TYPE_NOT_SPECIFIED,
		'anesthesia_other' => '',
		'special_equipment_implants' => '',
		'special_equipment_flag' => null,
		'implants' => '',
		'implants_flag' => null,
		'transportation' => null,
		'transportation_notes' => '',
		'description' => '',
		'point_of_origin' => null,
		'referring_provider_name' => '',
		'referring_provider_npi' => '',
		'prior_auth_number' => null,
		'date_of_injury' => null,
		'is_unable_to_work' => null,
		'unable_to_work_from' => null,
		'unable_to_work_to' => null,
//		Reg
		'patients_relations' => 4,
		'admission_type' => Registration::ADMISSION_TYPE_ELECTIVE,
//		Auto
		'auto_insurance_name' => '',
		'auto_adjust_name' => '',
		'auto_claim' => '',
		'auto_adjuster_phone' => null,
		'auto_insurance_address' => '',
		'auto_city_id' => null,
		'auto_state_id' => null,
		'auto_zip' => '',
		'auto_insurance_company_phone' => null,
		'auto_insurance_authorization_number' => null,
		'accident_date' => null,
		'attorney_name' => '',
		'attorney_phone' => null,
		'auto_is_primary' => null,
//		Work
		'work_comp_insurance_name' => '',
		'work_comp_adjusters_name' => '',
		'work_comp_claim' => '',
		'work_comp_adjuster_phone' => null,
		'work_comp_insurance_company_phone' => null,
		'work_comp_authorization_number' => null,
		'work_comp_insurance_address' => '',
		'work_comp_city_id' => null,
		'work_comp_state_id' => null,
		'work_comp_zip' => '',
		'work_comp_accident_date' => null,
		'work_comp_is_primary' => null,
		'notes_count' => 0,
	];

	protected $belongs_to = [
		'room' => [
			'model' => 'location',
			'key' => 'room_id'
		],
		'organization' => [
			'model' => 'Organization',
			'key' => 'organization_id'
		],
		'patient' => [
			'model' => 'Patient',
			'key' => 'patient_id'
		],
		'booking_patient' => [
			'model' => 'Booking_Patient',
			'key' => 'booking_patient_id',
			'cascade_delete' => true
		],
	];

	protected $has_one = [
		'template_snapshot' => [
			'model' => 'BookingSheetTemplate_Snapshot',
		    'key' => 'booking_id',
		    'cascade_delete' => true
		]
	];

	protected $has_many = [
		'users' => [
			'model' => 'User',
			'through' => 'booking_user',
			'key' => 'booking_id',
			'foreign_key' => 'user_id',
			'overwrite' => [
				'replace' => true,
				'ordering' => true
			]
		],
		'assistant' => [
			'model' => 'User',
			'through' => 'booking_assistant',
			'key' => 'booking_id',
			'foreign_key' => 'assistant_id',
			'overwrite' => [
				'replace' => true,
				'ordering' => true
			]
		],
		'other_staff' => [
			'model' => 'User',
			'through' => 'booking_other_staff',
			'key' => 'booking_id',
			'foreign_key' => 'staff_id',
			'overwrite' => [
				'replace' => true,
				'ordering' => true
			]
		],
		'additional_cpts' => [
			'model' => 'Cases_Type',
			'through' => 'booking_additional_type',
			'key' => 'booking_id',
			'foreign_key' => 'type_id',
			'overwrite' => [
				'replace' => true,
			    'ordering' => true
			]
		],
		'admitting_diagnosis' => [
			'model' => 'ICD',
			'through' => 'booking_admitting_diagnosis',
			'key' => 'booking_id',
			'foreign_key' => 'diagnosis_id'
		],
		'secondary_diagnosis' => [
			'model' => 'ICD',
			'through' => 'booking_secondary_diagnosis',
			'key' => 'booking_id',
			'foreign_key' => 'diagnosis_id'
		],
		'equipments' => [
			'model' => 'Inventory',
			'through' => 'booking_equipment',
			'key' => 'booking_id',
			'foreign_key' => 'inventory_id'
		],
		'implant_items' => [
			'model' => 'Inventory',
			'through' => 'booking_implant',
			'key' => 'booking_id',
			'foreign_key' => 'inventory_id'
		],
		'insurances' => [
			'model' => 'Booking_Insurance',
			'key' => 'booking_id',
			'cascade_delete' => true
		],
		'notes' => [
			'model' => 'Booking_Note',
			'key' => 'booking_id',
			'cascade_delete' => true
		]
	];

	const STATUS_NEW = 0;
	const STATUS_SCHEDULED = 1;
	const STATUS_SUBMITTED = 2;
	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	protected $formatters = [
		'BookingQueue' => [
			'class' => '\Opake\Formatter\Booking\BookingQueueFormatter'
		]
	];

	public function save()
	{
		$this->is_updated_by_satellite = $this->pixie->auth->user()->isSatelliteOffice();

		parent::save();
	}

	public function fromArray($data)
	{
		if (isset($data->accident_date) && $data->accident_date) {
			$data->accident_date = TimeFormat::formatToDB($data->accident_date);
		}
		if (isset($data->date_of_injury) && $data->date_of_injury) {
			$data->date_of_injury = TimeFormat::formatToDB($data->date_of_injury);
		}
		if (isset($data->unable_to_work_from) && $data->unable_to_work_from) {
			$data->unable_to_work_from = TimeFormat::formatToDB($data->unable_to_work_from);
		}
		if (isset($data->unable_to_work_to) && $data->unable_to_work_to) {
			$data->unable_to_work_to = TimeFormat::formatToDB($data->unable_to_work_to);
		}

		if (isset($data->auto_state) && $data->auto_state) {
			$data->auto_state_id = $data->auto_state->id;
		}
		if (isset($data->auto_city) && $data->auto_city) {
			$data->auto_city_id = $data->auto_city->id;
		}

		if (isset($data->work_comp_accident_date) && $data->work_comp_accident_date) {
			$data->work_comp_accident_date = TimeFormat::formatToDB($data->work_comp_accident_date);
		}

		if (isset($data->work_comp_state) && $data->work_comp_state) {
			$data->work_comp_state_id = $data->work_comp_state->id;
		}
		if (isset($data->work_comp_city) && $data->work_comp_city) {
			$data->work_comp_city_id = $data->work_comp_city->id;
		}

		if (isset($data->admitting_diagnosis) && $data->admitting_diagnosis) {
			$admitting_diagnosis = [];
			foreach ($data->admitting_diagnosis as $diagnosis) {
				$admitting_diagnosis[] = $diagnosis->id;
			}
			$data->admitting_diagnosis = $admitting_diagnosis;
		}
		if (isset($data->secondary_diagnosis) && $data->secondary_diagnosis) {
			$secondary_diagnosis = [];
			foreach ($data->secondary_diagnosis as $diagnosis) {
				$secondary_diagnosis[] = $diagnosis->id;
			}
			$data->secondary_diagnosis = $secondary_diagnosis;
		}

		if (isset($data->equipments) && $data->equipments) {
			$equipments = [];
			foreach ($data->equipments as $equipment) {
				if ($equipment->id) {
					$equipments[] = $equipment->id;
				} else {
					$model = $this->pixie->orm->get('Inventory');
					$inventory = $model->addCustomRecord($equipment->organization_id, $equipment->name, $equipment->type);
					$equipments[] = $inventory->id;
				}
			}
			$data->equipments = $equipments;
		}

		if (isset($data->implant_items) && $data->implant_items) {
			$implant_items = [];
			foreach ($data->implant_items as $implant) {
				if ($implant->id) {
					$implant_items[] = $implant->id;
				} else {
					$model = $this->pixie->orm->get('Inventory');
					$inventory = $model->addCustomRecord($implant->organization_id, $implant->name, $implant->type);
					$implant_items[] = $inventory->id;
				}
			}
			$data->implant_items = $implant_items;
		}

		$cityFields = [
			'auto_city' => 'auto_city_id',
			'work_comp_city' => 'work_comp_city_id'
		];

		foreach ($cityFields as $fieldName => $idFieldName) {
			if (property_exists($data, $fieldName)) {
				if (!empty($data->$fieldName->id)) {
					$data->$idFieldName = $data->$fieldName->id;
				} else if (!empty($data->$fieldName->name)) {
					$model = $this->pixie->orm->get('Geo_City');

					$organizationId = null;
					if (isset($data->organization->id)) {
						$organizationId = $data->organization->id;
					} else if (isset($data->organization_id)) {
						$organizationId = $data->organization_id;
					} else {
						throw new \Exception('Can\'t add new city without ID of organization');
					}

					$city = $model->addCustomRecord($organizationId, $data->$fieldName->state_id, $data->$fieldName->name);
					$data->$idFieldName = $city->id();
				} else if ($data->$fieldName === null) {
					$data->$idFieldName = null;
				}
				unset($data->$fieldName);
			}
		}

		if (isset($data->time_start) && $data->time_start) {
			$data->time_start = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($data->time_start));
		}
		if (isset($data->time_end) && $data->time_end) {
			$data->time_end = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($data->time_end));
		}
		if (isset($data->room) && $data->room) {
			$data->room_id = $data->room->id;
		}
		if (isset($data->users) && $data->users) {
			$users = [];
			foreach ($data->users as $user) {
				$users[] = $user->id;
			}
			$data->users = $users;
		}

		if (isset($data->assistant) && $data->assistant) {
			$assistant = [];
			foreach ($data->assistant as $user) {
				$assistant[] = $user->id;
			}
			$data->assistant = $assistant;
		}

		if (isset($data->other_staff) && $data->other_staff) {
			$other_staff = [];
			foreach ($data->other_staff as $user) {
				$other_staff[] = $user->id;
			}
			$data->other_staff = $other_staff;
		}

		if (isset($data->additional_cpts) && $data->additional_cpts) {
			$additional_cpts = [];
			foreach ($data->additional_cpts as $additional_cpt) {
				$additional_cpts[] = $additional_cpt->id;
			}
			$data->additional_cpts = $additional_cpts;
		}

		unset($data->notes_count);

		return $data;
	}

	public function getUsers()
	{
		if (!$this->loaded()) {
			return [];
		}

		$model = $this->pixie->orm->get('User');
		$model->query->join('booking_user', ['user.id', 'booking_user.user_id'], 'inner');
		$model->query->where('booking_user.booking_id', $this->id());
		$model->query->fields('user.*');
		return $model->order_by('booking_user.order')->find_all()->as_array();
	}

	public function hasUsers()
	{
		$model = $this->pixie->orm->get('User');
		$model->query->join('booking_user', ['user.id', 'booking_user.user_id'], 'inner');
		$model->query->where('booking_user.booking_id', $this->id());
		$model->query->limit(1);

		return ($model->count_all() > 0);
	}

	public function hasAdmittingDiagnosis()
	{
		return ($this->admitting_diagnosis->limit(1)->count_all() > 0);
	}

	public function hasAdditionalCpts()
	{
		return ($this->additional_cpts->limit(1)->count_all() > 0);
	}

	public function getFirstSurgeon() {
		$result = '';
		$users = $this->getUsers();
		if($users) {
			$result = $users[0]->getFullName();
			if ($this->users->count_all() > 1) {
				$result .= ' ...';
			}
		}

		return $result;
	}

	public function hasUnreadNotesForUser($userId)
	{
		$query = $this->pixie->db->query('select')
			->table('user_booking_note')
			->fields('last_read_note_id')
			->where([['user_id', $userId], ['booking_id', $this->id]])
			->execute()
			->current();

		if (!$query) {
			$this->pixie->db->query('insert')
				->table('user_booking_note')
				->data(['user_id' => $userId, 'booking_id' => $this->id])
				->execute();

			return true;
		} else {
			$lastReadNoteId = $query->last_read_note_id;
			$caseNotes = $this->notes->find_all()->as_array();

			if (count($caseNotes) && ($lastReadNoteId < $caseNotes[count($caseNotes) - 1]->id)) {
				return true;
			} else {
				return false;
			}
		}
	}

	public function readNotes($userId)
	{
		$caseNote = $this->notes->order_by('id', 'DESC')->limit(1)->find();
		$lastCaseNoteId = $caseNote->id;

		$this->pixie->db->query('update')
			->table('user_booking_note')
			->data(['last_read_note_id' => $lastCaseNoteId])
			->where([['user_id', $userId], ['booking_id', $this->id]])
			->execute();
	}

	public function updateNotesCount()
	{
		$this->conn->query('update')->table($this->table)
			->data(['notes_count' => $this->pixie->db->expr('notes_count + 1')])
			->where('id', $this->id)
			->execute();
	}

	public function reduceNotesCount()
	{
		$this->conn->query('update')->table($this->table)
			->data(['notes_count' => $this->pixie->db->expr('notes_count - 1')])
			->where('id', $this->id)
			->execute();
	}

	public function getCountNotes()
	{
		$notes = $this->notes;
		$patient_id = $this->patient_id ? $this->patient_id : $this->booking_patient_id;
		$notes->where([
			['patient_id', $patient_id],
			['or', ['patient_id', 'IS NULL', $this->pixie->db->expr('')]],
		]);
		return $notes->count_all();
	}

	public function toArray()
	{
		$data = parent::toArray();

		$insurances = [];
		foreach ($this->insurances->find_all() as $insurance) {
			$insurances[] = $insurance->toArray();
		}
		$data['insurances'] = $insurances;

		$admitting_diagnosis = [];
		foreach ($this->admitting_diagnosis->find_all() as $diagnosis) {
			$admitting_diagnosis[] = $diagnosis->toArray();
		}
		$data['admitting_diagnosis'] = $admitting_diagnosis;

		$secondary_diagnosis = [];
		foreach ($this->secondary_diagnosis->find_all() as $diagnosis) {
			$secondary_diagnosis[] = $diagnosis->toArray();
		}
		$data['secondary_diagnosis'] = $secondary_diagnosis;

		$equipments = [];
		foreach ($this->equipments->find_all() as $equipment) {
			$equipments[] = $equipment->toShortArray();
		}
		$data['equipments'] = $equipments;

		$implant_items = [];
		foreach ($this->implant_items->find_all() as $implant) {
			$implant_items[] = $implant->toShortArray();
		}
		$data['implant_items'] = $implant_items;

		$additional_cpts = [];
		foreach ($this->additional_cpts->find_all() as $diagnosis) {
			$additional_cpts[] = $diagnosis->toArray();
		}
		$data['additional_cpts'] = $additional_cpts;

		$pre_op_required_data = [];
		foreach ($this->getPreOpRequiredData() as $pre_op) {
			$pre_op_required_data[] = $pre_op;
		}
		$data['pre_op_required_data'] = $pre_op_required_data;

		$studies_ordered = [];
		foreach ($this->getStudiesOrdered() as $studies_order) {
			$studies_ordered[] = $studies_order;
		}
		$data['studies_ordered'] = $studies_ordered;

		$users = [];
		foreach ($this->getUsers() as $user) {
			$users[] = $user->toArray();
		}
		$data['users'] = $users;

		$assistant = [];
		foreach ($this->assistant->find_all() as $user) {
			$assistant[] = $user->toArray();
		}
		$data['assistant'] = $assistant;

		$other_staff = [];
		foreach ($this->other_staff->find_all() as $user) {
			$other_staff[] = $user->toArray();
		}
		$data['other_staff'] = $other_staff;

		$data['time_start'] = date('D M d Y H:i:s O', strtotime($this->time_start));
		$data['time_end'] = date('D M d Y H:i:s O', strtotime($this->time_end));
		$data['display_point_of_contact'] = (bool)$this->organization->sms_template->poc_sms;
		$data['is_unable_to_work'] = (bool) $this->is_unable_to_work;

		if ($user = $this->pixie->auth->user()) {
			$data['is_self_for_user'] = $this->isSelf($user);
		}

		$data['charts_count'] = (int) $this->getCharts()->count_all();
		$data['has_flagged_comments'] = (int) $this->hasFlaggedComments();
		$data['notes_count'] = $this->getCountNotes();


		return $data;
	}

	public function toShortArray()
	{
		$data = [
			'id' => $this->id(),
			'patient_id' => $this->patient_id,
			'patient_name' => $this->patient->getFullNameForBooking(),
			'mrn' => $this->patient->getFullMrn(),
			'booking_patient_name' => $this->booking_patient->getFullNameForBooking(),
			'first_surgeon' => $this->getFirstSurgeon(),
			'time_start' => date('D M d Y H:i:s O', strtotime($this->time_start)),
			'time_end' => date('D M d Y H:i:s O', strtotime($this->time_end)),
			'status' => (int) $this->status,
			'charts_count' => (int) $this->getCharts()->count_all(),
			'is_valid_for_schedule' => $this->isValidForSchedule(),
			'notes_count' => (int) $this->notes_count,
			'has_flagged_comments' => $this->hasFlaggedComments()
		];

		if ($user = $this->pixie->auth->user()) {
			$data['is_self_for_user'] = $this->isSelf($user);
		}

		return $data;
	}

	public function getCharts()
	{
		$charts = $this->pixie->orm->get('Cases_Chart');
		$chartsQuery = $charts->query;
		$chartsQuery->fields('case_chart.*');

		$chartsQuery->join('case_booking_list', ['case_booking_list.id', 'case_chart.list_id'], 'inner')
			->where('case_booking_list.booking_id', $this->id);

		$charts->where('is_booking_sheet', 0);

		return $charts;
	}

	public function getCaseBookingListId()
	{
		$query = $this->pixie->db->query('select')
			->table('case_booking_list')
			->fields('id')
			->where(['booking_id', $this->id])
			->execute()
			->current();

		if ($query) {
			return $query->id;
		} else {
			$this->pixie->db->query('insert')
				->table('case_booking_list')
				->data(['booking_id' => $this->id])
				->execute();

			return $this->pixie->db->insert_id();
		}
	}

	public function addCaseIdToCaseBookingList($caseId)
	{
		$this->getCaseBookingListId();

		$this->pixie->db->query('update')
			->table('case_booking_list')
			->data(['case_id' => $caseId])
			->where(['booking_id', $this->id])
			->execute();
	}

	public function updatePreOpRequiredData(array $data)
	{
		$this->pixie->db->query('delete')->table('booking_pre_op_required_data')->where('booking_id', $this->id)->execute();
		foreach ($data as $num => $preOp) {
			$this->conn->query('insert')->table('booking_pre_op_required_data')
				->data([
					'pre_op_required' => $preOp,
					'booking_id' => $this->id,
				    'order' => $num
				])
				->execute();
		}
	}

	public function getPreOpRequiredData()
	{
		$ids = [];
		$rows = $this->pixie->db->query('select')
			->table('booking_pre_op_required_data')
			->fields('pre_op_required')
			->where('booking_id', $this->id())
			->order_by('order')
			->execute();

		foreach ($rows as $row) {
			$ids[] = $row->pre_op_required;
		}
		return $ids;
	}

	public function updateStudiesOrdered(array $data)
	{
		$this->pixie->db->query('delete')->table('booking_studies_ordered')->where('booking_id', $this->id)->execute();
		foreach ($data as $num => $studies_order) {
			$this->conn->query('insert')->table('booking_studies_ordered')
				->data([
					'studies_order' => $studies_order,
					'booking_id' => $this->id,
				    'order' => $num
				])
				->execute();
		}
	}

	public function getStudiesOrdered()
	{
		$ids = [];
		$rows = $this->pixie->db->query('select')
			->table('booking_studies_ordered')
			->fields('studies_order')
			->where('booking_id', $this->id())
			->order_by('order')
			->execute();

		foreach ($rows as $row) {
			$ids[] = $row->studies_order;
		}
		return $ids;
	}

	public function getSelectedInsurances()
	{
		return $this->insurances
			->where('order', 'IS NOT NULL', $this->pixie->db->expr(''))
			->order_by('order', 'ASC')
			->find_all();
	}

	public function getPrimaryInsurance()
	{
		$insurance = $this->insurances
			->where('order', 'IS NOT NULL', $this->pixie->db->expr(''))
			->order_by('order', 'ASC')
			->find();

		return ($insurance->loaded()) ? $insurance : null;
	}

	public function isSelf($user)
	{
		$usePracticeGroups = false;
		$userPracticeGroupIds = [];

		if ($user->isSatelliteOffice()) {
			$userPracticeGroupIds = $user->getPracticeGroupIds();
			$usePracticeGroups = true;
			if ($this->status != self::STATUS_NEW) {
				return false;
			}
		}

		/** @var User $bookingUser */
		foreach ($this->users->find_all() as $bookingUser) {
			if ($bookingUser->id() == $user->id()) {
				return true;
			}

			if ($usePracticeGroups) {
				if ($bookingUser->organization_id == $user->organization_id) {
					$caseUserPracticeGroup = $bookingUser->getPracticeGroupIds();
					foreach ($userPracticeGroupIds as $id) {
						if (in_array($id, $caseUserPracticeGroup)) {
							return true;
						}
					}
				}
			}
		}

		return parent::isSelf($user);
	}

	public function isValidForSchedule()
	{

		if (!$this->time_start || !$this->time_end) {
			return false;
		}

		if (!$this->patient->loaded()) {
			$patient = $this->booking_patient;
		} else {
			$patient = $this->patient;
		}

		if (!$patient->last_name || !$patient->first_name || !$patient->dob || !$patient->home_address || !$patient->home_state || !$patient->home_city || !$patient->gender) {
			return false;
		}

		if (!$patient->point_of_contact_phone || !$patient->point_of_contact_phone_type) {
			return false;
		}

		if (!$this->hasUsers() /*|| !$this->hasAdmittingDiagnosis() || !$this->hasAdditionalCpts()*/) {
			return false;
		}

		return true;
	}

	protected function hasFlaggedComments()
	{
		if ($this->patient_id) {
			$caseNotes = $this->pixie->orm->get('Booking_Note')->where('patient_id', $this->patient_id);
	
			if ($caseNotes->count_all()) {
				return true;
			}
		}

		return false;
	}

	public static function getLocationList()
	{
		return [
			0 => 'NA',
			1 => 'Left',
			2 => 'Right',
			3 => 'Bilateral'
		];
	}

	public static function getPreOpRequiredList()
	{
		return [
			0 => 'None',
			1 => 'Medical Clearance',
			2 => 'Pre-Op Labs',
			3 => 'X-Ray',
			4 => 'EKG'
		];
	}

	public static function getStudiesOrderedList()
	{
		return [
			0 => 'None',
			1 => 'CBC',
			2 => 'CHEMS',
			3 => 'EKG',
			4 => 'PT/PTT',
			5 => 'CXR',
			6 => 'LFT’s',
			7 => 'Dig Level',
			9 => 'Other'
		];
	}

	public static function getTransportationList()
	{
		return [
			0 => 'No',
			1 => 'Yes'
		];
	}

}

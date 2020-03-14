<?php

namespace Opake\Model\Cases;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;
use Opake\Model\User;

/**
 * A case model
 *
 * @property-read \Opake\Model\Cases\Registration $registration
 * @property-read \Opake\Model\Cases\Coding $coding
 * @package Opake\Model\Cases
 */
class Item extends AbstractModel
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

	const PRE_OP_NONE = 0;
	const PRE_OP_MEDICAL_CLEARANCE = 1;
	const PRE_OP_LABS = 2;
	const PRE_OP_XRAY = 3;
	const PRE_OP_EKG = 4;

	const POINT_OF_ORIGIN_NON_HEALTH = 1;
	const POINT_OF_ORIGIN_NON_CLINIC = 2;
	const POINT_OF_ORIGIN_NON_HOSPITAL = 3;
	const POINT_OF_ORIGIN_NON_SNF = 4;
	const POINT_OF_ORIGIN_NON_FACILITY = 5;
	const POINT_OF_ORIGIN_NON_EMERGENCY = 6;
	const POINT_OF_ORIGIN_NON_COURT = 7;
	const POINT_OF_ORIGIN_NON_INFO_NOT_AVAIL= 8;
	const POINT_OF_ORIGIN_NON_TRANSFER_SAME_HOSPITAL = 9;
	const POINT_OF_ORIGIN_NON_AMBULATORY= 10;
	const POINT_OF_ORIGIN_NON_HOSPICE_FACILITY = 11;

	public $id_field = 'id';
	public $table = 'case';
	protected $_row = [
		'id' => null,
		'time_start' => null,
		'time_end' => null,
		'time_check_in' => null,
		'time_start_in_fact' => null,
		'time_end_in_fact' => null,
		'type_id' => null,
		'organization_id' => '',
		'location_id' => '',
		'description' => '',
		'stage' => self::STAGE_INTAKE,
		'phase' => self::STAGE_INTAKE_PHASE_PATIENT_INFO,
		'state' => '',
		'appointment_status' => self::APPOINTMENT_STATUS_NEW,
		'is_remained_in_billing' => 0,
		'status' => 0,
		'alert_status' => 0,
		'started_at' => null,
		'notes_count' => 0,
		'billing_notes_count' => 0,
		'accompanied_by' => '',
		'accompanied_phone' => null,
		'accompanied_email' => '',
		// Surgery details
		'studies_other' => '',
		'anesthesia_type' => self::ANESTHESIA_TYPE_NOT_SPECIFIED,
		'anesthesia_other' => '',
		'special_equipment_required' => null,
		'special_equipment_implants' => '',
		'special_equipment_flag' => null,
		'implants' => '',
		'implants_flag' => null,
		'locate' => 0,
		'transportation' => null,
		'transportation_notes' => '',
		'point_of_origin' => null,
		'referring_provider_name' => '',
		'referring_provider_npi' => '',
		'prior_auth_number' => null,
		'date_of_injury' => null,
		'is_unable_to_work' => null,
		'unable_to_work_from' => null,
		'unable_to_work_to' => null,
		'billing_status' => 0,
	];

	protected $belongs_to = [
		'type' => [
			'model' => 'Cases_Type',
			'key' => 'type_id'
		],
		'location' => [
			'model' => 'location',
			'key' => 'location_id'
		],
		'organization' => [
			'model' => 'Organization',
			'key' => 'organization_id'
		]
	];

	protected $has_one = [
		'registration' => [
			'model' => 'Cases_Registration',
			'key' => 'case_id',
			'cascade_delete' => true
		],
		'coding' => [
			'model' => 'Cases_Coding',
			'key' => 'case_id',
			'cascade_delete' => true
		],
		'claim' => [
			'model' => 'Cases_Claim',
			'key' => 'case_id',
			'cascade_delete' => true
		],
		'fields_template' => [
			'model' => 'Cases_OperativeReport_Template',
			'key' => 'case_id'
		],
		'drivers_license' => [
			'model' => 'Cases_DriversLicense',
			'key' => 'case_id',
			'cascade_delete' => true
		],
		'insurance_card' => [
			'model' => 'Cases_InsuranceCard',
			'key' => 'case_id',
			'cascade_delete' => true
		],
		'card_staff' => [
			'model' => 'Card_Staff',
			'key' => 'case_id',
			'cascade_delete' => true
		],
	];

	protected $has_many = [
		'users' => [
			'model' => 'User',
			'through' => 'case_user',
			'key' => 'case_id',
			'foreign_key' => 'user_id',
		    'overwrite' => [
			    'replace' => true,
		        'ordering' => true
		    ]
		],
		'surgeon_assistant' => [
			'model' => 'User',
			'through' => 'case_surgeon_assistant',
			'key' => 'case_id',
			'foreign_key' => 'surgeon_assistant_id',
		],
		'co_surgeon' => [
			'model' => 'User',
			'through' => 'case_co_surgeon',
			'key' => 'case_id',
			'foreign_key' => 'co_surgeon_id'
		],
		'supervising_surgeon' => [
			'model' => 'User',
			'through' => 'case_supervising_surgeon',
			'key' => 'case_id',
			'foreign_key' => 'supervising_surgeon_id'
		],
		'first_assistant_surgeon' => [
			'model' => 'User',
			'through' => 'case_first_assistant_surgeon',
			'key' => 'case_id',
			'foreign_key' => 'assistant_surgeon_id'
		],
		'assistant' => [
			'model' => 'User',
			'through' => 'case_assistant',
			'key' => 'case_id',
			'foreign_key' => 'assistant_id',
			'overwrite' => [
				'replace' => true,
				'ordering' => true
			]
		],
		'anesthesiologist' => [
			'model' => 'User',
			'through' => 'case_anesthesiologist',
			'key' => 'case_id',
			'foreign_key' => 'anesthesiologist_id'
		],
		'dictated_by' => [
			'model' => 'User',
			'through' => 'case_dictated_by',
			'key' => 'case_id',
			'foreign_key' => 'dictated_by_id'
		],
		'other_staff' => [
			'model' => 'User',
			'through' => 'case_other_staff',
			'key' => 'case_id',
			'foreign_key' => 'staff_id'
		],
		'equipments' => [
			'model' => 'Inventory',
			'through' => 'case_equipment',
			'key' => 'case_id',
			'foreign_key' => 'inventory_id'
		],
		'implant_items' => [
			'model' => 'Inventory',
			'through' => 'case_implant',
			'key' => 'case_id',
			'foreign_key' => 'inventory_id'
		],
		'discharge_docs' => [
			'model' => 'Cases_Discharge',
			'key' => 'case_id',
			'cascade_delete' => true
		],
		'notes' => [
			'model' => 'Cases_Note',
			'key' => 'case_id',
			'cascade_delete' => true
		],
		'billing_notes' => [
			'model' => 'Billing_Note',
			'key' => 'case_id',
			'cascade_delete' => true
		],
		'op_reports' => [
			'model' => 'Cases_OperativeReport',
			'key' => 'case_id',
			'cascade_delete' => true
		],
		'inventory_items' => [
			'model' => 'Cases_InventoryItem',
			'key' => 'case_id',
			'cascade_delete' => true
		],
		'additional_cpts' => [
			'model' => 'Cases_Type',
			'through' => 'case_additional_type',
			'key' => 'case_id',
			'foreign_key' => 'type_id',
			'overwrite' => [
				'replace' => true,
				'ordering' => true
			]
		],
		'time_logs' => [
			'model' => 'Cases_TimeLog',
			'key' => 'case_id',
			'cascade_delete' => true
		],
		'alerts' => [
			'model' => 'Cases_Alert',
			'key' => 'case_id',
			'cascade_delete' => true
		],
		'sms_logs' => [
			'model' => 'Cases_SmsLog',
			'key' => 'case_id',
			'cascade_delete' => true
		],
	    'ledger_interest_payments' => [
		    'model' => 'Billing_Ledger_InterestPayment',
	        'key' => 'case_id',
	        'cascade_delete' => true
	    ]
	];

	protected $formatters = [
		'VerificationList' => [
			'class' => '\Opake\Formatter\Cases\Item\VerificationListFormatter'
		],
		'CardListFormatter' => [
			'class' => '\Opake\Formatter\Cases\Item\CardListFormatter'
		],
		'DatePickerAlertsFormatter' => [
			'class' => '\Opake\Formatter\Cases\Item\DatePickerAlertsFormatter'
		],
		'FinancialDocsFormatter' => [
			'class' => '\Opake\Formatter\Cases\Item\FinancialDocsFormatter'
		],
		'BillingList' => [
			'class' => '\Opake\Formatter\Cases\Item\BillingListFormatter'
		],
		'CollectionList' => [
			'class' => '\Opake\Formatter\Cases\Item\CollectionListFormatter'
		],
	    'LedgerListEntry' => [
		    'class' => '\Opake\Formatter\Billing\Ledger\CaseFormatter'
	    ]
	];

	const STAGE_INTAKE = 'intake';
	const STAGE_CLINICAL = 'clinical';
	const STAGE_BILLING = 'billing';

	const STAGE_INTAKE_PHASE_PATIENT_INFO = 'patient_info';
	const STAGE_INTAKE_PHASE_INSURANCE = 'insurance';
	const STAGE_INTAKE_PHASE_ADDITIONAL = 'additional';

	const STAGE_CLINICAL_PHASE_PRE_OP = 'pre_op';
	const STAGE_CLINICAL_PHASE_OPERATION = 'operation';
	const STAGE_CLINICAL_PHASE_POST_OP = 'post_op';
	const STAGE_CLINICAL_PHASE_REPORT = 'report';
	const STAGE_CLINICAL_PHASE_DISCHARGE = 'discharge';

	const STAGE_BILLING_PHASE_CODING = 'coding';

	const APPOINTMENT_STATUS_NEW = 0;
	const APPOINTMENT_STATUS_CANCELED = 1;
	const APPOINTMENT_STATUS_COMPLETED = 2;

	const ANESTHESIA_TYPE_GEN = 0;
	const ANESTHESIA_TYPE_MAC = 1;
	const ANESTHESIA_TYPE_SED = 2;
	const ANESTHESIA_TYPE_LOCAL = 3;
	const ANESTHESIA_TYPE_BLOCK = 4;
	const ANESTHESIA_TYPE_OTHER = 5;
	const ANESTHESIA_TYPE_NOT_SPECIFIED = 6;

	const BILLING_STATUS_BEGIN = 1;
	const BILLING_STATUS_CONTINUE = 2;
	const BILLING_STATUS_COMPLETE = 3;
	const BILLING_STATUS_READY = 4;

	protected static $phases = [
		self::STAGE_INTAKE => [
			self::STAGE_INTAKE_PHASE_PATIENT_INFO => 'Patient Info',
			self::STAGE_INTAKE_PHASE_INSURANCE => 'Insurance',
			self::STAGE_INTAKE_PHASE_ADDITIONAL => 'Forms'
		],
		self::STAGE_CLINICAL => [
			self::STAGE_CLINICAL_PHASE_PRE_OP => 'Pre-Op',
			self::STAGE_CLINICAL_PHASE_OPERATION => 'Operation',
			self::STAGE_CLINICAL_PHASE_POST_OP => 'Post-Op',
			self::STAGE_CLINICAL_PHASE_REPORT => 'Operative Report',
			self::STAGE_CLINICAL_PHASE_DISCHARGE => 'Discharge'
		],
		self::STAGE_BILLING => [
			self::STAGE_BILLING_PHASE_CODING => 'Coding'
		]
	];

	public static $stagePhases = [
		self::STAGE_INTAKE => [
			self::STAGE_INTAKE_PHASE_PATIENT_INFO,
			self::STAGE_INTAKE_PHASE_INSURANCE,
			self::STAGE_INTAKE_PHASE_ADDITIONAL
		],
		self::STAGE_CLINICAL => [
			self::STAGE_CLINICAL_PHASE_PRE_OP,
			self::STAGE_CLINICAL_PHASE_OPERATION,
			self::STAGE_CLINICAL_PHASE_POST_OP,
			self::STAGE_CLINICAL_PHASE_REPORT,
			self::STAGE_CLINICAL_PHASE_DISCHARGE
		],
		self::STAGE_BILLING => [
			self::STAGE_BILLING_PHASE_CODING
		],
	];

	const STATUS_BEFORE = 0;
	const STATUS_PRIOR = 1;
	const STATUS_DURING = 2;
	const STATUS_AFTER = 3;

	public function save()
	{
		if (!is_string($this->state)) {
			$this->state = json_encode($this->state);
		}

		parent::save();
	}

	public static function getStagePhases()
	{
		return self::$phases;
	}

	// WARNING!!! не меняйте это пока, это перейдёт в формы
	public function getValidator($patientId = null)
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('time_start')->rule('filled')->rule('date')->error('Incorrect date or time of start');
		$validator->field('time_end')->rule('filled')->rule('date')->error('Incorrect date or time of end');
		$validator->field('time_start')->rule('sequence_dates', $this->time_end)->error('Length of Case must be positive');
		$validator->field('type_id')->rule('filled')->error('You must specify procedure');
		$validator->field('location_id')->rule('filled')->error('You must specify room');
		$validator->field('description')->rule('max_length', 10000)->error('The Description must be less than or equal to 10000 characters');
		$validator->field('users')->rule('filled')->error('You must select at least one user for surgeon');

		$validator->field('time_start')->rule('callback', function ($val, $validator, $field) {
			$model = $this->pixie->orm->get('Cases_Item');
			$model->where([
				['time_start', '<', $this->time_end],
				['time_end', '>', $this->time_start],
				['location_id', $this->location_id],
				['appointment_status', '!=', self::APPOINTMENT_STATUS_CANCELED],
			]);
			if ($this->id) {
				$model->where($this->table . '.id', '!=', $this->id);
			}
			$model = $model->find();
			return !$model->loaded();
		})->error('Case to the same location at the same time exists');

		if ($patientId) {
			$patientValidation = $validator->field('users')->rule('callback', function ($validator, $field) use (&$patientValidation, $patientId) {
				$query = $this->pixie->db->query('select')
					->table($this->table)
					->fields($this->table . '.id')
					->join(['case_registration', 'cr'], [$this->table . '.id', 'cr.case_id'])
					->where('cr.patient_id', $patientId)
					->where('time_start', '<', $this->time_end)
					->where('time_end', '>', $this->time_start)
					->where('appointment_status', '!=', self::APPOINTMENT_STATUS_CANCELED);
				if ($this->id) {
					$query->where($this->table . '.id', '!=', $this->id);
				}
				$patient = $query->execute()->as_array();

				if ($patient) {
					$patientValidation->error('Case to the same patient at the same time exists');
				}
				return !$patient;
			});
		}

		$locationValidation = $validator->field('location_id')->rule('callback', function ($location_id, $validator, $field) use (&$locationValidation) {
			$model = $this->pixie->orm->get('Cases_Blocking');
			$model->query
				->join('case_blocking_item', ['case_blocking.id', 'case_blocking_item.blocking_id'])
				->where($this->pixie->db->expr('case_blocking.location_id'), $location_id)
				->where($this->pixie->db->expr('case_blocking_item.start'), '<', $this->time_end)
				->where($this->pixie->db->expr('case_blocking_item.end'), '>', $this->time_start)
				->where($this->pixie->db->expr('case_blocking_item.overwrite'), 0);

			$model_users = $validator->get('users');

			$caseUsersPracticeGroupsIds = [0];
			$case_users = [];
			foreach ($model_users as $u) {
				foreach ($u->getPracticeGroupIds() as $practiceGroupId) {
					$caseUsersPracticeGroupsIds[] = $practiceGroupId;
				}
				$case_users[] = $u->id;
			}
			array_unique($caseUsersPracticeGroupsIds);

			if (!$model_users) {
				return true;
			}

			$blockings = $model->with('doctor')
				->where('location_id', $this->location_id)
				->where('and', [
					['or', ['doctor_id', 'NOT IN', $this->pixie->db->expr('(' . implode(', ', $case_users) . ')')]],
					['or', ['practice_id', 'NOT IN', $this->pixie->db->expr('(' . implode(', ', $caseUsersPracticeGroupsIds) . ')')]]
				])
				->find_all()
				->as_array();

			if ($blockings) {
				$existeMsgs = [];
				foreach ($blockings as $item) {
					if ($item->doctor_id) {
						$existeMsgs[] = $item->doctor->getFullName();
					}
					if ($item->practice_id) {
						$existeMsgs[] = $item->practice->name;
					}
				}
				$locationValidation->error('Selected room is currently blocked for ' . implode(', ', $existeMsgs) . ' during that time. Please modify selection to proceed');
			}
			return !$blockings;
		});

		$locationInServiceValidation = $validator->field('location_id')->rule('callback', function ($location_id, $validator, $field) use (&$locationInServiceValidation) {
			$model = $this->pixie->orm->get('Cases_InService');
			$model->query
				->where($this->pixie->db->expr($model->table . '.location_id'), $location_id)
				->where($this->pixie->db->expr($model->table . '.start'), '<', $this->time_end)
				->where($this->pixie->db->expr($model->table . '.end'), '>', $this->time_start);

			$inServices = $model
				->where('location_id', $this->location_id)
				->find_all()
				->as_array();

			if ($inServices) {
				$locationInServiceValidation->error('Selected room is currently scheduled by InService during that time. Please modify selection to proceed');
			}
			return !$inServices;
		});

		return $validator;
	}

	protected function deleteInternal()
	{
		$this->pixie->db->query('delete')
			->table('case_user')
			->where('case_id', $this->id())
			->execute();

		//delete bookings
		$caseBookingRecords = $this->pixie->db->query('select')
			->table('case_booking_list')
			->where('case_id', $this->id())
			->execute();

		foreach ($caseBookingRecords as $row) {
			$booking = $this->pixie->orm->get('Booking', $row->booking_id);
			if ($booking->loaded()) {
				$booking->deleteInternal();
			}
		}

		$this->pixie->db->query('delete')
			->table('case_booking_list')
			->where('case_id', $this->id())
			->execute();

		parent::deleteInternal();
	}

	public function isSelf($user)
	{
		$usePracticeGroups = false;
		$userPracticeGroupIds = [];

		if ($user->isSatelliteOffice()) {
			$userPracticeGroupIds = $user->getPracticeGroupIds();
			$usePracticeGroups = true;
		}

		/** @var User $case_user */
		foreach ($this->users->find_all() as $case_user) {
			if ($case_user->id() == $user->id()) {
				return true;
			}

			if ($usePracticeGroups) {
				if ($case_user->organization_id == $user->organization_id) {
					$caseUserPracticeGroup = $case_user->getPracticeGroupIds();
					foreach ($userPracticeGroupIds as $id) {
						if (in_array($id, $caseUserPracticeGroup)) {
							return true;
						}
					}
				}
			}
		}

		/** @var User $case_other_staff */
		foreach ($this->other_staff->find_all() as $case_other_staff) {
			if ($case_other_staff->id() == $user->id()) {
				return true;
			}
		}

		/** @var User $other_user */
		foreach ($this->surgeon_assistant->find_all() as $other_user) {
			if ($other_user->id() == $user->id()) {
				return true;
			}
		}

		/** @var User $other_user */
		foreach ($this->co_surgeon->find_all() as $other_user) {
			if ($other_user->id() == $user->id()) {
				return true;
			}
		}

		/** @var User $other_user */
		foreach ($this->supervising_surgeon->find_all() as $other_user) {
			if ($other_user->id() == $user->id()) {
				return true;
			}
		}

		/** @var User $other_user */
		foreach ($this->first_assistant_surgeon->find_all() as $other_user) {
			if ($other_user->id() == $user->id()) {
				return true;
			}
		}

		/** @var User $other_user */
		foreach ($this->assistant->find_all() as $other_user) {
			if ($other_user->id() == $user->id()) {
				return true;
			}
		}

		/** @var User $other_user */
		foreach ($this->anesthesiologist->find_all() as $other_user) {
			if ($other_user->id() == $user->id()) {
				return true;
			}
		}

		/** @var User $other_user */
		foreach ($this->dictated_by->find_all() as $other_user) {
			if ($other_user->id() == $user->id()) {
				return true;
			}
		}

		return parent::isSelf($user);
	}

	public function getStaffCards()
	{
		$result = [];

		foreach ($this->pref_staff_cards(true) as $card) {
			$result[$card->user_id] = $card;
		}

		foreach ($this->cards_staff->find_all() as $card) {
			$result[$card->user_id] = $card;
		}

		return $result;
	}

	public function pref_staff_cards($active = true)
	{
		$model = $this->pixie->orm->get('PrefCard_Staff');
		$model->query = clone $this->query;
		if ($this->loaded()) {
			$model->query->where($this->id_field, $this->_row[$this->id_field]);
		}

		$last_alias = $model->query->last_alias();
		$through_alias = $model->query->add_alias();
		$new_alias = $model->query->add_alias();
		$model->query->join(['case_user', $through_alias], [$last_alias . '.id', $through_alias . '.case_id'], 'inner')
			->join([$model->table, $new_alias], [$through_alias . '.user_id', $new_alias . '.user_id'], 'inner');

		if ($active) {
			$model->where($through_alias . '.active', 1);
		}

		return $model->find_all()->as_array();
	}

	public function getUsedItems()
	{
		$used_items = [];
		foreach ($this->cards_staff->find_all() as $card_staff) {
			$card_staff_items = $card_staff->items->where('status', \Opake\Model\Card\Staff\Item::STATUS_MOVED)->find_all();
			foreach ($card_staff_items as $item) {
				$used_items[] = $item;
			}
		}
		return $used_items;
	}

	/**
	 * @return string
	 */
	public function getSurgeonNames()
	{
		$names = [];
		foreach ($this->getUsers() as $user) {
			$names[] = $user->getFullName();
		}

		return implode(', ', $names);
	}

	public function getProvider()
	{
		$site = $this->location->site;
		return $site->name . "\r\n" . $site->address;
	}

	public function updateStagePhase()
	{
		$continue = false;
		$registration = $this->registration;
		$user = $this->pixie->auth->user();
		if (!$registration->getValidator()->valid() && !$user->isDoctor()) {
			$this->phase = self::STAGE_INTAKE_PHASE_PATIENT_INFO;
		} elseif (!$registration->isInsuranceSectionValid() && !$user->isDoctor()) {
			$this->phase = self::STAGE_INTAKE_PHASE_INSURANCE;
		} elseif (!$registration->isFormsSectionValid() && !$user->isDoctor()) {
			$this->phase = self::STAGE_INTAKE_PHASE_ADDITIONAL;
		} else {
			$continue = true;
		}

		if ($continue) {
			$continue = false;
			$this->stage = self::STAGE_CLINICAL;
			$this->phase = null;

			$state = $this->getState();
			if ($state) {
				foreach (self::$phases[self::STAGE_CLINICAL] as $phase => $name) {
					if (isset($state[$phase]) && !$state[$phase]) {
						$this->phase = $phase;
						break;
					}
				}
				if (!$this->phase) {
					$continue = true;
				}
			} else {
				$this->phase = self::STAGE_CLINICAL_PHASE_PRE_OP;
			}
		}

		if ($continue) {
			$this->stage = self::STAGE_BILLING;
			$this->phase = self::STAGE_BILLING_PHASE_CODING;
		}

		$this->conn->query('update')->table($this->table)
			->data(['stage' => $this->stage, 'phase' => $this->phase])
			->where('id', $this->id)
			->execute();
	}

	public function start()
	{
		$this->conn->query('update')->table($this->table)
			->data(['time_start_in_fact' => strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB)])
			->where('id', $this->id)
			->execute();
	}

	public function end()
	{
		$this->conn->query('update')->table($this->table)
			->data(['time_end_in_fact' => strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB)])
			->where('id', $this->id)
			->execute();
	}

	public function updateState(array $state)
	{
		$this->state = $state;
		$this->conn->query('update')->table($this->table)
			->data(['state' => json_encode($state)])
			->where('id', $this->id)
			->execute();

		$this->updateStagePhase();
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

	public function updateBillingNotesCount()
	{
		$this->conn->query('update')->table($this->table)
			->data(['billing_notes_count' => $this->pixie->db->expr('billing_notes_count + 1')])
			->where('id', $this->id)
			->execute();
	}

	public function reduceBillingNotesCount()
	{
		$this->conn->query('update')->table($this->table)
			->data(['billing_notes_count' => $this->pixie->db->expr('billing_notes_count - 1')])
			->where('id', $this->id)
			->execute();
	}

	public function updateAnesthesiaType($anesthesiaType)
	{
		$this->conn->query('update')->table($this->table)
			->data(['anesthesia_type' => $anesthesiaType])
			->where('id', $this->id)
			->execute();
	}

	public function fromArray($data)
	{
		if (!empty($data->type)) {
			$data->type_id = $data->type->id;
		}

		if (isset($data->time_start) && $data->time_start) {
			$data->time_start = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($data->time_start));
		}
		if (isset($data->time_end) && $data->time_end) {
			$data->time_end = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($data->time_end));
		}
		if (isset($data->location) && $data->location) {
			$data->location_id = $data->location->id;
		}
		if (isset($data->users) && $data->users) {
			$users = [];
			foreach ($data->users as $user) {
				$users[] = $user->id;
			}
			$data->users = $users;
		}

		if (isset($data->additional_cpts) && $data->additional_cpts) {
			$additional_cpts = [];
			foreach ($data->additional_cpts as $additional_cpt) {
				$additional_cpts[] = $additional_cpt->id;
			}
			$data->additional_cpts = $additional_cpts;
			if (isset($additional_cpts[0]) && $additional_cpts[0]) {
				$data->type_id = $additional_cpts[0];
			}
		}

		if (isset($data->co_surgeon) && $data->co_surgeon) {
			$co_surgeon = [];
			foreach ($data->co_surgeon as $user) {
				$co_surgeon[] = $user->id;
			}
			$data->co_surgeon = $co_surgeon;
		}

		if (isset($data->supervising_surgeon) && $data->supervising_surgeon) {
			$supervising_surgeon = [];
			foreach ($data->supervising_surgeon as $user) {
				$supervising_surgeon[] = $user->id;
			}
			$data->supervising_surgeon = $supervising_surgeon;
		}

		if (isset($data->first_assistant_surgeon) && $data->first_assistant_surgeon) {
			$first_assistant_surgeon = [];
			foreach ($data->first_assistant_surgeon as $user) {
				$first_assistant_surgeon[] = $user->id;
			}
			$data->first_assistant_surgeon = $first_assistant_surgeon;
		}

		if (isset($data->assistant) && $data->assistant) {
			$assistant = [];
			foreach ($data->assistant as $user) {
				$assistant[] = $user->id;
			}
			$data->assistant = $assistant;
		}

		if (isset($data->surgeon_assistant) && $data->surgeon_assistant) {
			$surgeon_assistant = [];
			foreach ($data->surgeon_assistant as $user) {
				$surgeon_assistant[] = $user->id;
			}
			$data->surgeon_assistant = $surgeon_assistant;
		}

		if (isset($data->anesthesiologist) && $data->anesthesiologist) {
			$anesthesiologist = [];
			foreach ($data->anesthesiologist as $user) {
				$anesthesiologist[] = $user->id;
			}
			$data->anesthesiologist = $anesthesiologist;
		}

		if (isset($data->dictated_by) && $data->dictated_by) {
			$dictated_by = [];
			foreach ($data->dictated_by as $user) {
				$dictated_by[] = $user->id;
			}
			$data->dictated_by = $dictated_by;
		}

		if (isset($data->other_staff) && $data->other_staff) {
			$other_staff = [];
			foreach ($data->other_staff as $user) {
				$other_staff[] = $user->id;
			}
			$data->other_staff = $other_staff;
		}
		unset($data->notes_count);
		unset($data->billing_notes_count);

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

		if (isset($data->date_of_injury) && $data->date_of_injury) {
			$data->date_of_injury = TimeFormat::formatToDB($data->date_of_injury);
		}
		if (isset($data->unable_to_work_from) && $data->unable_to_work_from) {
			$data->unable_to_work_from = TimeFormat::formatToDB($data->unable_to_work_from);
		}
		if (isset($data->unable_to_work_to) && $data->unable_to_work_to) {
			$data->unable_to_work_to = TimeFormat::formatToDB($data->unable_to_work_to);
		}

		return $data;
	}

	public function getRunningAlert()
	{
		$result = '';
		if ($this->time_start_in_fact && !$this->time_end_in_fact) {
			$started_at = new \DateTime($this->time_start_in_fact);
			$time_start = new \DateTime($this->time_start);
			if ($started_at < $time_start->add(new \DateInterval('PT10M'))) {
				$result = 'in-progress';
			} else {
				$result = 'running-late';
			}
		}
		return $result;
	}

	/**
	 * @param bool $checkTypeByLoggedUser
	 * @param null $surgeon_id
	 * @return OperativeReport
	 * @throws \Exception
	 */
	public function getOpReport($checkTypeByLoggedUser = false, $surgeon_id = null)
	{
		if ($checkTypeByLoggedUser) {
			if($this->getLoggedUser()->isDoctor()) {
				$typeConditional = ['type', 'IN', $this->pixie->db->arr(\Opake\Model\Cases\OperativeReport::getTypeSurgeons())];
			} else {
				$typeConditional = ['type', OperativeReport::TYPE_NON_SURGEON];
			}

			if ($surgeon_id) {
				$opReport = $this->op_reports->where([
					['surgeon_id', $surgeon_id],
					$typeConditional
				])->find();

				if($opReport->loaded()) {
					return $opReport;
				}
			}
		}

		$firstSurgeonId = $this->getFirstSurgeonId();
		if (!$firstSurgeonId) {
			throw new \Exception('Case #' . $this->id() . ' has no first surgeon in associated users');
		}

		return $this->op_reports->where([
			['surgeon_id', $firstSurgeonId],
			['type', OperativeReport::TYPE_SURGEON]
		])->find();
	}

	/**
	 * @return int
	 */
	public function getMainSurgeonOpReportId()
	{
		$opReport = $this->getOpReport();
		return $opReport ? $opReport->id : null;
	}

	public function getUserColor()
	{
		$users = $this->getUsers();
		if (sizeof($users)) {
			return $users[0]->getCaseColor();
		} else {
			return User::DEFAULT_CASE_COLOR;
		}
	}

	protected function getPracticeColor()
	{
		$users = $this->getUsers();
		if (sizeof($users)) {
			$practiceGroupIds = $users[0]->getPracticeGroupIds();
			if (count($practiceGroupIds)) {
				$practice = $this->pixie->orm->get('PracticeGroup', $practiceGroupIds[0]);
				if ($practice->loaded()) {
					$caseColor = $practice->getCaseColor($this->organization_id);
					if ($caseColor) {
						return $caseColor;
					}
				}
			}
		}

		return User::DEFAULT_CASE_COLOR;
	}

	public function getPhases()
	{
		return self::$stagePhases[$this->stage];
	}

	public function getState()
	{
		if (is_string($this->state) && $this->state !== '') {
			return json_decode($this->state, true);
		} else {
			return $this->state;
		}
	}

	public function getSurgeonsArray()
	{
		$surgeonsArray = [];
		if ($this->co_surgeon->count_all()) {
			$surgeonsArray['Co-Surgeon'] = $this->co_surgeon->find_all();
		}
		if ($this->supervising_surgeon->count_all()) {
			$surgeonsArray['Supervising Surgeon'] = $this->supervising_surgeon->find_all();
		}
		if ($this->first_assistant_surgeon->count_all()) {
			$surgeonsArray['First Assistant Surgeon'] = $this->first_assistant_surgeon->find_all();
		}
		if ($this->assistant->count_all()) {
			$surgeonsArray['Assistant'] = $this->assistant->find_all();
		}
		if ($this->anesthesiologist->count_all()) {
			$surgeonsArray['Anesthesiologist'] = $this->anesthesiologist->find_all();
		}
		if ($this->dictated_by->count_all()) {
			$surgeonsArray['Dictated by'] = $this->dictated_by->find_all();
		}
		if ($this->other_staff->count_all()) {
			$surgeonsArray['Other Staff'] = $this->other_staff->find_all();
		}

		return $surgeonsArray;
	}


	public function toArray()
	{
		$data = [
			'id' => (int)$this->id,
			'type' => $this->type->toArray(),
			'provider' => $this->getProvider(),
			'patient' => [
				'id' => $this->registration->patient->id(),
				'mrn' => $this->registration->patient->getFormattedMrn(),
				'mrn_year' => $this->registration->patient->getFormattedMrnYear(),
				'full_mrn' =>  $this->registration->patient->getFullMrn(),
				'fullname' => $this->registration->getFullName(),
				'first_name' => $this->registration->patient->first_name,
				'last_name' => $this->registration->patient->last_name,
				'age' => $this->registration->patient->getAge(),
				'sex' => $this->registration->patient->getGender(),
				'dob' => $this->registration->patient->dob,
				'home_phone' => $this->registration->patient->home_phone,
				'home_phone_type' => $this->registration->patient->home_phone_type,
				'additional_phone_type' => $this->registration->patient->additional_phone_type,
				'ec_phone_type' => $this->registration->patient->ec_phone_type,
				'parents_name' => $this->registration->patient->parents_name,
				'relationship' => $this->registration->patient->relationship,
				'language_id' => $this->registration->patient->language_id,
				'language' => $this->registration->patient->language->toArray(),
				'is_patient_portal_enabled' => $this->registration->patient->isPatientPortalEnabled(),
				'can_register_on_portal' => $this->registration->patient->canRegisterOnPortal()
			],
			'location' => $this->location->toArray(),
			'description' => $this->description,
			'time_start' => date('D M d Y H:i:s O', strtotime($this->time_start)),
			'time_end' => date('D M d Y H:i:s O', strtotime($this->time_end)),
			'time_check_in' => date('D M d Y H:i:s O', strtotime($this->time_check_in)),
			'time_start_in_fact' => $this->time_start_in_fact ? date('D M d Y H:i:s O', strtotime($this->time_start_in_fact)) : null,
			'time_end_in_fact' => $this->time_end_in_fact ? date('D M d Y H:i:s O', strtotime($this->time_end_in_fact)) : null,
			'stage' => $this->stage,
			'phase' => $this->phase,
			'state' => $this->getState(),
			'status' => $this->status,
			'registration' => $this->registration->toArray(),
			'registration_id' => $this->registration->id,
			'appointment_status' => $this->appointment_status,
			'notes_count' => (int) $this->getAllNotesCount(),
			'billing_notes_count' => (int) $this->getBillingNotesCount(),
			'accompanied_by' => $this->accompanied_by,
			'accompanied_phone' => $this->accompanied_phone,
			'accompanied_email' => $this->accompanied_email,
			'is_staff_checked' => $this->getStaffTemplateValue(),
			'studies_other' => $this->studies_other,
			'anesthesia_type' => $this->anesthesia_type,
			'anesthesia_other' => $this->anesthesia_other,
			'special_equipment_required' => $this->special_equipment_required,
			'special_equipment_implants' => $this->special_equipment_implants,
			'special_equipment_flag' => $this->special_equipment_flag,
			'implants' => $this->implants,
			'implants_flag' => $this->implants_flag,
			'verification_status' => $this->registration->verification_status,
			'verification_completed_date' => $this->registration->verification_completed_date ? date('D M d Y H:i:s O', strtotime($this->registration->verification_completed_date)) : null,
			'locate' => $this->locate,
			'transportation' => $this->transportation,
			'transportation_notes' => $this->transportation_notes,
			'point_of_origin' => $this->point_of_origin,
			'referring_provider_name' => $this->referring_provider_name,
			'referring_provider_npi' => $this->referring_provider_npi,
			'date_of_injury' => $this->date_of_injury,
			'is_unable_to_work' => (bool) $this->is_unable_to_work,
			'unable_to_work_from' => $this->unable_to_work_from,
			'unable_to_work_to' => $this->unable_to_work_to,
			'has_flagged_comments' => $this->hasFlaggedComments(),
			'has_billing_flagged_comments' => $this->hasFlaggedBillingComments(),
			'charts_count' => (int) $this->getCharts()->count_all()
		];

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

		$data['op_report_id'] = $this->getMainSurgeonOpReportId();

		$secondary_diagnosis = [];
		foreach ($this->registration->secondary_diagnosis->find_all() as $diagnosis) {
			$secondary_diagnosis[] = $diagnosis->toArray();
		}
		$data['registration']['secondary_diagnosis'] = $secondary_diagnosis;

		$admitting_diagnosis = [];
		foreach ($this->registration->admitting_diagnosis->find_all() as $diagnosis) {
			$admitting_diagnosis[] = $diagnosis->toArray();
		}
		$data['registration']['admitting_diagnosis'] = $admitting_diagnosis;

		$users = [];
		foreach ($this->getUsers() as $user) {
			$users[] = $user->toArray();
		}
		$data['users'] = $users;

		$additional_cpts = [];
		foreach ($this->getAdditionalCpts() as $additional_cpt) {
			$additional_cpts[] = $additional_cpt->toArray();
		}
		$data['additional_cpts'] = $additional_cpts;

		$surgeon_assistant = [];
		foreach ($this->surgeon_assistant->find_all() as $user) {
			$surgeon_assistant[] = $user->toArray();
		}
		$data['surgeon_assistant'] = $surgeon_assistant;

		$co_surgeon = [];
		foreach ($this->co_surgeon->find_all() as $user) {
			$co_surgeon[] = $user->toArray();
		}
		$data['co_surgeon'] = $co_surgeon;

		$supervising_surgeon = [];
		foreach ($this->supervising_surgeon->find_all() as $user) {
			$supervising_surgeon[] = $user->toArray();
		}
		$data['supervising_surgeon'] = $supervising_surgeon;

		$first_assistant_surgeon = [];
		foreach ($this->first_assistant_surgeon->find_all() as $user) {
			$first_assistant_surgeon[] = $user->toArray();
		}
		$data['first_assistant_surgeon'] = $first_assistant_surgeon;

		$assistant = [];
		foreach ($this->assistant->find_all() as $user) {
			$assistant[] = $user->toArray();
		}
		$data['assistant'] = $assistant;

		$anesthesiologist = [];
		foreach ($this->anesthesiologist->find_all() as $user) {
			$anesthesiologist[] = $user->toArray();
		}
		$data['anesthesiologist'] = $anesthesiologist;

		$dictated_by = [];
		foreach ($this->dictated_by->find_all() as $user) {
			$dictated_by[] = $user->toArray();
		}
		$data['dictated_by'] = $dictated_by;

		$other_staff = [];
		foreach ($this->other_staff->find_all() as $user) {
			$other_staff[] = $user->toArray();
		}
		$data['other_staff'] = $other_staff;

//		$inventory_items = [];
//		foreach ($this->inventory_items->find_all() as $item) {
//			$inventory_items[] = $item->toArray();
//		}
//		$data['inventory_items'] = $inventory_items;

//		$equipments = [];
//		foreach ($this->equipments->find_all() as $equipment) {
//			$equipments[] = $equipment->toShortArray();
//		}
//		$data['equipments'] = $equipments;
//
//		$implant_items = [];
//		foreach ($this->implant_items->find_all() as $implant) {
//			$implant_items[] = $implant->toShortArray();
//		}
//		$data['implant_items'] = $implant_items;

		if ($this->drivers_license->loaded()) {
			$data['drivers_license'] = $this->drivers_license->toArray();
		}

		if ($this->insurance_card->loaded()) {
			$data['insurance_card'] = $this->insurance_card->toArray();
		}

		if ($user = $this->pixie->auth->user()) {
			$data['is_self_for_user'] = $this->isSelf($user);
		}

		$alerts = [];
		foreach ($this->getAlerts() as $alert) {
			$alerts[] = $alert->toArray();
		}

		$data['alerts'] = $alerts;



		return $data;
	}

	public function getNotes()
	{
		return $this->notes->find_all()->as_array();
	}

	public function getBillingNotes()
	{
		return $this->billing_notes->order_by('id', 'desc')->find_all()->as_array();
	}

	public function getAlerts()
	{
		$alerts = [];
		$user =  $this->getLoggedUser();
		if(!$user->isInternal() && !$user->isFullAdmin()) {
			return $alerts;
		}
		$siteAlert = $this->pixie->orm->get('Site_Alert')->where('site_id', $this->location->site_id)->find();
		if($siteAlert->loaded() && $siteAlert->enable_for_site) {
			foreach ($this->alerts->find_all() as $alert) {
				if(!empty($siteAlert->{$alert->code})) {
					$alerts[] = $alert;
				}
			}
		}
		return $alerts;
	}

	public function isSentSms()
	{
		return $this->pixie->orm->get('Cases_SmsLog')->with('log')->where([
			['case_id', $this->id],
			['type', \Opake\Model\Cases\SmsLog::TYPE_POINT_OF_CONTACT],
			['log.status', \Opake\Model\SmsLog::STATUS_SENT],
		])->find()->loaded();
	}

	public function toShortArray()
	{
		$registration = $this->registration;
		$data = [
			'id' => (int)$this->id,
			'location' => $this->location->toArray(),
			'type' => $this->type->toArray(),
			'time_start' => date('D M d Y H:i:s O', strtotime($this->time_start)),
			'time_end' => date('D M d Y H:i:s O', strtotime($this->time_end)),
			'time_check_in' => date('D M d Y H:i:s O', strtotime($this->time_check_in)),
			'phase' => $this->phase,
			'description' => $this->description,
			'registration_id' => $registration->id,
			'patient' => [
				'id' => (int) $registration->patient_id,
				'full_name' => $registration->last_name . ', ' . $registration->first_name,
				'dob' => $registration->dob,
				'home_phone' => $registration->home_phone,
				'full_mrn' => $registration->patient->getFullMrn(),
				'sex_letter' => $registration->patient->getSexLetter()
			],
			'point_of_contact_phone' => $registration->point_of_contact_phone,
			'point_of_contact_phone_type' => $registration->point_of_contact_phone_type,
			'is_sent_sms' => $this->isSentSms(),
			'appointment_status' => $this->appointment_status,
			'notes_count' => (int) $this->getAllNotesCount(),
			'accompanied_by' => $this->accompanied_by,
			'accompanied_phone' => $this->accompanied_phone,
			'accompanied_email' => $this->accompanied_email,
			'first_surgeon_for_dashboard' => $this->getFirstSurgeonForDashboard(),
			'procedure_name_for_dashboard' => $this->getProcedureNameForDashboard(),
			'verification_status' => $registration->verification_status,
			'verification_completed_date' => $registration->verification_completed_date ? date('D M d Y H:i:s O', strtotime($registration->verification_completed_date)) : null,
			'has_flagged_comments' => $this->hasFlaggedComments(),
			'has_billing_flagged_comments' => $this->hasFlaggedBillingComments(),
			'transport' => $this->transportation_notes
		];

		if ($this->drivers_license->loaded()) {
			$data['drivers_license'] = $this->drivers_license->toArray();
		}

		if ($this->insurance_card->loaded()) {
			$data['insurance_card'] = $this->insurance_card->toArray();
		}

		return $data;
	}

	public function toCancellationArray()
	{
		$data = [
			'id' => (int) $this->id,
			'patient' => [
				'id' => $this->registration->patient->id(),
				'mrn' => $this->registration->patient->getFormattedMrn(),
				'mrn_year' => $this->registration->patient->getFormattedMrnYear(),
				'full_mrn' =>  $this->registration->patient->getFullMrn(),
				'fullname' => $this->registration->getFullName(),
				'first_name' => $this->registration->patient->first_name,
				'last_name' => $this->registration->patient->last_name,
			],
			'registration_id' => $this->registration->id,
			'first_surgeon_for_dashboard' => $this->getFirstSurgeonForDashboard(),
			'first_surgeon_practice_name' => $this->getFirstSurgeon()->getFirstPracticeGroupName(),
		];

		return $data;
	}

	public function toChartsArray()
	{
		$data = [
			'id' => (int)$this->id,
			'type_name' => $this->type->name,
			'first_surgeon_name' => $this->getFirstSurgeonForDashboard(),
			'time_start' => date('D M d Y H:i:s O', strtotime($this->time_start)),
			'appointment_status' => $this->appointment_status,
			'report' => $this->getOpReport()->getFormatter('PatientCharts')->toArray()
		];

		$data['charts'] = $this->getChartsArray();

		return $data;
	}

	public function toCalendarArray($colorType)
	{
		$staff = [];
		foreach ($this->getUsers() as $user) {
			$staff[] = $user->getFullName();
		}

		if ($colorType === 'room') {
			$color = $this->location->getCaseColor();
		} else if ($colorType === 'practice') {
			$color = $this->getPracticeColor();
		}else {
			$color = $this->getUserColor();
		}

		$reg = $this->registration;

		$alerts = [];
		foreach ($this->getAlerts() as $alert) {
			$alerts[] = $alert->toArray();
		}

		$data = [
			'id' => (int)$this->id,
			'title' => $reg->getFullNameForCalendarCell(),
			'start' => date('D M d Y H:i:s O', strtotime($this->time_start)),
			'end' => date('D M d Y H:i:s O', strtotime($this->time_end)),
			'allDay' => false,
			'className' => [
				'color-' . $color
			],

			'type' => 'case',
			'case_type' => $this->type->name,
			'description' => $this->description,
			'location_id' => (int)$this->location_id,
			'location' => $this->location->name,
			'staff' => $staff,
			'first_staff_id' => $this->getFirstSurgeonId(),
			'patient' => [
				'full_mrn' => $reg->patient->getFullMrn(),
				'fullname' => $reg->getFullName(),
				'age' => $reg->patient->getAge(),
				'dob' => TimeFormat::getDate($reg->patient->dob)
			],
			'running_alert_class' => $this->getRunningAlert(),
			'appointment_status' => $this->appointment_status,
			'alerts' => $alerts,
		];

		if ($this->pixie->permissions->checkAccess('cases', 'edit_by_calendar')) {
			$data['startEditable'] = true;
		}

		return $data;
	}

	public function toOpReportArray()
	{
		$data = $this->toArray();

		$patient = $this->registration->patient;

		$data['patient']['home_address'] = $patient->home_address;
		$data['patient']['home_apt_number'] = $patient->home_apt_number;
		$data['patient']['home_city_name'] = (($patient->custom_home_city) ? : (($patient->home_city->loaded()) ? $patient->home_city->name : ''));
		$data['patient']['home_state_name'] = (($patient->custom_home_state) ? : (($patient->home_state->loaded()) ? $patient->home_state->name : ''));
		$data['patient']['home_country_name'] = ($patient->home_country->loaded()) ? $patient->home_country->name : '';
		$data['patient']['home_zip_code'] = $patient->home_zip_code;

		$data['registration']['primary_insurance_title'] = $this->registration->getPrimaryInsuranceTitle();

		$site = $this->location->site;

		$data['location']['site_address'] = $site->address;
		$data['location']['site_country_name'] = ($site->country && $site->country->loaded()) ? $site->country->name : null;
		$data['location']['site_state_name'] = ($site->custom_state !== null) ? $site->custom_state :
			($site->state && $site->state->loaded()) ? $site->state->name : null;
		$data['location']['site_city_name'] = ($site->custom_city !== null) ? $site->custom_city :
			($site->city && $site->city->loaded()) ? $site->city->name : null;
		$data['location']['site_zip'] = $site->zip_code;
		$data['location']['site_phone'] = $site->contact_phone;

		return $data;
	}

	public function getFirstSurgeonForDashboard()
	{
		$users = $this->getUsers();
		if (!empty($users)) {
			$result = $users[0]->getFullName();
			if (sizeof($users) > 1) {
				$result .= ' ...';
			}
			return $result;
		}
		return '';
	}

	public function getProcedureNameForDashboard()
	{
		if (strlen($this->type->name) <= 100) {
			return $this->type->name;
		}

		return (substr($this->type->name, 0, 100) . ' ...');
	}

	public function getProcedureNameForDashboardPrint()
	{
		if (strlen($this->type->name) <= 50) {
			return $this->type->name;
		}

		return (substr($this->type->name, 0, 50) . ' ...');
	}

	/**
	 * @return string
	 */
	public function getFirstSurgeonForLabel()
	{
		$firstSurgeon = $this->getFirstSurgeon();
		if ($firstSurgeon) {
			return substr($firstSurgeon->last_name . ', ' . $firstSurgeon->first_name, 0, 23);
		}

		return '';
	}

	/**
	 * @return int|null
	 */
	public function getFirstSurgeonId()
	{
		$firstSurgeon = $this->getFirstSurgeon();
		if ($firstSurgeon) {
			return $firstSurgeon->id();
		}

		return null;
	}

	/**
	 * @return \Opake\Model\User|null
	 */
	public function getFirstSurgeon()
	{
		return $this->getFirstUser();
	}

	public static function getAppointmentStatusList()
	{
		return [
			self::APPOINTMENT_STATUS_NEW => 'New',
			self::APPOINTMENT_STATUS_CANCELED => 'Canceled',
			self::APPOINTMENT_STATUS_COMPLETED => 'Completed'
		];
	}

	public static function getAnesthesiaTypeList()
	{
		return [
			self::ANESTHESIA_TYPE_GEN => 'Gen',
			self::ANESTHESIA_TYPE_MAC => 'Mac',
			self::ANESTHESIA_TYPE_SED => 'IV Sed',
			self::ANESTHESIA_TYPE_LOCAL => 'Local',
			self::ANESTHESIA_TYPE_BLOCK => 'Block',
			self::ANESTHESIA_TYPE_OTHER => 'Other',
			self::ANESTHESIA_TYPE_NOT_SPECIFIED => 'Not Specified',
		];
	}

	public static function getPointOfOriginList()
	{
		return [
			self::POINT_OF_ORIGIN_NON_HEALTH => 'Non-Health Care Facility Point of Origin',
			self::POINT_OF_ORIGIN_NON_CLINIC => 'Clinic or Physician Referral',
			self::POINT_OF_ORIGIN_NON_HOSPITAL => 'Transfer from Hospital',
			self::POINT_OF_ORIGIN_NON_SNF => 'Transfer from SNF',
			self::POINT_OF_ORIGIN_NON_FACILITY => 'Transfer from another health care facility',
			self::POINT_OF_ORIGIN_NON_EMERGENCY => 'Emergency Room',
			self::POINT_OF_ORIGIN_NON_COURT => 'Court/Law Enforcement',
			self::POINT_OF_ORIGIN_NON_INFO_NOT_AVAIL=> 'Information not available',
			self::POINT_OF_ORIGIN_NON_TRANSFER_SAME_HOSPITAL => 'Transfer from one unit to another in same hospital',
			self::POINT_OF_ORIGIN_NON_AMBULATORY => 'Transfer from Ambulatory Surgical Center',
			self::POINT_OF_ORIGIN_NON_HOSPICE_FACILITY => 'Transfer from Hospice Facility'
		];
	}

	public function getCard()
	{
		return $this->card_staff;
	}

	public function addDriversLicense($file)
	{
		if ($this->drivers_license->loaded()) {
			$this->drivers_license->delete();
		}

		$driversLicenseModel = $this->pixie->orm->get('Cases_DriversLicense');
		$driversLicenseModel->case_id = $this->id;
		$driversLicenseModel->uploaded_file_id = $file->id;
		$driversLicenseModel->uploaded_date = strftime(TimeFormat::DATE_FORMAT_DB, time());
		$driversLicenseModel->save();

		return $driversLicenseModel;
	}

	public function addInsuranceCard($file)
	{
		if ($this->insurance_card->loaded()) {
			$this->insurance_card->delete();
		}

		$insuranceCardModel = $this->pixie->orm->get('Cases_InsuranceCard');
		$insuranceCardModel->case_id = $this->id;
		$insuranceCardModel->uploaded_file_id = $file->id;
		$insuranceCardModel->uploaded_date = strftime(TimeFormat::DATE_FORMAT_DB, time());
		$insuranceCardModel->save();

		return $insuranceCardModel;
	}

	public function hasUnreadNotesForUser($userId)
	{
		$query = $this->pixie->db->query('select')
			->table('user_case_note')
			->fields('last_read_note_id')
			->where([['user_id', $userId], ['case_id', $this->id]])
			->execute()
			->current();

		if (!$query) {
			$this->pixie->db->query('insert')
				->table('user_case_note')
				->data(['user_id' => $userId, 'case_id' => $this->id])
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

	public function hasUnreadBillingNotesForUser($userId)
	{
		$query = $this->pixie->db->query('select')
			->table('user_billing_note')
			->fields('last_read_note_id')
			->where([['user_id', $userId], ['case_id', $this->id]])
			->execute()
			->current();

		if (!$query) {
			$this->pixie->db->query('insert')
				->table('user_billing_note')
				->data(['user_id' => $userId, 'case_id' => $this->id])
				->execute();

			return true;
		} else {
			$lastReadNoteId = $query->last_read_note_id;
			$caseNotes = $this->billing_notes->find_all()->as_array();

			if (count($caseNotes) && ($lastReadNoteId < $caseNotes[count($caseNotes) - 1]->id)) {
				return true;
			} else {
				return false;
			}
		}
	}

	public function getCaseBookingListId()
	{
		$query = $this->pixie->db->query('select')
			->table('case_booking_list')
			->fields('id')
			->where(['case_id', $this->id])
			->execute()
			->current();

		if ($query) {
			return $query->id;
		} else {
			$this->pixie->db->query('insert')
				->table('case_booking_list')
				->data(['case_id' => $this->id])
				->execute();

			return $this->pixie->db->insert_id();
		}
	}

	public function getLastCancellation()
	{
		$query = $this->pixie->db->query('select')
			->table('case_cancellation')
			->fields('id')
			->where(['case_id', $this->id], ['rescheduled_date', 'IS', $this->pixie->db->expr('NULL')])
			->execute()
			->current();

		if ($query) {
			return $this->pixie->orm->get('Cases_Cancellation', $query->id);
		} else {
			return false;
		}
	}

	public function getChartsArray()
	{
		$charts = $this->pixie->orm->get('Cases_Chart');
		$chartsQuery = $charts->query;
		$chartsQuery->fields('case_chart.*');

		$chartsQuery->join('case_booking_list', ['case_booking_list.id', 'case_chart.list_id'], 'inner')
			->where('case_booking_list.case_id', $this->id);

		$chartsArray = [];
		foreach ($charts->find_all() as $chart) {
			$chartsArray[] = $chart->toArray();
		}
		
		return $chartsArray;
	}

	public function getFinancialDocumentsArray()
	{
		$docs = $this->pixie->orm->get('Cases_FinancialDocument');
		$docsQuery = $docs->query;
		$docsQuery->fields('case_financial_document.*');

		$docsQuery->join('case_booking_list', ['case_booking_list.id', 'case_financial_document.list_id'], 'inner')
			->where('case_booking_list.case_id', $this->id);

		$docsArray = [];
		foreach ($docs->find_all() as $doc) {
			$docsArray[] = $doc->toArray();
		}

		return $docsArray;
	}

	public function getCharts()
	{
		$charts = $this->pixie->orm->get('Cases_Chart');
		$chartsQuery = $charts->query;
		$chartsQuery->fields('case_chart.*');

		$chartsQuery->join('case_booking_list', ['case_booking_list.id', 'case_chart.list_id'], 'inner')
			->where('case_booking_list.case_id', $this->id);

		$charts->where('is_booking_sheet', 0);

		return $charts;
	}

	public function getFinancialDocuments()
	{
		$docs = $this->pixie->orm->get('Cases_FinancialDocument');
		$chartsQuery = $docs->query;
		$chartsQuery->fields('case_financial_document.*');

		$chartsQuery->join('case_booking_list', ['case_booking_list.id', 'case_financial_document.list_id'], 'inner')
			->where('case_booking_list.case_id', $this->id);

		$docs->where('is_booking_sheet', 0);

		return $docs;
	}

	public function getBookingSheetSnapshot()
	{
		$chart = $this->pixie->orm->get('Cases_Chart');
		$chartsQuery = $chart->query;
		$chartsQuery->fields('case_chart.*');

		$chartsQuery->join('case_booking_list', ['case_booking_list.id', 'case_chart.list_id'], 'inner')
			->where('case_booking_list.case_id', $this->id);

		$chart->where('is_booking_sheet', 1);

		return $chart->find();
	}

	public function readNotes($userId)
	{
		$caseNote = $this->notes->order_by('id', 'DESC')->limit(1)->find();
		$lastCaseNoteId = $caseNote->id;

		$this->pixie->db->query('update')
			->table('user_case_note')
			->data(['last_read_note_id' => $lastCaseNoteId])
			->where([['user_id', $userId], ['case_id', $this->id]])
			->execute();
	}

	public function readBillingNotes($userId)
	{
		$caseNote = $this->billing_notes->order_by('id', 'DESC')->limit(1)->find();
		$lastCaseNoteId = $caseNote->id;

		$this->pixie->db->query('update')
			->table('user_billing_note')
			->data(['last_read_note_id' => $lastCaseNoteId])
			->where([['user_id', $userId], ['case_id', $this->id]])
			->execute();
	}

	protected function getStaffTemplateValue()
	{
		$isCheckedStaff = false;
		$service = $this->pixie->services->get('Cases_operativeReports');
		$template = $service->getTemplate($this->organization_id);
		if (isset($template['staff'])) {
			$isCheckedStaff = $template['staff'];
		}
		return $isCheckedStaff;
	}

	public function updateAdditionalCpts(array $data)
	{
		$this->pixie->db->query('delete')->table('case_additional_type')->where('case_id', $this->id)->execute();
		foreach ($data as $num => $caseType) {
			$this->conn->query('insert')->table('case_additional_type')
				->data([
					'type_id' => $caseType->id,
					'case_id' => $this->id,
					'order' => $num,
				])
				->execute();
		}
	}

	public function getAdditionalCpts()
	{
		$model = $this->pixie->orm->get('Cases_Type');
		$model->query->join('case_additional_type', [$model->table . '.id', 'case_additional_type.type_id'], 'inner');
		if ($this->loaded()) {
			$model->query->where('case_additional_type.case_id', $this->id);
		}
		$model->query->fields($model->table . '.*');
		return $model->order_by('case_additional_type.order')->find_all()->as_array();
	}

	public function updateUsers($data)
	{
		$this->pixie->db->query('delete')
			->table('case_user')->where('case_id', $this->id)->execute();

		foreach ($data as $num => $user) {
			$this->conn->query('insert')->table('case_user')
				->data([
					'user_id' => $user->id,
					'case_id' => $this->id,
					'order' => $num,
				])
				->execute();
		}
	}

	/**
	 * @return \Opake\Model\User[]
	 */
	public function getUsers()
	{
		if (!$this->loaded()) {
			return [];
		}

		$model = $this->pixie->orm->get('User');
		$model->query->join('case_user', ['user.id', 'case_user.user_id'], 'inner');
		$model->query->where('case_user.case_id', $this->id());
		$model->query->fields('user.*');
		return $model->order_by('case_user.order')->find_all()->as_array();
	}

	/**
	 * @return \Opake\Model\User|null
	 */
	public function getFirstUser()
	{
		if (!$this->loaded()) {
			return null;
		}

		$model = $this->pixie->orm->get('User');
		$model->query->join('case_user', ['user.id', 'case_user.user_id'], 'inner');
		$model->query->where('case_user.case_id', $this->id());
		$model->query->fields('user.*');
		$model->query->order_by('case_user.order');
		$model->query->limit(1);

		$userModel = $model->find();
		if ($userModel->loaded()) {
			return $userModel;
		}

		return null;
	}


	public function updateAssistantUsers($data)
	{
		$this->pixie->db->query('delete')
			->table('case_assistant')->where('case_id', $this->id)->execute();

		foreach ($data as $num => $user) {
			$this->conn->query('insert')->table('case_assistant')
				->data([
					'assistant_id' => $user->id,
					'case_id' => $this->id,
					'order' => $num,
				])
				->execute();
		}
	}

	public function getAssistantUsers()
	{
		if (!$this->loaded()) {
			return [];
		}

		$model = $this->pixie->orm->get('User');
		$model->query->join('case_assistant', ['user.id', 'case_assistant.assistant_id'], 'inner');
		$model->query->where('case_assistant.case_id', $this->id());
		$model->query->fields('user.*');
		return $model->order_by('case_assistant.order')->find_all()->as_array();
	}

	public function updateOtherStaffUsers($data)
	{
		$this->pixie->db->query('delete')
			->table('case_other_staff')
			->where('case_id', $this->id)
			->execute();

		foreach ($data as $num => $user) {
			$this->conn->query('insert')
				->table('case_other_staff')
				->data([
					'staff_id' => $user->id,
					'case_id' => $this->id,
					'order' => $num,
				])
				->execute();
		}
	}

	public function getOtherStaffUsers()
	{
		if (!$this->loaded()) {
			return [];
		}

		$model = $this->pixie->orm->get('User');
		$model->query->join('case_other_staff', ['user.id', 'case_other_staff.staff_id'], 'inner');
		$model->query->where('case_other_staff.case_id', $this->id());
		$model->query->fields('user.*');
		return $model->order_by('case_other_staff.order')->find_all()->as_array();
	}

	protected function getLoggedUser()
	{
		return $this->pixie->auth->user();
	}

	public function updatePreOpRequiredData(array $data)
	{
		$this->pixie->db->query('delete')->table('case_pre_op_required_data')->where('case_id', $this->id)->execute();
		foreach ($data as $num => $preOp) {
			$this->conn->query('insert')->table('case_pre_op_required_data')
				->data([
					'pre_op_required' => $preOp,
					'case_id' => $this->id,
				    'order' => $num
				])
				->execute();
		}
	}

	public function getPreOpRequiredData()
	{
		$ids = [];
		$rows = $this->pixie->db->query('select')
			->table('case_pre_op_required_data')
			->fields('pre_op_required')
			->where('case_id', $this->id())
			->order_by('order')
			->execute();

		foreach ($rows as $row) {
			$ids[] = $row->pre_op_required;
		}

		return $ids;
	}

	public function updateStudiesOrdered(array $data)
	{
		$this->pixie->db->query('delete')->table('case_studies_ordered')
			->where('case_id', $this->id)->execute();
		foreach ($data as $num => $studies_order) {
			$this->conn->query('insert')->table('case_studies_ordered')
				->data([
					'studies_order' => $studies_order,
					'case_id' => $this->id,
				    'order' => $num
				])
				->execute();
		}
	}

	public function getStudiesOrdered()
	{
		$ids = [];
		$rows = $this->pixie->db->query('select')
			->table('case_studies_ordered')
			->fields('studies_order')
			->where('case_id', $this->id())
			->order_by('order')
			->execute();

		foreach ($rows as $row) {
			$ids[] = $row->studies_order;
		}

		return $ids;
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

	public static function getManualBillingStatusesList()
	{
		return [
			0 => '',
			8 => 'TBP',
			9 => 'APP',
			10 => 'ARB',
			11 => 'NOONB',
			12 => 'BEX',
			13 => 'COLL',
			14 => 'CRB',
			15 => 'CD',
			16 => 'CP',
			17 => 'CLX',
			18 => 'SECINS'
		];
	}

	public static function getManualBillingStatusesListDesc()
	{
		return [
			0 => '',
			8 => 'TRANSFERRED BALANCE TO PATIENT',
			9 => 'APPEAL',
			10 => 'ARBITRATION',
			11 => 'NO OUT OF NETWORK BENEFITS',
			12 => 'BENEFITS EXHUASTED',
			13 => 'COLLECTION YES',
			14 => 'CLAIM RESUBMISSION',
			15 => 'CLAIM DENIED',
			16 => 'CLAIM IS PROCESSING',
			17 => 'CLAIM CLOSED',
			18 => 'SUBMITTED TO SECONDARY INSURANCE'
		];
	}

	protected function hasFlaggedComments()
	{
		$caseNotes = $this->pixie->orm->get('Cases_Note')->where('patient_id', $this->registration->patient_id);

		if ($caseNotes->count_all()) {
			return true;
		}

		return false;
	}

	public function hasFlaggedBillingComments()
	{
		if($this->registration->patient_id) {
			$caseNotes = $this->pixie->orm->get('Billing_Note')->where('patient_id', $this->registration->patient_id);

			if ($caseNotes->count_all()) {
				return true;
			}
		}

		return false;
	}

	public function getAllNotesCount()
	{
		return $this->pixie->orm->get('Cases_Note')->where('and', [
			['or', ['patient_id', $this->registration->patient_id]],
			['or', ['case_id', $this->id]]
		])->count_all();
	}

	public function getBillingNotesCount()
	{
		if(empty($this->registration->patient_id)) {
			return 0;

		}
		return $this->pixie->orm->get('Billing_Note')->where('and', [
			['or', ['patient_id', $this->registration->patient_id]],
			['or', ['case_id', $this->id]]
		])->count_all();
	}
}

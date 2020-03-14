<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;

class Cancellation extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'case_cancellation';
	protected $_row = [
		'id' => null,
		'case_id' => null,
		'dos' => null,
		'cancel_time' => null,
		'cancel_status' => null,
		'cancel_reason' => '',
		'canceled_user_id' => null,
		'rescheduled_date' => null,
	];
	protected $belongs_to = [
		'case' => [
			'model' => 'Cases_Item',
			'key' => 'case_id'
		],
		'canceled_user' => [
			'model' => 'User',
			'key' => 'canceled_user_id'
		],
	];
	protected $has_many = [
		'cancel_attempts' => [
			'model' => 'Cases_CancelAttempt',
			'key' => 'case_cancellation_id',
			'cascade_delete' => true
		]
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	const CANCELLED_STATUS_PATIENT_RESPONSIBILITY = 0;
	const CANCELLED_STATUS_PHYSICIAN_RESPONSIBILITY = 1;
	const CANCELLED_STATUS_BEFORE_ANESTHESIA = 2;
	const CANCELLED_STATUS_AFTER_ANESTHESIA = 3;
	const CANCELLED_STATUS_NO_SHOW = 4;
	const CANCELLED_STATUS_OTHER = 5;

	protected static $cancellation_statuses = [
		self::CANCELLED_STATUS_PATIENT_RESPONSIBILITY => 'Patient Responsibility',
		self::CANCELLED_STATUS_PHYSICIAN_RESPONSIBILITY => 'Physician Responsibility',
		self::CANCELLED_STATUS_BEFORE_ANESTHESIA => 'Before Anesthesia',
		self::CANCELLED_STATUS_AFTER_ANESTHESIA => 'After Anesthesia',
		self::CANCELLED_STATUS_NO_SHOW => 'No Show',
		self::CANCELLED_STATUS_OTHER => 'Other'
	];

	public function toArray()
	{
		$cancel_attempts = [];
		foreach ($this->cancel_attempts->find_all() as $cancel_attempt) {
			$cancel_attempts[] = $cancel_attempt->toArray();
		}

		return [
			'id' => (int) $this->id,
			'case_id' => (int) $this->case_id,
			'dos' => $this->dos ? date('D M d Y H:i:s O', strtotime($this->dos)) : null,
			'cancel_time' => $this->cancel_time ? date('D M d Y H:i:s O', strtotime($this->cancel_time)) : null,
			'cancel_status' => $this->cancel_status,
			'cancel_reason' => $this->cancel_reason,
			'canceled_user_id' => $this->canceled_user_id,
			'rescheduled_date' => $this->rescheduled_date ? date('D M d Y H:i:s O', strtotime($this->rescheduled_date)) : null,
			'canceled_user' => [
				'id' => $this->canceled_user->id,
				'full_name' => $this->canceled_user->getFullName(),
			],
			'cancel_reason_for_table' => $this->getCancelReasonForTable(),
			'case' => $this->case->toCancellationArray(),
			'cancel_attempts' => $cancel_attempts,
			'patient_full_name' => $this->getCasePatientFullName(),
			'patient_full_mrn' => $this->case->registration->patient->getFullMrn(),
			'case_time_start' => $this->case->time_start ? date('D M d Y H:i:s O', strtotime($this->case->time_start)) : null,
			'case_time_end' => $this->case->time_end ? date('D M d Y H:i:s O', strtotime($this->case->time_end)) : null,
			'case_surgeon' => $this->case->getFirstSurgeonForDashboard(),
			'is_remained_in_billing' => (bool)$this->case->is_remained_in_billing
		];
	}

	public function getCancelReasonForTable()
	{
		if (strlen($this->cancel_reason) <= 100) {
			return $this->cancel_reason;
		}

		return (substr($this->cancel_reason, 0, 100) . ' ...');
	}

	public function getCancellationStatus()
	{
		if (isset(self::$cancellation_statuses[$this->cancel_status])) {
			return self::$cancellation_statuses[$this->cancel_status];
		}
		return '';
	}

	private function getCasePatientFullName()
	{
		return $this->case->registration->patient->last_name . ', ' . $this->case->registration->patient->first_name;
	}
}

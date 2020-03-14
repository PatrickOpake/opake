<?php

namespace Opake\Model\Patient\Appointment;

use Opake\Model\AbstractModel;

class AlertHidden extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'patient_user_appointment_alert_hidden';
	protected $_row = [
		'id' => null,
		'patient_user_id' => null,
		'case_registration_id' => null,
		'is_info_alert_hidden' => 0,
		'is_insurance_alert_hidden' => 0,
		'is_forms_alert_hidden' => 0,
	];

	protected $belongs_to = [
		'patient_user' => [
			'model' => 'Patient_User',
			'key' => 'patient_user_id',
		],
		'case_registration' => [
			'model' => 'Cases_Registration',
			'key' => 'case_registration_id',
		]
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];
}
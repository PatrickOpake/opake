<?php

namespace Opake\Model\Site;

use Opake\Model\AbstractModel;

class Alert extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'site_alert_settings';
	protected $_row = [
		'id' => null,
		'site_id' => null,
		'enable_for_site' => null,
		'cases_report_completed_48hrs_case_end' => null,
		'not_insurance_verified' => null,
		'not_completed_preauthorized' => null,
		'has_pre_certification_required' => null,
		'has_not_been_pre_certified' => null,
		'is_self_funded' => null,
		'has_oon_benefits' => null,
		'has_asc_benefits' => null,
		'has_clauses_under_medicare_entitlement' => null,
		'has_clauses_under_patient_policy' => null,
		'registration_not_completed' => null,
	];

	protected $belongs_to = [
		'site' => [
			'model' => 'Site',
			'key' => 'site_id'
		],
	];

}

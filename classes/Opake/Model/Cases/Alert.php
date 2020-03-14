<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;

class Alert extends AbstractModel
{



	public $id_field = 'id';
	public $table = 'case_alert';
	protected $_row = [
		'id' => null,
		'case_id' => null,
		'code' => null,
		'type' => null,
	];

	protected $belongs_to = [
		'case' => [
			'model' => 'Cases_Item',
			'key' => 'case_id'
		]
	];

	public static $alerts = [
		'registration_not_completed',
		'not_insurance_verified',
		/*
		'not_completed_preauthorized',
		'has_not_been_pre_certified',
		'is_self_funded',
		'has_oon_benefits',
		'has_asc_benefits',
		'has_clauses_under_medicare_entitlement',
		'has_clauses_under_patient_policy'*/
	];

	public static $alertTypes = [
		'cases_report_completed_48hrs_case_end' => 'cases',
		'registration_not_completed' => 'registration',
		'not_insurance_verified' => 'schedule',
		'not_completed_preauthorized' => 'schedule',
		'has_not_been_pre_certified' => 'schedule',
		'is_self_funded' => 'schedule',
		'has_oon_benefits' => 'schedule',
		'has_asc_benefits' => 'schedule',
		'has_clauses_under_medicare_entitlement' => 'schedule',
		'has_clauses_under_patient_policy' => 'schedule',
	];

	protected static $alertMessages = [
		'cases_report_completed_48hrs_case_end' => 'Operative Reports has not been completed within 48hrs of case',
		'registration_not_completed' => 'Registration Incomplete',
		'not_insurance_verified' => 'Patient insurance not verified',
		'not_completed_preauthorized' => 'Patient preauthorized incomplete',
		'has_not_been_pre_certified' => 'Patient pre-certification not obtained',
		'is_self_funded' => 'Patient is self-funded',
		'has_oon_benefits' => 'Patient has OON benefits cap',
		'has_asc_benefits' => 'Patient has ASC benefits cap',
		'has_clauses_under_medicare_entitlement' => 'Patient has pre-existing clauses under medicare entitlement',
		'has_clauses_under_patient_policy' => 'Patient has pre-existing clauses under patient policy',
	];

	public static $alertCheckingHandler = [
		'registration_not_completed' => 'IsRegistrationNotCompleted',
		'not_insurance_verified' => 'IsInsuranceNotVerified',
		'not_completed_preauthorized' => 'IsPreAuthorizationCompleted',
		'has_not_been_pre_certified' => 'IsPreCertificationObtained',
		'is_self_funded' => 'IsSelfFunded',
		'has_oon_benefits' => 'IsHasOONBenefits',
		'has_asc_benefits' => 'IsASCBenefits',
		'has_clauses_under_medicare_entitlement' => 'IsHasClausesUnderMedicare',
		'has_clauses_under_patient_policy' => 'IsHasClausesUnderPatient',
	];

	public function toArray()
	{
		$data = parent::toArray();
		if(isset(self::$alertMessages[$this->code])) {
			$data['message'] = self::$alertMessages[$this->code];
		}
		return $data;
	}


}

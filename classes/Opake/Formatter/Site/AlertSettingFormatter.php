<?php

namespace Opake\Formatter\Site;

class AlertSettingFormatter extends BaseSiteFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'name',
				'alert'
			],

			'fieldMethods' => [
				'alert' => ['relationshipOne', [
					'formatter' => [
						'class' => '\Opake\Formatter\BaseDataFormatter',
						'fields' => [
							'id',
							'site_id',
							'enable_for_site',
							'cases_report_completed_48hrs_case_end',
							'not_insurance_verified',
							'not_completed_preauthorized',
							'has_pre_certification_required',
							'has_not_been_pre_certified',
							'is_self_funded',
							'has_oon_benefits',
							'has_asc_benefits',
							'has_clauses_under_medicare_entitlement',
							'has_clauses_under_patient_policy',
							'registration_not_completed',
						],
						'fieldMethods' => [
							'enable_for_site' => 'bool',
							'cases_report_completed_48hrs_case_end' => 'bool',
							'not_insurance_verified' => 'bool',
							'not_completed_preauthorized' => 'bool',
							'has_pre_certification_required' => 'bool',
							'has_not_been_pre_certified' => 'bool',
							'is_self_funded' => 'bool',
							'has_oon_benefits' => 'bool',
							'has_asc_benefits' => 'bool',
							'has_clauses_under_medicare_entitlement' => 'bool',
							'has_clauses_under_patient_policy' => 'bool',
							'registration_not_completed' => 'bool',
							]
						]
					]
				]
			],


		]);
	}
}
<?php

namespace Opake\Formatter\Cases\Item;

class VerificationListFormatter extends ItemFormatter
{
	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'registration_id',
				'time_start',
				'time_end',
				'first_surgeon_for_dashboard',
				'procedure_name_for_dashboard',
				'patient',
				'type',
				'description',
				'verification_status',
				'verification_completed_date'
			],

			'fieldMethods' => [
				'type' => ['relationshipOne', [
						'formatter' => [
							'class' => '\Opake\Formatter\BaseDataFormatter',
							'fields' => [
								'full_name'
							],
							'fieldMethods' => [
									'full_name' => ['modelMethod', [
										'modelMethod' => 'getFullName'
									]
								]
							]
						]
					]
				],
				'patient' => ['patient', [
						'formatter' => [
							'class' => '\Opake\Formatter\BaseDataFormatter',
							'fields' => [
								'first_name',
								'last_name',
								'dob',
								'home_phone',
								'full_mrn'
							],
							'fieldMethods' => [
								'dob' => 'toJsDate',
								'full_mrn' => ['modelMethod', [
										'modelMethod' => 'getFullMrn'
									]
								]
							]
						]
					]
				]
			]
		]);
	}

}
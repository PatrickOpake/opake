<?php

namespace Opake\Formatter\Cases\Item;

use Opake\Formatter\BaseDataFormatter;
use Opake\Model\AbstractModel;

/**
 * @todo: copy all logic from Cases\Item::toArray
 *
 * @package Opake\Formatter\Cases\Item
 */
class ItemFormatter extends BaseDataFormatter
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
				'provider',
				'time_start',
				'time_end',
				'time_check_in',
				'time_start_in_fact',
				'time_end_in_fact',
				'stage',
				'phase',
				'state',
				'status',
				'first_surgeon_for_dashboard',
				'procedure_name_for_dashboard',
				'patient',
				'type',
				'description',
				'verification_status',
				'verification_completed_date',
			    'location',
				'registration',
			    'appointment_status',
			    'notes_count',
		        'first_surgeon_for_label',
		        'accompanied_by',
		        'accompanied_phone',
		        'accompanied_email',
		        'is_staff_checked',
		        'pre_op_none',
		        'pre_op_medical_clearance',
		        'pre_op_labs',
		        'pre_op_xray',
		        'pre_op_ekg',
		        'studies_ordered_none',
		        'studies_ordered_cbc',
		        'studies_ordered_chems',
		        'studies_ordered_ekg',
		        'studies_ordered_pt_pit',
		        'studies_ordered_cxr',
		        'studies_ordered_lft',
		        'studies_ordered_dig_level',
		        'studies_ordered_other',
		        'studies_other',
		        'anesthesia_type',
		        'anesthesia_other',
		        'special_equipment_required',
		        'special_equipment_implants'
			],

			'fieldMethods' => [
				'id' => 'int',
				'registration_id' => 'registrationId',
				'time_start' => 'toJsDate',
				'time_end' => 'toJsDate',
				'time_check_in' => 'toJsDate',
				'time_start_in_fact' => 'toJsDate',
				'time_end_in_fact' => 'toJsDate',
				'provider' => [
					'modelMethod', [
						'modelMethod' => 'getProvider'
					]
				],
				'state' => [
					'modelMethod', [
						'modelMethod' => 'getState'
					]
				],
				'first_surgeon_for_dashboard' => [
					'modelMethod', [
						'modelMethod' => 'getFirstSurgeonForDashboard'
					]
				],
				'procedure_name_for_dashboard' => [
					'modelMethod', [
						'modelMethod' => 'getProcedureNameForDashboard'
					]
				],
				'notes_count' => 'int',
				'is_staff_checked' => [
					'modelMethod', [
						'modelMethod' => 'getStaffTemplateValue'
					]
				],
				'pre_op_none' => 'bool',
				'pre_op_medical_clearance' => 'bool',
				'pre_op_labs' => 'bool',
				'pre_op_xray' => 'bool',
				'pre_op_ekg' => 'bool',
				'studies_ordered_none' => 'bool',
				'studies_ordered_cbc' => 'bool',
				'studies_ordered_chems' => 'bool',
				'studies_ordered_ekg' => 'bool',
				'studies_ordered_pt_pit' => 'bool',
				'studies_ordered_cxr' => 'bool',
				'studies_ordered_lft' => 'bool',
				'studies_ordered_dig_level' => 'bool',
				'studies_ordered_other' => 'bool',
				'type' => ['relationshipOne'],
				'registration' => ['relationshipOne'],
				'patient' => ['patient'],
				'verification_status' => [
					'delegateRelationField', [
						'relation' => 'registration',
						'fieldInRelation' => 'verification_status',
					]
				],
				'verification_completed_date' => [
					'delegateRelationField', [
						'relation' => 'registration',
						'fieldInRelation' => 'verification_completed_date',
						'formatMethod' => 'toJsDate'
					]
				],
			    'opReportId' => ['modelMethod', [
				        'modelMethod' => 'getStaffTemplateValue'
			        ]
			    ]
			]
		]);
	}

	/**
	 * @param string $name
	 * @param array $options
	 * @param AbstractModel $model
	 * @return int
	 */
	public function formatRegistrationId($name, $options, $model)
	{
		return $model->registration->loaded() ? $model->registration->id() : null;
	}

	/**
	 * @param string $name
	 * @param array $options
	 * @param AbstractModel $model
	 * @return array
	 */
	public function formatPatient($name, $options, $model)
	{
		return $this->formatRelationshipOne('patient', $options, $model->registration);
	}

}
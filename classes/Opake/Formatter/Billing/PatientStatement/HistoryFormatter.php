<?php

namespace Opake\Formatter\Billing\PatientStatement;

use Opake\Formatter\BaseDataFormatter;
use Opake\Model\Billing\PatientStatement\History;

class HistoryFormatter extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'patient_id',
			    'date_generated',
			    'print_result_id',
				'url',
				'last_name',
				'first_name',
				'mrn',
				'dob',
			    'is_bulk_print',
				'type'
			],
			'fieldMethods' => [
				'id' => 'int',
				'patient_id' => 'int',
			    'date_generated' => 'toJsDate',
				'url' => 'url',
				'last_name' => [
					'delegateRelationField', [
						'relation' => 'patient',
						'fieldInRelation' => 'last_name',
					]
				],
				'first_name' => [
					'delegateRelationField', [
						'relation' => 'patient',
						'fieldInRelation' => 'first_name',
					]
				],
				'mrn' => [
					'delegateRelationField', [
						'relation' => 'patient',
						'formatMethod' => ['modelMethod', [
							'modelMethod' => 'getFullMrn'
						]]
					]
				],
				'dob' => [
					'delegateRelationField', [
						'relation' => 'patient',
						'fieldInRelation' => 'dob',
						'formatMethod' => 'toJsDate'
					]
				],
			    'is_bulk_print' => 'isBulkPrint',
				'type' => 'type'
			]
		]);
	}

	protected function formatUrl($name, $options, $model)
	{
		if($model->print_result->loaded()) {
			return $model->print_result->getResultUrl();
		}
		return '';
	}

	protected function formatIsBulkPrint($name, $options, $model)
	{
		return ($model->is_bulk_print) ? 'Yes' : 'No';
	}

	protected function formatType($name, $options, $model)
	{
		$typesList = History::getTypeList();
		return (isset($typesList[$model->type])) ? $typesList[$model->type] : '';
	}
}
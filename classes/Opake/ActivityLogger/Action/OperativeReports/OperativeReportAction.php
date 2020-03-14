<?php

namespace Opake\ActivityLogger\Action\OperativeReports;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Extractor\OperativeReports\OperativeReportExtractor;

class OperativeReportAction extends ModelAction
{

	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'report' => $model->id()
		];
	}


	/**
	 * @return array
	 */
	protected function getFieldsForCompare()
	{
		return [
			'name',
			'ebl',
			'anesthesia_administered',
			'pre_op_diagnosis',
			'post_op_diagnosis',
			'operation_time',
			'procedure_id',
			'blood_transfused',
			'specimens_removed',
			'fluids',
			'drains',
			'urine_output',
			'total_tourniquet_time',
			'consent',
			'complications',
			'clinical_history',
			'approach',
			'findings',
			'description_procedure',
			'follow_up_care',
			'conditions_for_discharge',
			'scribe',
		];
	}

	/**
	 * @return OperativeReportExtractor
	 */
	protected function createExtractor()
	{
		return new OperativeReportExtractor();
	}

}
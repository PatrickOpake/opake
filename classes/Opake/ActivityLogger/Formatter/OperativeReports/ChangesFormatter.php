<?php

namespace Opake\ActivityLogger\Formatter\OperativeReports;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'procedure_id':
				return FormatterHelper::formatProcedure($this->pixie, $value);

			case 'post_op_diagnosis':
				return FormatterHelper::formatICDList($this->pixie, $value);

			case 'pre_op_diagnosis':
				return FormatterHelper::formatICDList($this->pixie, $value);
		}

		return $value;
	}

	protected function getIgnored()
	{
		return [
			'pre_op_diagnosis_id',
			'post_op_diagnosis_id'
		];
	}

	protected function getLabels()
	{
		return [
			'anesthesia_administered' => 'Anesthesia Administered',
			'pre_op_diagnosis' => 'Pre-Op Diagnosis',
			'ebl' => 'EBL',
			'post_op_diagnosis' => 'Post-Op Diagnosis',
			'operation_time' => 'Operation Start / Finish Time',
			'procedure_id' => 'Procedure',
			'blood_transfused' => 'Blood Transfused',
			'specimens_removed' => 'Specimens Removed',
			'fluids' => 'Fluids',
			'drains' => 'Drains',
			'urine_output' => 'Urine Output',
			'total_tourniquet_time' => 'Total Tourniquet Time',
			'consent' => 'Consent',
			'complications' => 'Complications',
			'clinical_history' => 'Clinical History & Indications for Procedure',
			'approach' => 'Approach',
			'findings' => 'Findings',
			'description_procedure' => 'Description of Procedure',
			'follow_up_care' => 'Follow Up Care',
			'conditions_for_discharge' => 'Conditions for Discharge',
			'scribe' => 'Scribe'
		];
	}
}


<?php

namespace OpakeApi\Model\Cases\Report;

use Opake\Model\Cases\Report\Diagnosis as OpakeCaseReportDiagnosis;

class Diagnosis extends OpakeCaseReportDiagnosis
{

	public function fromArray($data)
	{
		if (isset($data->case_type) && $data->case_type) {
			$data->case_type_id = $data->case_type->surgerytypeid;
		}
		return $data;
	}

}

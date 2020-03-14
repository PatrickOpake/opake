<?php

namespace OpakeApi\Model\Cases;

use Opake\Model\Cases\Report as OpakeCaseReport;
use OpakeApi\Model\Api;

class Report extends OpakeCaseReport
{
	use Api;

	public function fromArray($data)
	{
		$reportdata = $this->apiFill([
			'caseid' => 'case_id',
			'date' => 'date',
			'provider' => 'provider',
			'desc' => 'desc'
		], $data);

		$types = [];
		foreach ($data->types as $typedata) {
			$types[] = $typedata->surgerytypeid;
		}
		$reportdata['types'] = $types;

		$staff = [];
		foreach ($data->users as $staffdata) {
			$staff[] = $staffdata->userid;
		}
		$reportdata['users'] = $staff;

		return $reportdata;
	}

}

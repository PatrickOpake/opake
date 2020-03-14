<?php

namespace OpakeApi\Model\Cases;

use Opake\Model\Cases\OperativeReport as OpakeCaseOperativeReport;
use OpakeApi\Model\Api;

class OperativeReport extends OpakeCaseOperativeReport
{
	use Api;

	public function apiReportFill(array $rules, $fields)
	{
		$data = [];
		foreach($fields as $fieldObj) {
			if($fieldObj->field && in_array($fieldObj->field->field, $rules)) {
				if (isset($fieldObj->value) || is_null($fieldObj->value)) {
					$data[$fieldObj->field->field] = $fieldObj->value;
				}
			}
		}
		return $data;
	}

	public function apiDiagnosisFill(array $rules, $fields)
	{
		$data = [];
		foreach($fields as $fieldObj) {
			if($fieldObj->field && in_array($fieldObj->field->field, $rules)) {
				if (isset($fieldObj->value)) {
					$result = [];
					foreach ($fieldObj->value as $diagnosis) {
						$result[] = $diagnosis->id;
					}
					$data[$fieldObj->field->field] = $result;
				}
			}
		}
		return $data;
	}

	public function apiFill(array $rules, $object)
	{
		$data = [];
		foreach ($rules as $key => $value) {
			if (property_exists($object, $key)) {
				if (!is_object($object->$key)) {
					$data[$value] = $object->$key;
				}
				if (isset($object->$key->value)) {
					$data[$value] = $object->$key->value;
				}
			}
		}
		return $data;
	}

	public function fromArray($data)
	{

		$reportdata = $this->apiFill([
			'caseid' => 'case_id',
		], $data);

		if (isset($data->applied_template) && $data->applied_template->id) {
			$reportdata['applied_template_id'] = $data->applied_template->id;
		}

		$fields = [
			'consent',
			'complications',
			'clinical_history',
			'approach',
			'findings',
			'description_procedure',
			'follow_up_care',
			'conditions_for_discharge',
			'scribe',
			'specimens_removed',
			'anesthesia_administered',
			'ebl',
			'blood_transfused',
			'fluids',
			'drains',
			'urine_output',
			'total_tourniquet_time',
			'operation_time'
		];

		$reportFields = $this->apiReportFill($fields, $data->fields);

		$reportDiagnosis = $this->apiDiagnosisFill([
			'pre_op_diagnosis',
			'post_op_diagnosis',
		], $data->fields);

		$reportdata = array_merge($reportdata, $reportFields, $reportDiagnosis);

		return $reportdata;
	}

	public function toShortArray()
	{
		$case = $this->getCase();
		$template = $case->getTemplate($this->id());

		$data = [
			'id' => (int)$this->id(),
			'firstname' => $this->case->registration->patient->first_name,
			'lastname' => $this->case->registration->patient->last_name,
			'status' => $this->status,
			'caseid' => (int)$case->id(),
			'type' => $case->type->name,
			'typeid' => (int)$case->type->id,
			'typecode' => $case->type->code,
			'provider' => $case->getProvider(),
			'reportid' => $this->id(),
			'reportstatus' => $this->status,
			'description' => $case->description,
			'patient' => [
				'mrn' => $case->registration->patient->getFullMrn(),
				'fullname' => $case->registration->getFullNameForCalendarCell(),
				'age' => $case->registration->patient->getAge(),
				'sex' => $case->registration->patient->getGender(),
				'dob' => ['field' => $template['dob'], 'value' => $case->registration->patient->dob],
			],
			'locationid' => (int) $case->location->id,
			'locationname' => $case->location->name,
			'datestart' => date('Y-m-d H:i:s O', strtotime($case->time_start)),
			'datefinish' => date('Y-m-d H:i:s O', strtotime($case->time_end)),
			'colors' => [
				'room' => $case->location->getCaseColor(),
				'user' => $case->getUserColor()
			]
		];

		$staff = [];
		foreach ($case->users->find_all() as $user) {
			$staff[] = $user->getFullName();
		}
		$data['staff'] = implode(', ', $staff);


		return $data;
	}
}

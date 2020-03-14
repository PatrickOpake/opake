<?php

namespace OpakeApi\Model\Cases;

use Opake\Helper\StringHelper;
use Opake\Model\Cases\Item as OpakeCase;
use OpakeApi\Model\Cases\OperativeReport\SiteTemplate;
use OpakeApi\Model\Api;

class Item extends OpakeCase
{
	use Api;

	public function apiFill(array $rules, $object)
	{
		$data = [];
		foreach ($rules as $key => $value) {
			if (property_exists($object, $key)) {
				$data[$value] = $object->$key->value;
			}
		}
		return $data;
	}

	public function apiFillSurgeons(array $rules, $fields)
	{
		$data = [];
		foreach($fields as $fieldObj) {
			if(is_object($fieldObj->field) && $fieldObj->field->field === 'staff') {
				foreach($fieldObj->value as $staffObj) {
					$data[$rules[$staffObj->field->field]] = [];
					if (isset($staffObj->value)) {
						foreach ($staffObj->value as $user) {
							$data[$rules[$staffObj->field->field]][] = $user->userid;
						}
					}
				}
			}
		}
		return $data;
	}

	public function fromArray($data)
	{
		$surgeoninfo = $this->apiFillSurgeons([
			'surgeon' => 'users',
			'co_surgeon' => 'co_surgeon',
			'other_staff' => 'other_staff',
			'supervising_surgeon' => 'supervising_surgeon',
			'first_assistant_surgeon' => 'first_assistant_surgeon',
			'assistant' => 'assistant',
			'anesthesiologist' => 'anesthesiologist',
			'dictated_by' => 'dictated_by'
		], $data->fields);

		return $surgeoninfo;
	}

	public function getTemplate($report_id)
	{
		$service_report = $this->pixie->services->get('Cases_OperativeReports');
		$template = $service_report->getFieldsTemplate($this->organization_id, $report_id);


		foreach ($template as $key => $item) {
			$item['report_id'] = $report_id;
			//fixme: force string for API
			$item['sort'] = (string)$item['sort'];
			$template[$key] = $item;
		}

		$templateOptionsList = [
			'surgeon',
			'other_staff',
			'co_surgeon',
			'supervising_surgeon',
			'first_assistant_surgeon',
			'assistant',
			'dictated_by',
			'mrn',
			'dob',
			'admit_type',
			'pre_op_diagnosis',
			'post_op_diagnosis',
			'specimens_removed',
			'anesthesia_administered',
			'blood_transfused',
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
			'conditions_for_discharge',
			'scribe',
			'staffinfo',
			'staff'
		];


		foreach ($templateOptionsList as $optionName) {
			if (!isset($template[$optionName])) {
				$template[$optionName] = [
					'id' => null,
					'organization_id' => $this->organization_id,
					'report_id' => $report_id,
					'group_id' => SiteTemplate::getGroupIdByField($optionName),
					'field' => $optionName,
					'type' => SiteTemplate::getTypeByField($optionName),
					'name' => SiteTemplate::getNameByField($optionName),
					//fixme: force string for API
					'sort' => (string)SiteTemplate::getSortByField($optionName),
					'show' => SiteTemplate::getShowByField($optionName),
					'active' => false,
				];
			}
		}

		foreach(SiteTemplate::$staffProfession as $staffCode => $professionId) {
			if (isset($template[$staffCode])) {
				$template[$staffCode]['medicalProfessions'] = $professionId;
			}
		}

		return $template;
	}

	private function getStaffInfoArray($template)
	{
		$data = [];

		$surgeons = [];
		foreach ($this->users->find_all() as $user) {
			$surgeons[] = $user->toArray(false);
		}
		$data[] = ['field' => $template['surgeon'],  'value' => $surgeons];

		$other_staff = [];
		foreach ($this->other_staff->find_all() as $user) {
			$other_staff[] = $user->toArray(false);
		}
		$data[] = ['field' => $template['other_staff'],  'value' => $other_staff];

		$co_surgeon = [];
		foreach ($this->co_surgeon->find_all() as $user) {
			$co_surgeon[] = $user->toArray(false);
		}
		$data[] = ['field' => $template['co_surgeon'], 'value' => $co_surgeon];

		$supervising_surgeon = [];
		foreach ($this->supervising_surgeon->find_all() as $user) {
			$supervising_surgeon[] = $user->toArray(false);
		}
		$data[] = ['field' => $template['supervising_surgeon'], 'value' => $supervising_surgeon];

		$first_assistant_surgeon = [];
		foreach ($this->first_assistant_surgeon->find_all() as $user) {
			$first_assistant_surgeon[] = $user->toArray(false);
		}
		$data[] = ['field' => $template['first_assistant_surgeon'], 'value' => $first_assistant_surgeon];

		$assistant = [];
		foreach ($this->assistant->find_all() as $user) {
			$assistant[] = $user->toArray(false);
		}
		$data[] = ['field' => $template['assistant'], 'value' => $assistant];

		$anesthesiologist = [];
		foreach ($this->anesthesiologist->find_all() as $user) {
			$anesthesiologist[] = $user->toArray(false);
		}
		$data[] = ['field' => $template['anesthesiologist'], 'value' => $anesthesiologist];

		$dictated_by = [];
		foreach ($this->dictated_by->find_all() as $user) {
			$dictated_by[] = $user->toArray(false);
		}
		$data[] = ['field' => $template['dictated_by'], 'value' => $dictated_by];

		return $data;
	}

	public function toArray()
	{
		$service_report = $this->pixie->services->get('Cases_OperativeReports');
		$data = [];
		$opReport = $this->getOpReport(true, $this->getLoggedUser()->id());

		if($opReport) {
			$pre_op_diagnosis = [];
			foreach ($opReport->pre_op_diagnosis->find_all() as $diagnosis) {
				$pre_op_diagnosis[] = $diagnosis->toArray();
			}
			if (!$pre_op_diagnosis) {
				foreach($this->registration->admitting_diagnosis->find_all() as $diagnosis) {
					$pre_op_diagnosis[] = $diagnosis->toArray();
				}
			}
			$post_op_diagnosis = [];
			foreach ($opReport->post_op_diagnosis->find_all() as $diagnosis) {
				$post_op_diagnosis[] = $diagnosis->toArray();
			}

			$template = $this->getTemplate($opReport->id());

			$data = [
				'caseid' => (int)$this->id,
				'type' => $this->type->name,
				'typeid' => (int)$this->type->id,
				'typecode' => $this->type->code,
				'provider' => $this->getProvider(),
				'reportid' => $opReport->id(),
				'reportstatus' => $opReport->status,
				'patient' => [
					'fullname' => $this->registration->getFullNameForCalendarCell(),
					'age' => $this->registration->patient->getAge(),
					'sex' => $this->registration->patient->getGender(),
					'mrn' => ['field' => $template['mrn'], 'value' => $this->registration->patient->getFullMrn()],
					'dob' => ['field' =>  $template['dob'], 'value' => $this->registration->patient->dob],
				],
				'locationid' => (int)$this->location->id,
				'locationname' => $this->location->name,
				'datestart' => date('Y-m-d H:i:s O', strtotime($this->time_start)),
				'datefinish' => date('Y-m-d H:i:s O', strtotime($this->time_end)),
				'description' => $this->description,
				'admittype' => ['field' => $template['admit_type'], 'value' => [
					'id' => $this->registration->admission_type,
					'name' => $this->registration->getAdmitType()
				]],
				'fields' => [
					['field' => $template['dob'], 'value' => $this->registration->patient->dob],
					['field' => $template['mrn'], 'value' => $this->registration->patient->getFullMrn()],
					['field' => $template['admit_type'], 'value' => [
						'id' => $this->registration->admission_type,
						'name' => $this->registration->getAdmitType()
					]],
					//['field' => $template['procedure'], 'value' => $this->type->name],
					['field' => $template['pre_op_diagnosis'], 'value' => $pre_op_diagnosis],
					['field' => $template['post_op_diagnosis'], 'value' => $post_op_diagnosis],
					['field' => $template['specimens_removed'], 'value' => StringHelper::stripHtmlTags($opReport->specimens_removed)],
					['field' => $template['operation_time'], 'value' => StringHelper::stripHtmlTags($opReport->operation_time)],
					['field' => $template['anesthesia_administered'], 'value' => StringHelper::stripHtmlTags($opReport->anesthesia_administered)],
					['field' => $template['ebl'], 'value' => StringHelper::stripHtmlTags($opReport->ebl)],
					['field' => $template['blood_transfused'], 'value' => StringHelper::stripHtmlTags($opReport->blood_transfused)],
					['field' => $template['fluids'], 'value' => StringHelper::stripHtmlTags($opReport->fluids)],
					['field' => $template['drains'], 'value' => StringHelper::stripHtmlTags($opReport->drains)],
					['field' => $template['urine_output'], 'value' => StringHelper::stripHtmlTags($opReport->urine_output)],
					['field' => $template['total_tourniquet_time'], 'value' => StringHelper::stripHtmlTags($opReport->total_tourniquet_time)],
					['field' => $template['consent'], 'value' => htmlspecialchars_decode(StringHelper::stripHtmlTags($opReport->consent))],
					['field' => $template['complications'], 'value' => StringHelper::stripHtmlTags($opReport->complications)],
					['field' => $template['clinical_history'], 'value' => StringHelper::stripHtmlTags($opReport->clinical_history)],
					['field' => $template['approach'], 'value' => StringHelper::stripHtmlTags($opReport->approach)],
					['field' => $template['findings'], 'value' => StringHelper::stripHtmlTags($opReport->findings)],
					['field' => $template['description_procedure'], 'value' => StringHelper::stripHtmlTags($opReport->description_procedure)],
					['field' => $template['follow_up_care'], 'value' => StringHelper::stripHtmlTags($opReport->follow_up_care)],
					['field' => $template['conditions_for_discharge'], 'value' => StringHelper::stripHtmlTags($opReport->conditions_for_discharge)],
					['field' => $template['scribe'], 'value' => StringHelper::stripHtmlTags($opReport->scribe)],
				],
				'applied_template' => $opReport->applied_template_id ? $opReport->applied_template->toArray() : null,
			];

			$custom_fields = [];
			foreach ($service_report->getCustomFieldsTemplate($this->organization_id, $opReport->id()) as $customField) {
				$customValue = '';
				if(isset($customField['custom_value'])) {
					$customValue = StringHelper::stripHtmlTags($customField['custom_value']);
				}
				$field = [];
				$field['value'] = $customValue;
				$field['field'] = $customField;
				$custom_fields[] = $field;
			}
			$data['fields'] = array_merge($data['fields'], $custom_fields);

			$staff = [];
			foreach ($this->users->find_all() as $user) {
				$staff[] = $user->getFullName();
			}
			$data['staff'] = implode(', ', $staff);
			
			$template['staff']['active'] = true;
			$data['fields'][] = ['field' => $template['staff'], 'value' => $this->getStaffInfoArray($template)];

			$data['fieldgroups'] = [];
			foreach (SiteTemplate::getFieldGroups() as $id => $name) {
				$data['fieldgroups'][] = ['id' => $id, 'name' => $name];
			}
		}


		return $data;
	}

	public function toShortArray()
	{
		$data = [];
		$opReport = $this->getOpReport(true, $this->getLoggedUser()->id());
		$template = $this->getTemplate($opReport->id());

		if($opReport) {
			$data = [
				'caseid' => (int)$this->id,
				'type' => $this->type->name,
				'typeid' => (int)$this->type->id,
				'typecode' => $this->type->code,
				'provider' => $this->getProvider(),
				'reportid' => $opReport->id(),
				'reportstatus' => $opReport->status,
				'description' => $this->description,
				'patient' => [
					'mrn' => $this->registration->patient->getFullMrn(),
					'fullname' => $this->registration->getFullNameForCalendarCell(),
					'age' => $this->registration->patient->getAge(),
					'sex' => $this->registration->patient->getGender(),
					'dob' => ['field' => $template['dob'], 'value' => $this->registration->patient->dob],
				],
				'locationid' => (int) $this->location->id,
				'locationname' => $this->location->name,
				'datestart' => date('Y-m-d H:i:s O', strtotime($this->time_start)),
				'datefinish' => date('Y-m-d H:i:s O', strtotime($this->time_end)),
				'colors' => [
					'room' => $this->location->getCaseColor(),
					'user' => $this->getUserColor()
				]
			];
		}


		$staff = [];
		foreach ($this->users->find_all() as $user) {
			$staff[] = $user->getFullName();
		}
		$data['staff'] = implode(', ', $staff);

		return $data;
	}

}

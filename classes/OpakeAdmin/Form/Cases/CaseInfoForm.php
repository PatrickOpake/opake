<?php

namespace OpakeAdmin\Form\Cases;

use Opake\Model\Cases\Item as CaseItem;
use Opake\Form\AbstractForm;
use Opake\Model\Cases\Item;

class CaseInfoForm extends AbstractForm
{

	protected function prepareValues($data)
	{
		$result = parent::prepareValues($data);
		if (isset($result['location']) && $result['location']) {
			$result['location_id'] = $result['location']->id;
		}
		if (isset($result['type']) && $result['type']) {
			$result['type_id'] = $result['type']->id;
		}
		if (isset($result['time_start'])) {
			$result['time_start'] = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($result['time_start']));
		}
		if (isset($result['time_end'])) {
			$result['time_end'] = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($result['time_end']));
		}
		if (isset($result['time_check_in'])) {
			$result['time_check_in'] = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($result['time_check_in']));
		}
		return $result;
	}

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$caseModel = $this->getModel();

		$timeStart = $this->getValueByName('time_start');
		$timeEnd = $this->getValueByName('time_end');
		$locationId = $this->getValueByName('location_id');
		$patientId = $this->getValueByName('patient')->id;

		$validator->field('time_start')->rule('filled')->rule('date')->error('Incorrect date or time of start');
		$validator->field('time_end')->rule('filled')->rule('date')->error('Incorrect date or time of end');
		$validator->field('time_start')->rule('sequence_dates', $timeEnd)->error('Length of Case must be positive');
		$validator->field('type_id')->rule('filled')->error('You must specify procedure');
		$validator->field('additional_cpts')->rule('filled')->error('You must specify Procedure');
		$validator->field('location_id')->rule('filled')->error('You must specify room');
		$validator->field('users')->rule('filled')->error('You must select at least one user for surgeon');

		/*$validator->field('description')->rule('max_length', 10000)->error('The Description must be less than or equal to 10000 characters');*/

		if($locationId) {
			$validator->field('time_start')->rule('callback', function ($val, $validator, $field) use($caseModel, $timeStart, $timeEnd, $locationId) {
				$query = $this->pixie->orm->get('Cases_Item');
				$query->where([
					['time_start', '<', $timeEnd],
					['time_end', '>', $timeStart],
					['location_id', $locationId],
					['appointment_status', '!=', Item::APPOINTMENT_STATUS_CANCELED],
				]);
				if ($caseModel->id) {
					$query->where($caseModel->table . '.id', '!=', $caseModel->id);
				}
				$query = $query->find();
				return !$query->loaded();
			})->error('Case to the same location at the same time exists');
		}

		if ($patientId) {
			$patientValidation = $validator->field('users')->rule('callback', function ($validator, $field) use ($caseModel, $timeEnd, $timeStart, &$patientValidation, $patientId) {
				$query = $this->pixie->db->query('select')
					->table($caseModel->table)
					->fields($caseModel->table . '.id')
					->join(['case_registration', 'cr'], [$caseModel->table . '.id', 'cr.case_id'])
					->where('cr.patient_id', $patientId)
					->where('time_start', '<', $timeEnd)
					->where('time_end', '>', $timeStart)
					->where('appointment_status', '!=', Item::APPOINTMENT_STATUS_CANCELED);
				if ($caseModel->id) {
					$query->where($caseModel->table . '.id', '!=', $caseModel->id);
				}
				$patient = $query->execute()->as_array();

				if ($patient) {
					$patientValidation->error('Case to the same patient at the same time exists');
				}
				return !$patient;
			});
		}

		$locationValidation = $validator->field('location_id')->rule('callback', function ($location_id, $validator, $field) use (&$locationValidation, $caseModel, $timeEnd, $timeStart, $locationId) {
			$model = $this->pixie->orm->get('Cases_Blocking');
			$model->query
				->join('case_blocking_item', ['case_blocking.id', 'case_blocking_item.blocking_id'])
				->where($this->pixie->db->expr('case_blocking.location_id'), $locationId)
				->where($this->pixie->db->expr('case_blocking_item.start'), '<', $timeEnd)
				->where($this->pixie->db->expr('case_blocking_item.end'), '>', $timeStart)
				->where($this->pixie->db->expr('case_blocking_item.overwrite'), 0);

			$model_users = $caseModel->getUsers();

			$caseUsersPracticeGroupsIds = [0];
			$case_users = [];
			foreach ($model_users as $u) {
				foreach ($u->getPracticeGroupIds() as $practiceGroupId) {
					$caseUsersPracticeGroupsIds[] = $practiceGroupId;
				}
				$case_users[] = $u->id;
			}

			array_unique($caseUsersPracticeGroupsIds);

			if (!$model_users) {
				return true;
			}

			$blockings = $model->with('doctor')
				->where('location_id', $locationId)
				->where('and', [
					['or', ['doctor_id', 'NOT IN', $this->pixie->db->expr('(' . implode(', ', $case_users) . ')')]],
					['or', ['practice_id', 'NOT IN', $this->pixie->db->expr('(' . implode(', ', $caseUsersPracticeGroupsIds) . ')')]]
				])
				->find_all()
				->as_array();


			if ($blockings) {
				$existeMsgs = [];
				foreach ($blockings as $item) {
					if ($item->doctor_id) {
						$existeMsgs[] = $item->doctor->getFullName();
					}
					if ($item->practice_id) {
						$existeMsgs[] = $item->practice->name;
					}
				}
				$locationValidation->error('Selected room is currently blocked for ' . implode(', ', $existeMsgs) . ' during that time. Please modify selection to proceed');
			}
			return !$blockings;
		});

		$locationInServiceValidation = $validator->field('location_id')->rule('callback', function ($location_id, $validator, $field) use (&$locationInServiceValidation, $caseModel, $timeEnd, $timeStart, $locationId) {
			$model = $this->pixie->orm->get('Cases_InService');
			$model->query
				->where($this->pixie->db->expr($model->table . '.location_id'), $locationId)
				->where($this->pixie->db->expr($model->table . '.start'), '<', $timeEnd)
				->where($this->pixie->db->expr($model->table . '.end'), '>', $timeStart);

			$inServices = $model
				->where('location_id', $locationId)
				->find_all()
				->as_array();

			if ($inServices) {
				$locationInServiceValidation->error('Selected room is currently scheduled by InService during that time. Please modify selection to proceed');
			}
			return !$inServices;
		});
	}

	protected function getFields()
	{
		return [
			'organization_id',
			'time_start',
			'time_end',
			'location',
			'users',
			'co_surgeon',
			'supervising_surgeon',
			'first_assistant_surgeon',
			'dictated_by',
			'anesthesiologist',
			'surgeon_assistant',
			'patient',
			'time_check_in',
			'time_start_in_fact',
			'time_end_in_fact',
			'type',
			'location_id',
			'description',
			'stage',
			'phase',
			'state',
			'appointment_status',
			'status',
			'alert_status',
			'started_at',
			'notes_count',
			'accompanied_by',
			'accompanied_phone',
			'accompanied_email',
			'studies_other',
			'anesthesia_type',
			'anesthesia_other',
			'special_equipment_required' ,
			'special_equipment_implants',
			'special_equipment_flag' ,
			'implants',
			'implants_flag' ,
			'locate',
			'transportation' ,
			'transportation_notes',
			'point_of_origin' ,
			'referring_provider_name',
			'referring_provider_npi',
			'date_of_injury' ,
			'is_unable_to_work' ,
			'unable_to_work_from' ,
			'unable_to_work_to',
			'additional_cpts',
			'assistant',
			'other_staff',
		];
	}

	protected function prepareValuesForModel($data)
	{
		return $data;
	}

}

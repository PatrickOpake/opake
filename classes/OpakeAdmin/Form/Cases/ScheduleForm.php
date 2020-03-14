<?php

namespace OpakeAdmin\Form\Cases;

use Opake\Model\Cases\Item as CaseItem;
use Opake\Form\AbstractForm;
use Opake\Helper\TimeFormat;

class ScheduleForm extends AbstractForm
{

	protected function getUserIds()
	{
		$userIds = [];
		foreach ($this->getModel()->users->find_all() as $user) {
			$userIds[] = $user->id;
		}
		return $userIds;
	}

	protected function getUsers()
	{
		$selectedUsers = [];
		foreach ($this->getModel()->users->find_all() as $user) {
			$selectedUsers[$user->id] = $user;
		}
		return $selectedUsers;
	}

	protected function prepareValues($data)
	{
		$result = parent::prepareValues($data);
		// TODO: нужна нормальная работа с временем
		if (isset($result['time_start'])) {
			$result['time_start'] = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($result['time_start']));
		}
		if (isset($result['time_end'])) {
			$result['time_end'] = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($result['time_end']));
		}
		return $result;
	}

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$model = $this->getModel();

		$timeStart = $this->getValueByName('time_start');
		$timeEnd = $this->getValueByName('time_end');
		$locationId = $this->getValueByName('location_id');

		$validator->field('time_start')->rule('filled')->rule('date')->error('Incorrect date or time of start');
		$validator->field('time_end')->rule('filled')->rule('date')->error('Incorrect date or time of end');
		$validator->field('location_id')->rule('filled')->error('You must specify room');

		if ($timeStart && $timeEnd && $locationId) {
			$validator->field('time_start')->rule('sequence_dates', $timeEnd)->error('Start time must be earlier end time');

			$validator->field('time_start')->rule('callback', function () use ($model, $timeStart, $timeEnd, $locationId) {
				$query = $this->pixie->orm->get('Cases_Item');
				$query->where([
					['time_start', '<', $timeEnd],
					['time_end', '>', $timeStart],
					['location_id', $locationId],
					['appointment_status', '!=', CaseItem::APPOINTMENT_STATUS_CANCELED],
				]);
				if ($model->id) {
					$query->where($model->table . '.id', '!=', $model->id);
				}
				return !$query->find()->loaded();
			})->error('Case to the same location at the same time exists');

			$settingService = $this->pixie->services->get('Cases_Settings');
			$setting = $settingService->getSetting($model->organization_id);

			$patientValidation = $validator->field('time_start')->rule('callback', function () use (&$patientValidation, $model, $timeStart, $timeEnd) {
				$query = $this->pixie->db->query('select')
					->table($model->table)
					->fields($model->table . '.id')
					->join(['case_registration', 'cr'], [$model->table . '.id', 'cr.case_id'])
					->where('cr.patient_id', $model->registration->patient_id)
					->where('time_start', '<', $timeEnd)
					->where('time_end', '>', $timeStart)
					->where('appointment_status', '!=', CaseItem::APPOINTMENT_STATUS_CANCELED);
				if ($model->id) {
					$query->where($model->table . '.id', '!=', $model->id);
				}
				$patient = $query->execute()->as_array();

				if ($patient) {
					$patientValidation->error('Case to the same patient at the same time exists');
				}
				return !$patient;
			});

			$locationValidation = $validator->field('location_id')->rule('callback', function () use (&$locationValidation, $locationId, $timeStart, $timeEnd) {
				$userIds = $this->getUserIds();
				if (!$userIds) {
					return true;
				}

				$model = $this->pixie->orm->get('Cases_Blocking');
				$model->query
					->join('case_blocking_item', ['case_blocking.id', 'case_blocking_item.blocking_id'])
					->where($this->pixie->db->expr('case_blocking.location_id'), $locationId)
					->where($this->pixie->db->expr('case_blocking_item.start'), '<', $timeEnd)
					->where($this->pixie->db->expr('case_blocking_item.end'), '>', $timeStart)
					->where($this->pixie->db->expr('case_blocking_item.overwrite'), 0);

				$blockings = $model->with('doctor')
					->where('location_id', $locationId)
					->where('doctor_id', 'NOT IN', $this->pixie->db->expr('(' . implode(', ', $userIds) . ')'))
					->find_all()
					->as_array();

				if ($blockings) {
					$existeMsgs = [];
					foreach ($blockings as $item) {
						$existeMsgs[] = $item->doctor->getFullName();
					}
					$locationValidation->error('Selected room is currently blocked for ' . implode(', ', $existeMsgs) . ' during that time. Please modify selection to proceed');
				}
				return !$blockings;
			});

		}
	}

	protected function getFields()
	{
		return [
			'time_start',
			'time_end',
			'location_id'
		];
	}

}

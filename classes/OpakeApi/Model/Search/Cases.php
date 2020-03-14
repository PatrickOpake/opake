<?php

namespace OpakeApi\Model\Search;

class Cases extends AbstractSearch
{

	protected function prePareQuery($request)
	{
		$this->_params = [
			'start' => trim($request->get('start')),
			'end' => trim($request->get('end'))
		];

		$user = $this->pixie->auth->user();
		$userId = $user->id();

		$model = $this->pixie->orm->get('Cases_Item')
			->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED)
			->where('organization_id', $user->organization_id);

		if ($this->pixie->permissions->getAccessLevel('cases', 'view')->isSelfAllowed()) {

			$model->query->join(['case_user', 'cu'], ['case.id', 'cu.case_id'], 'inner');
			$model->query->join(['case_other_staff', 'cos'], ['case.id', 'cos.case_id'], 'left');
			$model->query->join('case_co_surgeon', ['case.id', 'case_co_surgeon.case_id'], 'left');
			$model->query->join('case_surgeon_assistant', ['case.id', 'case_surgeon_assistant.case_id'], 'left');
			$model->query->join('case_supervising_surgeon', ['case.id', 'case_supervising_surgeon.case_id'], 'left');
			$model->query->join('case_first_assistant_surgeon', ['case.id', 'case_first_assistant_surgeon.case_id'], 'left');
			$model->query->join('case_assistant', ['case.id', 'case_assistant.case_id'], 'left');
			$model->query->join('case_anesthesiologist', ['case.id', 'case_anesthesiologist.case_id'], 'left');
			$model->query->join('case_dictated_by', ['case.id', 'case_dictated_by.case_id'], 'left');

			$usePracticeGroups = false;

			if ($user->isSatelliteOffice()) {
				$userPracticeGroupIds = $user->getPracticeGroupIds();
				if ($userPracticeGroupIds) {
					$model->query->join(['user_practice_groups', 'upg'], ['case_user.user_id', 'upg.user_id'], 'left');
					$model->where('user.organization_id', $user->organization_id);
					$model->where('and', [
						['or', ['upg.practice_group_id', 'IN', $this->pixie->db->expr("(" . implode(',', $userPracticeGroupIds) . ")")]],
						['or', ['case_user.user_id', $user->id()]]
					]);

					$usePracticeGroups = true;
				}
			}

			if (!$usePracticeGroups) {
				$model->where('and', [
					['or', ['cu.user_id', $userId]],
					['or', ['cos.staff_id', $userId]],
					['or', ['case_surgeon_assistant.surgeon_assistant_id', $userId]],
					['or', ['case_co_surgeon.co_surgeon_id', $userId]],
					['or', ['case_supervising_surgeon.supervising_surgeon_id', $userId]],
					['or', ['case_first_assistant_surgeon.assistant_surgeon_id', $userId]],
					['or', ['case_assistant.assistant_id', $userId]],
					['or', ['case_anesthesiologist.anesthesiologist_id', $userId]],
					['or', ['case_dictated_by.dictated_by_id', $userId]],
				]);
			}

		}

		if ($this->_params['start'] !== '') {
			$model->where($this->pixie->db->expr('DATE(case.time_start)'), '>=', $this->_params['start']);
		}
		if ($this->_params['end'] !== '') {
			$model->where($this->pixie->db->expr('DATE(case.time_start)'), '<', $this->_params['end']);
		}
		return $model;
	}

	public function search($request)
	{
		$model = $this->prePareQuery($request);
		return $model->find_all()->as_array();
	}

	public function searchStartTimes($request)
	{
		$model = $this->prePareQuery($request);
		$model->query->fields('time_start');

		$rows = $model->query->execute();

		$results = [];
		foreach ($rows as $row) {
			$results[] = $row->time_start;
		}

		return $results;
	}

}

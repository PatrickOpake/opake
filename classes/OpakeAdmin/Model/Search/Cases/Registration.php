<?php

namespace OpakeAdmin\Model\Search\Cases;

use Opake\Model\Profession;
use Opake\Model\Search\AbstractSearch;

class Registration extends AbstractSearch {

	public function search($model, $request) {

		$model = parent::prepare($model, $request);

		$this->_params = [
		    'first_name' => trim($request->get('first_name')),
		    'last_name' => trim($request->get('last_name')),
		    'search_procedure' => trim($request->get('search_procedure')),
		    'dob' => trim($request->get('dob')),
		    'dos' => trim($request->get('dos')),
		    'status' => trim($request->get('status')),
		    'active' => filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN),
		];

		$sort = $request->get('sort_by', 'dos');
		$order = $request->get('sort_order', 'DESC');

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->join('patient', [$model->table.'.patient_id', 'patient.id'])
			->join('case', [$model->table.'.case_id', 'case.id']);

		if ($this->_params['active']) {
			$startDate = (new \DateTime())->format('Y-m-d') . ' 00:00:00';
			$model->where([
				[$model->table . '.status', '<>', \Opake\Model\Cases\Registration::STATUS_SUBMIT],
				['or', [
						['case.time_start', '>=', $startDate]
					]
				]
			]);
		}

		if ($this->pixie->permissions->getAccessLevel('cases', 'view')->isSelfAllowed()) {

			$user = $this->pixie->auth->user();
			$userId = $user->id();

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
					$model->query->join(['user_practice_groups', 'upg'], ['cu.user_id', 'upg.user_id'], 'left');
					$model->where('and', [
						['or', ['upg.practice_group_id', 'IN', $this->pixie->db->expr("(" . implode(',', $userPracticeGroupIds) . ")")]],
						['or', ['cu.user_id', $user->id()]]
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

			$model->query->group_by('case.id');
		}

		if ($this->_params['first_name'] !== '') {
			$model->where($model->table . '.first_name', 'like', '%' . $this->_params['first_name'] . '%');
		}

		if ($this->_params['last_name'] !== '') {
			$model->where($model->table . '.last_name', 'like', '%' . $this->_params['last_name'] . '%');
		}

		if ($this->_params['dos'] !== '') {
			$model->where($this->pixie->db->expr('DATE(case.time_start)'), \Opake\Helper\TimeFormat::formatToDB($this->_params['dos']));
		}

		if ($this->_params['search_procedure'] !== '') {
			$model->query->join('case_type', ['case.type_id', 'case_type.id']);
			$model->where(['case_type.code', 'like', '%' . $this->_params['search_procedure'] . '%'],
					['or', [
						['case_type.name', 'like', '%' . $this->_params['search_procedure'] . '%']
					]
				]);
		}

		if ($this->_params['dob'] !== '') {
			$model->where($this->pixie->db->expr('DATE(patient.dob)'), \Opake\Helper\TimeFormat::formatToDB($this->_params['dob']));
		}

		if($this->_params['status'] !== '') {
			$model->where($model->table . '.status', $this->_params['status']);
		}

		switch ($sort) {
			case 'acc_number': $model->order_by('case.id', $order);break;
			case 'dos': $model->order_by('case.time_start', $order);break;
			case 'first_name': $model->order_by($model->table . '.first_name', $order); break;
			case 'last_name': $model->order_by($model->table . '.last_name', $order); break;
			case 'appointment': $model->order_by($this->pixie->db->expr('TIME(case.time_start)'), $order); break;
			case 'dob': $model->order_by('patient.dob', $order); break;
			case 'status': $model->order_by($model->table . '.status', $order); break;
			case 'procedure':
				$model->query->join('case_type', ['case.type_id', 'case_type.id']);
				$model->order_by('case_type.code', $order);
			break;
		}

		$results = $model->pagination($this->_pagination)->find_all()->as_array();

		$count = $this->pixie->db
				->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
				->execute()->get('count');

		$this->_pagination->setCount($count);

		return $results;
	}

}

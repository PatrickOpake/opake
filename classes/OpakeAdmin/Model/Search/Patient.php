<?php

namespace OpakeAdmin\Model\Search;

use Opake\Model\Search\AbstractSearch;

class Patient extends AbstractSearch
{

	public function search($model, $request)
	{

		$model = parent::prepare($model, $request);

		$this->_params = [
			'first_name' => trim($request->get('first_name')),
			'last_name' => trim($request->get('last_name')),
			'ssn' => trim($request->get('ssn')),
			'mrn' => trim($request->get('mrn')),
			'home_phone' => trim($request->get('home_phone')),
			'dob' => trim($request->get('dob')),
			'surgeons' => trim($request->get('surgeons'))
		];

		$sort = $request->get('sort_by', 'name');
		$order = $request->get('sort_order', 'ASC');

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));

		$user = $this->pixie->auth->user();
		if(!$user->isInternal()) {
			$model->where($model->table . '.status', \Opake\Model\Patient::STATUS_ACTIVE);
		}

		if ($this->_params['first_name'] !== '') {
			$model->where($model->table . '.first_name', 'like', '%' . $this->_params['first_name'] . '%');
		}

		if ($this->_params['last_name'] !== '') {
			$model->where($model->table . '.last_name', 'like', '%' . $this->_params['last_name'] . '%');
		}

		if ($this->_params['ssn'] !== '') {
			$model->where($model->table . '.ssn', 'like', '%' . $this->_params['ssn'] . '%');
		}

		if ($this->_params['mrn'] !== '') {
			$model->query->where($this->pixie->db->expr("CONCAT(LPAD(patient.mrn, 5, '0'), '-', patient.mrn_year)"), 'like', '%' . $this->_params['mrn'] . '%');
		}

		if ($this->_params['home_phone'] !== '') {
			$model->where($model->table . '.home_phone', 'like', '%' . $this->_params['home_phone'] . '%');
		}

		if ($this->_params['dob'] !== '') {
			$model->where($this->pixie->db->expr('DATE(' . $model->table . '.dob)'), \Opake\Helper\TimeFormat::formatToDB($this->_params['dob']));
		}

		if ($this->_params['surgeons'] !== '') {
			$surgeonsIds = json_decode($this->_params['surgeons'], true);
			if (count($surgeonsIds)) {
				$model->query->join('case_registration', [$model->table . '.id', 'case_registration.patient_id'])
					->join('case', ['case_registration.case_id', 'case.id'])
					->join('case_user', ['case.id', 'case_user.case_id'])
					->where('case_user.user_id', 'IN', $this->pixie->db->expr("(" . implode(',', $surgeonsIds) . ")"))
					->group_by('patient.id');
			}
		}

		if ($this->pixie->permissions->getAccessLevel('patients', 'view')->isSelfAllowed()) {

			$q = $this->pixie->db->query('select')
				->table('patient')
				->fields($this->pixie->db->expr('DISTINCT patient.id'))
				->join('case_registration', ['patient.id', 'case_registration.patient_id'], 'inner')
				->join('case', ['case_registration.case_id', 'case.id'], 'inner')
				->join('case_user', ['case.id', 'case_user.case_id'], 'inner');

			//allowed for all professions
			$userPracticeGroupIds = $user->getPracticeGroupIds();

			if ($userPracticeGroupIds) {
				$q->join('user', ['user.id', 'case_user.user_id'], 'inner');
				$q->join('user_practice_groups', ['case_user.user_id', 'user_practice_groups.user_id'], 'left');
				$q->where('user.organization_id', $user->organization_id);
				$q->where('and', [
					['or', ['user_practice_groups.practice_group_id', 'IN', $this->pixie->db->expr("(" . implode(',', $userPracticeGroupIds) . ")")]],
					['or', ['case_user.user_id', $user->id()]]
				]);
			} else {
				$q->where('case_user.user_id', $user->id());
			}

			$patientIds = [];
			foreach ($q->execute() as $row) {
				$patientIds[] = $row->id;
			}

			if (!$patientIds) {
				$patientIds = [-1];
			}

			$model->where('id', 'IN', $this->pixie->db->expr('(' . implode(',', $patientIds) . ')'));
		}

		switch ($sort) {
			case 'id':
				$model->order_by($model->table . '.id', $order);
				break;
			case 'name':
				$model->order_by($model->table . '.last_name', 'asc')->order_by($model->table . '.first_name', 'asc');
				break;
			case 'first_name':
				$model->order_by($model->table . '.first_name', $order);
				break;
			case 'last_name':
				$model->order_by($model->table . '.last_name', $order);
				break;
			case 'ssn':
				$model->order_by($model->table . '.ssn', $order);
				break;
			case 'dob':
				$model->order_by($model->table . '.dob', $order);
				break;
			case 'home_phone':
				$model->order_by($model->table . '.home_phone', $order);
				break;
		}

		if ($this->_pagination) {
			$model->pagination($this->_pagination);
		}

		$results = $model->find_all()->as_array();

		$count = $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');

		if ($this->_pagination) {
			$this->_pagination->setCount($count);
		}

		return $results;
	}

}

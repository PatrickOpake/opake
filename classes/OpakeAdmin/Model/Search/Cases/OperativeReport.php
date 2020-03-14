<?php

namespace OpakeAdmin\Model\Search\Cases;

use Opake\Model\Search\AbstractSearch;

class OperativeReport extends AbstractSearch
{

	/**
	 * Params
	 * @var array
	 */
	public function search($model, $request)
	{

		$model = parent::prepare($model, $request);

		$this->_params = [
			'id' => trim($request->get('id')),
			'user_id' => trim($request->get('user_id')),
			'type' => trim($request->get('type')),
			'first_name' => trim($request->get('first_name')),
			'last_name' => trim($request->get('last_name')),
			'case_type' => trim($request->get('case_type')),
			'age' => trim($request->get('age')),
			'dos' => trim($request->get('dos')),
		];

		$sort = $request->get('sort_by', 'dos');
		$order = $request->get('sort_order', 'DESC');

		if (!$this->pixie->permissions->checkAccess('operative_reports', 'view')) {
			return [];
		}

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->join('case', [$model->table . '.case_id', 'case.id'])
			->where('case.appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED)
			->group_by($model->table . '.id');

		$model->query->join('case_user', ['case.id', 'case_user.case_id'], 'inner');

		if ($this->_params['user_id'] !== '' && $this->pixie->permissions->checkAccess('operative_reports', 'view')) {
			$userId = $this->_params['user_id'];
			$user = $this->pixie->orm->get('User', $userId);
			if (!$user->loaded()) {
				throw new \Exception('Unknown user');
			}
		} else {
			$user = $this->pixie->auth->user();
		}

		$this->queryUserAccess($model, $user);
		$this->querySurgeonType($model, $user);

		if ($this->_params['type'] !== '') {
			$this->queryAlert($model, $this->_params['type']);
		}

		if ($this->_params['dos'] !== '') {
			$model->where($this->pixie->db->expr('DATE(case.time_start)'), \Opake\Helper\TimeFormat::formatToDB($this->_params['dos']));
		}

		switch ($sort) {
			case 'first_name':
				$model->query
					->join('case_registration', ['case_registration.case_id', 'case.id'])
					->join('patient', ['patient.id', 'case_registration.patient_id']);
				$model->order_by('patient.first_name', $order);
				break;
			case 'last_name':
				$model->query
					->join('case_registration', ['case_registration.case_id', 'case.id'])
					->join('patient', ['patient.id', 'case_registration.patient_id']);
				$model->order_by('patient.last_name', $order);
				break;
			case 'case_type':
				$model->query->join('case_type', ['case.type_id', 'case_type.id']);
				$model->order_by('case_type.name', $order);
				break;
			case 'age':
				$model->query
					->join('case_registration', ['case_registration.case_id', 'case.id'])
					->join('patient', ['patient.id', 'case_registration.patient_id']);
				if ($order === 'DESC') {
					$order = 'ASC';
				} else {
					$order = 'DESC';
				}
				$model->order_by('patient.dob', $order);
				break;
			case 'dos':
				$model->order_by('case.time_start', $order);
				break;
		}

		$results = $model->pagination($this->_pagination)->find_all()->as_array();

		$count = $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');

		$this->_pagination->setCount($count);

		return $results;
	}

	public function searchForPatient($model, $patient_id)
	{
		if (!$patient_id) {
			return [];
		}

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->join('case', [$model->table . '.case_id', 'case.id'])
			->join('case_registration', ['case_registration.case_id', 'case.id']);
		$model->where('case_registration.patient_id', $patient_id);
		$model->order_by('case.time_start', 'desc');

		return $model->find_all()->as_array();
	}

	public function getCountByAlert($main_model, $type, $userId = null)
	{
		$model = $this->pixie->orm->get('Cases_OperativeReport');
		$model->query = clone $main_model->query;
		$model->query->fields($this->pixie->db->expr($model->table . '.*'))
			->join('case', [$model->table . '.case_id', 'case.id'])
			->where('case.appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED)
			->group_by($model->table . '.id');


		$model->query->join('case_user', ['case.id', 'case_user.case_id'], 'inner');

		if ($userId && $this->pixie->permissions->checkAccess('operative_reports', 'view')) {
			$user = $this->pixie->orm->get('User', $userId);
			if (!$user->loaded()) {
				throw new \Exception('Unknown user');
			}
		} else {
			$user = $this->pixie->auth->user();
		}

		$this->queryUserAccess($model, $user);
		$this->querySurgeonType($model, $user);

		$this->queryAlert($model, $type);

		return (int)$this->pixie->db
			->query('select')->fields($this->pixie->db->expr('COUNT(*) as count'))
			->table($model->query)
			->execute()->get('count');
	}

	protected function queryUserAccess($model, $user)
	{
		$model->query->where($model->table . '.surgeon_id', $user->id());
	}

	protected function queryAlert($model, $type)
	{
		$openStatuses = [
			\Opake\Model\Cases\OperativeReport::STATUS_OPEN,
			\Opake\Model\Cases\OperativeReport::STATUS_DRAFT
		];

		$submittedStatuses = [
			\Opake\Model\Cases\OperativeReport::STATUS_SUBMITTED,
			\Opake\Model\Cases\OperativeReport::STATUS_SIGNED
		];

		if ($type === 'open') {
			$model->where($model->table . '.status', 'IN', $this->pixie->db->expr('(' . implode(', ', $openStatuses) . ')'));
		} else if ($type === 'submitted') {
			$model->where($model->table . '.status', 'IN', $this->pixie->db->expr('(' . implode(', ', $submittedStatuses) . ')'));
		}

		$model->where($model->table . '.is_archived', 0);
	}

	/**
	 * @param $model
	 * @param $user
	 */
	protected function querySurgeonType($model, $user)
	{
		if(\Opake\Model\Cases\OperativeReport::isNonSurgeonReport($user)) {
			$model->query->where($model->table . '.type', \Opake\Model\Cases\OperativeReport::TYPE_NON_SURGEON);
		} else {
			$model->query->where(
				$model->table . '.type', 'IN', $this->pixie->db->arr(\Opake\Model\Cases\OperativeReport::getTypeSurgeons())
			);
		}
	}
}
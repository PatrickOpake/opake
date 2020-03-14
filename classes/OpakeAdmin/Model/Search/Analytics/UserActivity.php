<?php

namespace OpakeAdmin\Model\Search\Analytics;

use Opake\Helper\TimeFormat;
use Opake\Model\Search\AbstractSearch;

class UserActivity extends AbstractSearch
{
	protected $organizationId;

	/**
	 * @return mixed
	 */
	public function getOrganizationId()
	{
		return $this->organizationId;
	}

	/**
	 * @param mixed $organizationId
	 */
	public function setOrganizationId($organizationId)
	{
		$this->organizationId = $organizationId;
	}

	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$this->_params = [
			'action' => trim($request->get('action')),
			'organization' => trim($request->get('organization')),
			'user' => trim($request->get('user')),
			'date_from' => trim($request->get('date_from')),
			'date_to' => trim($request->get('date_to')),
			'case' => trim($request->get('case')),
		    'patient' => trim($request->get('patient'))
		];

		$sort = $request->get('sort_by', 'id');
		$order = $request->get('sort_order', 'ASC');

		$fields = [];
		$fields[] = $this->pixie->db->expr('DISTINCT SQL_CALC_FOUND_ROWS `user_activity`.*');

		$model->query->join('user', ['user_activity.user_id', 'user.id'], 'left');

		$user = $this->pixie->auth->user();
		if ($this->organizationId) {
			$model->query->where('user.organization_id', $this->organizationId);
		} else if ($user && !$user->isInternal()) {
			$model->query->where('user.organization_id', $user->organization_id);
		}

		if (!empty($this->_params['action'])) {
			$model->query->where('user_activity.action', $this->_params['action']);
		}

		if (!empty($this->_params['case']) || !empty($this->_params['patient']) || $sort === 'case') {
			$joinType = ($sort === 'case') ? 'left' : 'inner';
			$model->query->join('user_activity_search_params', ['user_activity_search_params.user_activity_id', 'user_activity.id'], $joinType);

			if (!empty($this->_params['case'])) {
				$cases = explode(',', $this->_params['case']);
				$cases = array_map('trim', $cases);
				$model->query->where('user_activity_search_params.case_id', 'IN', $this->pixie->db->arr($cases));
			}

			if (!empty($this->_params['patient'])) {
				$casesForPatient = $this->pixie->orm->get('Cases_Item');
				$casesForPatientQuery = $casesForPatient->query;
				$casesForPatientQuery->fields('case.*');

				$casesForPatientQuery->join('case_registration', ['case_registration.case_id', 'case.id'], 'inner')
					->where('case_registration.patient_id', $this->_params['patient']);

				$casesForPatientIds = [];
				foreach ($casesForPatient->find_all()->as_array() as $case) {
					$casesForPatientIds[] = $case->id;
				}

				if (count($casesForPatientIds)) {
					$model->query->where('and', [
						['or', ['user_activity_search_params.patient_id', $this->_params['patient']]],
						['or', ['user_activity_search_params.case_id', 'IN', $this->pixie->db->arr($casesForPatientIds)]]
					]);
				} else {
					$model->query->where('user_activity_search_params.patient_id', $this->_params['patient']);
				}
			}
		}

		if (!empty($this->_params['organization'])) {
			$model->query->where('user.organization_id', $this->_params['organization']);
		}

		if (!empty($this->_params['user'])) {
			$model->query->where('user.id', $this->_params['user']);
		}

		if (!empty($this->_params['date_from'])) {
			$dateTime = TimeFormat::fromDBDate($this->_params['date_from']);
			if ($dateTime) {
				$dateTime->setTime(0, 0, 0);
				$model->query->where('user_activity.date', '>=', TimeFormat::formatToDBDatetime($dateTime));
			}
		}

		if (!empty($this->_params['date_to'])) {
			$dateTime = TimeFormat::fromDBDate($this->_params['date_to']);
			if ($dateTime) {
				$dateTime->setTime(23, 59, 59);
				$model->query->where('user_activity.date', '<=', TimeFormat::formatToDBDatetime($dateTime));
			}
		}

		if ($sort === 'organization') {
			$model->query->join('organization', ['user.organization_id', 'organization.id'], 'left');
		}

		if ($sort === 'action') {
			$model->query->join('user_activity_action', ['user_activity.action', 'user_activity_action.id'], 'left');
			$model->query->join('user_activity_action_zone', ['user_activity_action.zone', 'user_activity_action_zone.id'], 'left');
			$fields[] = $this->pixie->db->expr("CONCAT(user_activity_action_zone.name, ' ', user_activity_action.name) as `action_name`");
		}

		switch ($sort) {
			case 'id':
				$model->query->order_by('user_activity.id', $order);
				break;
			case 'date':
			case 'time':
				$model->query->order_by('user_activity.date', $order);
				break;
			case 'organization':
				$model->query->order_by('organization.name', $order);
				break;
			case 'action':
				$model->query->order_by($this->pixie->db->expr('`action_name`'), $order);
				break;
			case 'user':
				$model->query->order_by('user.first_name', $order);
				break;
			case 'case':
				$model->query->order_by('user_activity_search_params.case_id', $order);
				break;
		}

		if ($this->_pagination) {
			$model->pagination($this->_pagination);
		}

		call_user_func_array([$model->query, 'fields'], $fields);

		$results = $model->find_all();

		$count = $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');

		if ($this->_pagination) {
			$this->_pagination->setCount($count);
		}

		return $results;

	}

}
<?php

namespace OpakeAdmin\Model\Search;

use Opake\Model\Search\AbstractSearch;

class User extends AbstractSearch
{

	public function search($model, $request)
	{

		$model = parent::prepare($model, $request);

		$this->_params = [
			'user_id' => trim($request->get('user_id')),
			'user' => trim($request->get('user')),
			'keyword' => trim($request->get('keyword')),
			'site' => trim($request->get('site')),
			'site_id' => trim($request->get('site_id')),
			'org' => trim($request->get('org')),
			'profession' => trim($request->get('profession')),
			'for_op_reports_list' => trim($request->get('for_op_reports_list'))
		];

		$sort = $request->get('sort_by', 'full_name');
		$order = $request->get('sort_order', 'ASC');

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->group_by('user.id');

		if ($this->_params['site'] !== '') {
			$model->query->join('user_site', [$model->table . '.id', 'user_site.user_id']);
			$model->query->join('site', ['user_site.site_id', 'site.id']);
			$model->where('site.name', 'like', '%' . $this->_params['site'] . '%');
		}
		if ($this->_params['org'] !== '') {
			$model->query->join('organization', [$model->table . '.organization_id', 'organization.id']);
			$model->where('organization.name', 'like', '%' . $this->_params['org'] . '%');
		}

		if (!empty($this->_params['profession'])) {
			$model->where('profession_id', $this->_params['profession']);
		}

		if ($this->_params['site_id'] !== '') {
			$model->query->join('user_site', [$model->table . '.id', 'user_site.user_id']);
			$model->where('user_site.site_id', $this->_params['site_id']);
		}

		if ($this->_params['user'] !== '' || $this->_params['keyword'] !== '') {
			$search = $this->_params['user'] ? $this->_params['user'] : $this->_params['keyword'];

			$model->where('and', [
				['or', [$this->pixie->db->expr("CONCAT_WS(' ',first_name,last_name)"), 'like', '%' . $search . '%']],
				['or', ['email', 'like', $search . '%']]
			]);
		}

		if ($this->_params['user_id'] !== '') {
			$model->where($model->table . '.id', $this->_params['user_id']);
		}

		$user = $this->pixie->auth->user();

		if ($user->isSatelliteOffice()) {

			$userPracticeGroupIds = $user->getPracticeGroupIds();
			if ($userPracticeGroupIds) {
				$model->query
					->join(['user_practice_groups', 'upg'], ['user.id', 'upg.user_id'], 'left');

				$model->where('user.organization_id', $user->organization_id);
				$model->where('upg.practice_group_id', 'IN', $this->pixie->db->expr("(" . implode(',', $userPracticeGroupIds) . ")"));
				$model->where('user.id', '!=', $user->id());
			}
		}

		switch ($sort) {
			case 'full_name':
				$model->order_by($model->table . '.first_name', $order)
					->order_by($model->table . '.last_name', $order);
				break;
			case 'username':
				$model->order_by($model->table . '.username', $order);
				break;
			case 'email':
				$model->order_by($model->table . '.email', $order);
				break;
			case 'status':
				$model->order_by($model->table . '.status', $order);
				$model->order_by($model->table . '.first_name', $order)
					->order_by($model->table . '.last_name', $order);
				break;
			case 'time_first_login':
				$model->order_by($model->table . '.time_first_login', $order);
				break;
			case 'time_last_login':
				$model->order_by($model->table . '.time_last_login', $order);
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

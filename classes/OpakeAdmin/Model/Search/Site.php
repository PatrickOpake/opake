<?php

namespace OpakeAdmin\Model\Search;

use Opake\Model\Search\AbstractSearch;

class Site extends AbstractSearch
{
	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$this->_params = [
			'logged_user_sites' => trim($request->get('logged_user_sites'))
		];

		$sort = $request->get('sort_by', 'name');
		$order = $request->get('sort_order', 'ASC');

		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'), $this->pixie->db->expr('d.departments_count, count(u.id) as users_count'))
			->join([$this->pixie->db->query('select')
				->fields('site.id', $this->pixie->db->expr('count(department.id) as departments_count'))
				->table($model->table, 'site')
				->join(['department_site', 'ds'], ['site.id', 'ds.site_id'])
				->join('department', ['department.id', 'ds.department_id'])
				->group_by('site.id')
				, 'd'], [$model->table . '.id', 'd.id'])
			->join(['user_site', 'us'], ['site.id', 'us.site_id'])
			->join(['user', 'u'], ['u.id', 'us.user_id'])
			->where($model->table . '.active', true)
			->group_by($model->table . '.id');

		$user = $this->pixie->auth->user();
		if ($this->_params['logged_user_sites'] !== '' && !$user->isInternal() ) {
			$model->where('us.user_id',  $user->id());
		}

		switch ($sort) {
			case 'name':
				$model->order_by($model->table . '.name', $order);
				break;
			case 'description':
				$model->order_by($model->table . '.description', $order)
					->order_by($model->table . '.name', $order);
				break;
			case 'departments_count':
				$model->order_by($this->pixie->db->expr('departments_count'), $order)
					->order_by($model->table . '.name', $order);
				break;
			case 'users_count':
				$model->order_by($this->pixie->db->expr('users_count'), $order)
					->order_by($model->table . '.name', $order);
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

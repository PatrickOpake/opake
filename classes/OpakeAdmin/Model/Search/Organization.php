<?php

namespace OpakeAdmin\Model\Search;

use Opake\Model\Search\AbstractSearch;

class Organization extends AbstractSearch
{

	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$this->_params = [
			'org' => trim($request->get('organization')),
			'user' => trim($request->get('user')),
			'active' => filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN)
		];

		$sort = $request->get('sort_by', 'name');
		$order = $request->get('sort_order', 'ASC');

		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'), $this->pixie->db->expr('s.sites_count, count(u.id) as users_count'))
			->join([$this->pixie->db->query('select')
				->fields('org.id', $this->pixie->db->expr('count(site.id) as sites_count'))
				->table($model->table, 'org')
				->join('site', ['org.id', 'site.organization_id'])
				->group_by('org.id')
				, 's'], [$model->table . '.id', 's.id'])
			->join(['user', 'u'], [
				[$model->table . '.id', 'u.organization_id'],
				['u.status', $this->pixie->db->expr('\'' . \Opake\Model\User::STATUS_ACTIVE . '\'')]
			])
			->group_by($model->table . '.id');

		if ($this->_params['org'] !== '') {
			$model->where('name', 'like', '%' . $this->_params['org'] . '%');
		}
		if ($this->_params['active']) {
			$model->where('status', \Opake\Model\Organization::STATUS_ACTIVE);
		}

		switch ($sort) {
			case 'name':
				$model->order_by($model->table . '.name', $order);
				break;
			case 'sites_count':
				$model->order_by($this->pixie->db->expr('sites_count'), $order);
				$model->order_by($model->table . '.name', $order);
				break;
			case 'users_count':
				$model->order_by($this->pixie->db->expr('users_count'), $order);
				$model->order_by($model->table . '.name', $order);
				break;
			case 'time_create':
				$model->order_by($model->table . '.time_create', $order);
				break;
			case 'status':
				$model->order_by($model->table . '.status', $order);
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

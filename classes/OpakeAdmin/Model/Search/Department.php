<?php

namespace OpakeAdmin\Model\Search;

use Opake\Model\Search\AbstractSearch;

class Department extends AbstractSearch
{
	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$sort = $request->get('sort_by', 'name');
		$order = $request->get('sort_order', 'ASC');

		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->group_by($model->table . '.id');

		switch ($sort) {
			case 'name':
				$model->order_by($model->table . '.name', $order);
				break;
			case 'active':
				$model->order_by($model->table . '.active', $order)
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

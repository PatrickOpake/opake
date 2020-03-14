<?php

namespace OpakeAdmin\Model\Search\Card;

use Opake\Model\Search\AbstractSearch;

class Staff extends AbstractSearch {

	public function search($model, $request) {

		$model = parent::prepare($model, $request);

		$this->_params = [
		    'user_id' => trim($request->get('user_id'))
		];

		$sort = $request->get('sort_by', 'case_type');
		$order = $request->get('sort_order', 'ASC');

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->join('user', [$model->table . '.user_id', 'user.id']);

		if ($this->_params['user_id'] !== '') {
			$model->where('user_id', $this->_params['user_id']);
		}

		switch ($sort) {
			case 'last_edit_date':
				$model->order_by($model->table . '.last_edit_date', $order);
				break;
			case 'name':
				$model->order_by($model->table . '.name', $order);
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

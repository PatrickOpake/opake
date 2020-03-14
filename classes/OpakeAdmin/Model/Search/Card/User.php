<?php

namespace OpakeAdmin\Model\Search\Card;

use Opake\Model\Search\AbstractSearch;

class User extends AbstractSearch {

	public function search($model, $request) {

		$model = parent::prepare($model, $request);

		$this->_params = [
			'user_id' => trim($request->get('user_id')),
		];

		$sort = $request->get('sort_by', 'full_name');
		$order = $request->get('sort_order', 'ASC');

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'), $this->pixie->db->expr('count(card.id) as card_amount'))
			->join(['pref_card_staff', 'card'], ['user.id', 'card.user_id'])
			->where('user.role_id', \Opake\Model\Role::Doctor)
			->group_by('user.id');

		if ($this->_params['user_id'] !== '') {
			$model->where($model->table . '.id', $this->_params['user_id']);
		}

		switch ($sort) {
			case 'full_name':
				$model->order_by($model->table . '.first_name', $order)
					->order_by($model->table . '.last_name', $order);
				break;
			case 'card_amount': $model->order_by($this->pixie->db->expr('card_amount'), $order)
					->order_by($model->table . '.first_name', 'ASC')
					->order_by($model->table . '.last_name', 'ASC');
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

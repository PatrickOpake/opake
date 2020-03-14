<?php

namespace OpakeAdmin\Model\Search\Cases;

use Opake\Model\Search\AbstractSearch;

class Type extends AbstractSearch {

	public function search($model, $request) {

		$model = parent::prepare($model, $request);

		$this->_params = [
			'keyword' => trim($request->get('keyword')),
			'org_id' => trim($request->get('org_id'))
		];

		$order = $request->get('sort_order', 'ASC');
		$model->order_by('code', $order);

		if ($this->_params['keyword'] !== '') {
			$model->where('name', 'like', '%' . $this->_params['keyword'] . '%');
		}
		if ($this->_params['org_id'] !== '') {
			$model->where('id', 'IN', $this->pixie->db
					->query('select')
					->fields($this->pixie->db->expr('DISTINCT case_type_id'))
					->table('pref_card_staff', 'card')
					->join(['user', 'u'], ['card.user_id', 'u.id'])
					->where('u.organization_id', $this->_params['org_id'])
			);
		}
		$model->where('active', true);

		$this->_pagination->setCount($model->count_all());
		return $model->pagination($this->_pagination)->find_all()->as_array();
	}

}

<?php

namespace OpakeAdmin\Model\Search\Cases;

use Opake\Model\Search\AbstractSearch;

class CaseType extends AbstractSearch
{
	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$this->_params = [
			'name' => trim($request->get('name')),
			'code' => trim($request->get('code')),
			'cpt_name' => trim($request->get('cpt_name')),
			'active' => trim($request->get('active')),
			'length' => trim($request->get('length'))
		];

		$sort = $request->get('sort_by', 'name');
		$order = $request->get('sort_order', 'ASC');

		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->group_by($model->table .  '.id');

		if ($this->_params['name'] !== '') {
			$model->where($model->table . '.name', 'like', '%' . $this->_params['name'] . '%');
		}

		if ($this->_params['code'] !== '') {
			$model->where($model->table . '.code', 'like', '%' . $this->_params['code'] . '%');
		}

		if ($this->_params['cpt_name'] !== '') {
			$model->query
				->join(['case_type_cpt', 'ct_cpt'], ['case_type.id', 'ct_cpt.case_type_id'])
				->join('cpt', ['cpt.id', 'ct_cpt.cpt_id'])
				->where('cpt.name', 'like', '%' . $this->_params['cpt_name'] . '%');
		}

		if ($this->_params['active'] !== '') {
			$model->where($model->table . '.active', $this->_params['active']);
		}

		switch ($sort) {
			case 'name':
				$model->order_by($model->table . '.name', $order);
				break;
			case 'code':
				$model->order_by($model->table . '.code', $order)
					->order_by($model->table . '.name', $order);;
				break;
			case 'active':
				$model->order_by($model->table . '.active', $order)
					->order_by($model->table . '.name', $order);
				break;
			case 'length':
				$model->order_by($model->table . '.length', $order)
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

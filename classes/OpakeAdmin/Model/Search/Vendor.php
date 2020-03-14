<?php

namespace OpakeAdmin\Model\Search;

use Opake\Model\Search\AbstractSearch;

class Vendor extends AbstractSearch
{

	public function search($model, $request)
	{

		$model = parent::prepare($model, $request);

		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->order_by($model->table . '.name', 'asc');

		$this->_params = [
			'name' => trim($request->get('name')),
			'type' => trim($request->get('type')),
			'vend_id' => trim($request->get('vend_id'))
		];

		if ($this->_params['name'] !== '') {
			$model->where('name', 'like', '%' . $this->_params['name'] . '%');
		}
		if ($this->_params['vend_id'] !== '') {
			$model->where($model->id_field, $this->_params['vend_id']);
		}
		if ($this->_params['type'] !== '') {
			if ($this->_params['type'] === 'dist') {
				$model->where('is_dist', 1);
			} elseif ($this->_params['type'] === 'manf') {
				$model->where('is_manf', 1);
			}
		}

		$results = $model->pagination($this->_pagination)->find_all()->as_array();

		$count = $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');
		$this->_pagination->setCount($count);

		return $results;

	}

}

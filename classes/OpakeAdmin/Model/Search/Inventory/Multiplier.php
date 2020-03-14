<?php

namespace OpakeAdmin\Model\Search\Inventory;

use Opake\Model\Search\AbstractSearch;

class Multiplier extends AbstractSearch
{
	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);
		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));

		$results = $model->pagination($this->_pagination)->find_all()->as_array();
		$count = $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');
		$this->_pagination->setCount($count);

		return $results;
	}
}

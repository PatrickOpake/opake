<?php

namespace OpakeAdmin\Model\Search\Billing;

use Opake\Model\Search\AbstractSearch;

class EOB extends AbstractSearch
{

	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$this->_params = [
			'insurer' => trim($request->get('insurer')),
			'charge' => trim($request->get('charge')),
		];

		$sort = $request->get('sort_by', 'id');
		$order = $request->get('sort_order', 'DESC');

		$query = $model->query;


		$query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `'. $model->table .'`.*'));

		if (!empty($this->_params['insurer'])) {
			$query->where($model->table. '.insurer_id', $this->_params['insurer']);
		}

		if (!empty($this->_params['charge'])) {
			$query->where($model->table. '.charge_master_id', $this->_params['charge']);
		}

		switch ($sort) {
			case 'id':
				$query->order_by($model->table . '.id', $order);
				break;
			case 'insurer':
				$query->join('insurance_payor', ['insurance_payor.id', $model->table . '.insurer_id']);
				$query->order_by('insurance_payor.name', $order);
				break;
			case 'cpt':
				$query->join('master_charge', ['master_charge.id', $model->table . '.charge_master_id']);
				$query->order_by('master_charge.cpt', $order);
				break;
			case 'charge_master_amount':
				$query->order_by($model->table . '.charge_master_amount', $order);
				break;
			case 'amount_reimbursed':
				$query->order_by($model->table . '.amount_reimbursed', $order);
				break;
		}

		if ($this->_pagination) {
			$model->pagination($this->_pagination);
		}

		$results = $model->find_all()->as_array();

		$count = $this->pixie->db
			->query('select')
			->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()
			->get('count');

		if ($this->_pagination) {
			$this->_pagination->setCount($count);
		}

		return $results;
	}
}
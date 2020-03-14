<?php

namespace OpakeAdmin\Model\Search\Insurance;

use Opake\Model\Search\AbstractSearch;

class Payors extends AbstractSearch
{

	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));

		$this->_params = [
			'name' => trim($request->get('name')),
			'phone' => trim($request->get('phone')),
			'address' => trim($request->get('address')),
		    'zip' => trim($request->get('zip'))
		];

		$sort = $request->get('sort_by', 'name');
		$order = $request->get('sort_order', 'ASC');

		if ($this->_params['name']) {
			$model->where(
				$model->table . '.name', 'like', '%' . $this->_params['name'] . '%'
			);
		}

		if ($this->_params['phone']) {
			$model->where(
				$model->table . '.phone', 'like', '%' . $this->_params['phone'] . '%'
			);
		}

		if ($this->_params['address']) {
			$model->where(
				$model->table . '.address', 'like', '%' . $this->_params['address'] . '%'
			);
		}

		if ($this->_params['zip']) {
			$model->where(
				$model->table . '.zip_code', 'like', '%' . $this->_params['zip_code'] . '%'
			);
		}

		switch ($sort) {
			case 'payor_id':
				$model->order_by($model->table . '.remote_payor_id', $order);
				break;
			case 'name':
				$model->order_by($model->table . '.name', $order);
				break;
			case 'insurance_type':
				$model->order_by($model->table . '.insurance_type', $order);
				break;
			case 'address1':
				$model->order_by($model->table . '.address', $order);
				break;
			case 'address2':
				$model->order_by($model->table . '.address2', $order);
				break;
			case 'city':
				$model->query->join('geo_city', [$model->table . '.city_id', 'geo_city.id']);
				$model->query->join('geo_state', [$model->table . '.state_id', 'geo_state.id']);
				$model->order_by('geo_city.name', $order)->order_by('geo_state.name', $order);
				break;
			case 'state':
				$model->query->join('geo_state', [$model->table . '.state_id', 'geo_state.id']);
				$model->order_by('geo_state.name', $order);
				break;
			case 'zip_code':
				$model->order_by($model->table . '.zip_code', $order);
				break;
			case 'phone':
				$model->order_by($model->table . '.phone', $order);
				break;
			case 'last_change_date':
				$model->order_by($model->table . '.last_change_date', $order);
				break;
			case 'last_change_user_name':
				$model->query->join('user', [$model->table . '.last_change_user_id', 'user.id']);
				$model->order_by('user.first_name', $order);
				break;
			case 'carrier_code':
				$model->order_by($model->table . '.carrier_code', $order);
				break;
			case 'ub04_payer_id':
				$model->order_by($model->table . '.ub04_payer_id', $order);
				break;
			case 'cms1500_payer_id':
				$model->order_by($model->table . '.cms1500_payer_id', $order);
				break;
		}

		if($this->_pagination) {
			$this->_pagination->setPage($request->get('p'));
			$this->_pagination->setLimit($request->get('l'));
			$model->pagination($this->_pagination);
		}

		$results = $model
			->find_all()
			->as_array();

		$count = $this->pixie->db->query('select')
			->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');

		if($this->_pagination) {
			$this->_pagination->setCount($count);
		}

		return $results;

	}

}

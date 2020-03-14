<?php

namespace OpakeAdmin\Model\Search;

use Opake\Model\Search\AbstractSearch;

class Insurance extends AbstractSearch
{

	public function search($model, $request)
	{

		$model = parent::prepare($model, $request);

		$this->_params = [
			'id' => trim($request->get('id')),
			'payor_name' => trim($request->get('payor_name')),
			'payor_group' => trim($request->get('payor_group')),
			'payor_id' => trim($request->get('payor_id')),
			'state' => trim($request->get('state')),
			'zip_code' => trim($request->get('zip_code')),
			'eligibility_id' => trim($request->get('eligibility_id')),
			'plan_type' => trim($request->get('plan_type')),
			'phone' => trim($request->get('phone')),
			'status_active' => trim($request->get('status_active')),
			'status_inactive' => trim($request->get('status_inactive')),
		];

		$sort = $request->get('sort_by', 'id');
		$order = $request->get('sort_order', 'ASC');

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));

		if ($this->_params['id'] !== '') {
			$model->where($model->table . '.id', 'like', '%' . $this->_params['id'] . '%');
		}
		if ($this->_params['payor_name'] !== '') {
			$model->where($model->table . '.payor_name', 'like', '%' . $this->_params['payor_name'] . '%');
		}
		if ($this->_params['payor_group'] !== '') {
			$model->where($model->table . '.payor_group', 'like', '%' . $this->_params['payor_group'] . '%');
		}
		if ($this->_params['payor_id'] !== '') {
			$model->where($model->table . '.payor_id', 'like', '%' . $this->_params['payor_id'] . '%');
		}
		if ($this->_params['state'] !== '') {
			$model->where($model->table . '.state_id', $this->_params['state']);
		}
		if ($this->_params['zip_code'] !== '') {
			$model->where($model->table . '.zip_code', 'like', '%' . $this->_params['zip_code'] . '%');
		}
		if ($this->_params['eligibility_id'] !== '') {
			$model->where($model->table . '.eligibility_id', 'like', '%' . $this->_params['eligibility_id'] . '%');
		}
		if ($this->_params['plan_type'] !== '') {
			$model->where($model->table . '.plan_type', $this->_params['plan_type']);
		}
		if ($this->_params['phone'] !== '') {
			$model->where($model->table . '.phone', 'like', '%' . $this->_params['phone'] . '%');
		}

		if ($this->_params['status_active'] === 'true' && $this->_params['status_inactive'] === 'false') {
			$model->where($model->table . '.status', 1);
		}
		if ($this->_params['status_inactive'] === 'true' && $this->_params['status_active'] === 'false') {
			$model->where($model->table . '.status', 0);
		}

		switch ($sort) {
			case 'id':
				$model->order_by($model->table . '.id', $order);
				break;
			case 'payor_name':
				$model->order_by($model->table . '.payor_name', $order);
				break;
			case 'payor_group':
				$model->order_by($model->table . '.payor_group', $order);
				break;
			case 'plan_type':
				$model->order_by($model->table . '.plan_type', $order);
				break;
			case 'address1':
				$model->order_by($model->table . '.address1', $order);
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
			case 'fax_number':
				$model->order_by($model->table . '.fax_number', $order);
				break;
			case 'payor_id':
				$model->order_by($model->table . '.payor_id', $order);
				break;
			case 'submitter_id':
				$model->order_by($model->table . '.submitter_id', $order);
				break;
			case 'eligibility_id':
				$model->order_by($model->table . '.eligibility_id', $order);
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

<?php

namespace OpakeAdmin\Model\Search\Order;

use Opake\Model\Search\AbstractSearch;

class Outgoing extends AbstractSearch {

	public function search($model, $request) {

		$model = parent::prepare($model, $request);

		$this->_params = [
		    'date_from' => trim($request->get('date_from')),
		    'date_to' => trim($request->get('date_to')),
		    'count_from' => trim($request->get('count_from')),
		    'count_to' => trim($request->get('count_to')),
		    'org' => trim($request->get('org')),
		    'vendor' => trim($request->get('vendor')),
		];

		$sort = $request->get('sort_by', 'date');
		$order = $request->get('sort_order', 'ASC');

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'), $this->pixie->db->expr('count(oi.id) as item_count'))
			->join(['order_outgoing_vendor', 'ov'], [$model->table . '.id', 'ov.order_id'])
			->join(['order_outgoing_item', 'oi'], ['oi.group_id', 'ov.id'])
			->group_by('order_outgoing.id');

		if ($this->_params['date_from'] !== '') {
			$model->where($this->pixie->db->expr('DATE(order_outgoing.date)'), '>=', $this->_params['date_from']);
		}
		if ($this->_params['date_to'] !== '') {
			$model->where($this->pixie->db->expr('DATE(order_outgoing.date)'), '<=', $this->_params['date_to']);
		}

		if ($this->_params['count_from'] !== '') {
			$model->query->having($this->pixie->db->expr('item_count'), '>=', $this->_params['count_from']);
		}
		if ($this->_params['count_to'] !== '') {
			$model->query->having($this->pixie->db->expr('item_count'), '<=', $this->_params['count_to']);
		}
		if ($this->_params['org'] !== '') {
			$model->query->join('organization', [$model->table . '.organization_id', 'organization.id']);
			$model->where('organization.name', 'like', '%' . $this->_params['org'] . '%');
		}
		if ($this->_params['vendor'] !== '') {
			$model->query->join('vendor', ['oi.vendor_id', 'vendor.id']);
			$model->where('vendor.name', 'like', '%' . $this->_params['vendor'] . '%');
		}

		switch ($sort) {
			case 'date': $model->order_by( $model->table.'.date', $order); break;
			case 'item_count':
				$model->order_by( $this->pixie->db->expr('item_count'), $order);
				$model->order_by( $model->table.'.date', $order);
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

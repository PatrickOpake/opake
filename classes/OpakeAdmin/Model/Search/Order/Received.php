<?php

namespace OpakeAdmin\Model\Search\Order;

use Opake\Model\Order;
use Opake\Model\Search\AbstractSearch;

class Received extends AbstractSearch {

	public function search($model, $request) {

		$model = parent::prepare($model, $request);

		$this->_params = [
		    'date_from' => trim($request->get('date_from')),
		    'date_to' => trim($request->get('date_to')),
		    'order_id' => trim($request->get('order_id')),
		    'po_id' => trim($request->get('po_id')),
		    'count_from' => trim($request->get('count_from')),
		    'count_to' => trim($request->get('count_to')),
		    'org' => trim($request->get('org')),
		    'type' => trim($request->get('type')),
		    'status' => trim($request->get('status')),
		    'vendor' => trim($request->get('vendor')),
		];

		$sort = $request->get('sort_by', 'date');
		$order = $request->get('sort_order', 'DESC');

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'), $this->pixie->db->expr('count(oi.id) as item_count'))
			->join(['order_item', 'oi'], [$model->table . '.id', 'oi.order_id'])
			->group_by('order.id');

		if ($this->_params['date_from'] !== '') {
			$model->where($this->pixie->db->expr('DATE(order.date)'), '>=', $this->_params['date_from']);
		}
		if ($this->_params['date_to'] !== '') {
			$model->where($this->pixie->db->expr('DATE(order.date)'), '<=', $this->_params['date_to']);
		}
		if ($this->_params['order_id'] !== '') {
			$model->where($model->id_field, $this->_params['order_id']);
		}
		if ($this->_params['po_id'] !== '') {
			$model->where($model->table.'.po_id', 'like', '%'. $this->_params['po_id'] . '%');
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
		if ($this->_params['type'] !== '') {
			if($this->_params['type'] === 'open_orders') {
				$model->where('status', 'IN', $this->pixie->db->expr("(".implode(',', [Order::STATUS_OPEN, Order::STATUS_INCOMPLETE]).")"));
			} else if($this->_params['type'] === 'partial_orders') {
				$model->where('status', 'IN', $this->pixie->db->expr("(".implode(',', [Order::STATUS_INCOMPLETE]).")"));
			} else if($this->_params['type'] === 'received_orders') {
				$model->where('status', 'IN', $this->pixie->db->expr("(".implode(',', [Order::STATUS_COMPLETE]).")"));
			}
		}
		if ($this->_params['status'] !== '') {
			$statuses = array_flip(Order::getStatuses());
			$model->where($model->table.'.status', $statuses[$this->_params['status']]);
		}

		if ($this->_params['vendor'] !== '' || $sort === 'vendor') {
			$model->query->join('vendor', [$model->table . '.vendor_id', 'vendor.id']);
		}

		if ($this->_params['vendor'] !== '') {
			$model->where('vendor.name', 'like', '%' . $this->_params['vendor'] . '%');
		}

		switch ($sort) {
			case 'date': 
				$model->order_by( $model->table.'.date', $order)
					->order_by( $model->table.'.id', $order);
				break;
			case 'id': $model->order_by( $model->table.'.id', $order); break;
			case 'vendor':
				$model->order_by('vendor.name', $order);
				$model->order_by( $model->table.'.id', $order);
				break;
			case 'po_id':
				$model->order_by( $model->table.'.po_id', $order);
				$model->order_by( $model->table.'.id', $order);
				break;
			case 'item_count':
				$model->order_by( $this->pixie->db->expr('item_count'), $order);
				$model->order_by( $model->table.'.id', $order);
				break;
			case 'org':
				$model->query->join('organization', [$model->table . '.organization_id', 'organization.id'])
					->order_by('organization.name', $order)
					->order_by($model->table . '.date', $order)
					->order_by( $model->table.'.id', $order);
				break;
		}

		$results = $model->pagination($this->_pagination)->find_all()->as_array();

		$count = $this->pixie->db
				->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
				->execute()->get('count');
		$this->_pagination->setCount($count);

		return $results;
	}

	public function searchItems($model, $request) {

		$model = parent::prepare($model, $request);

		$this->_params = [
			'order_id' => trim($request->get('order_id')),
			'name' => trim($request->get('name')),
			'desc' => trim($request->get('desc')),
			'exp_date' => trim($request->get('exp_date')),
			'ordered' => trim($request->get('ordered')),
		];

		$sort = $request->get('sort_by', 'name');
		$order = $request->get('sort_order', 'DESC');

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*, SUM(`inventory_pack`.`quantity`) as ordered'))
			->join('inventory', [$model->table.'.inventory_id', 'inventory.id'])
			->join('inventory_pack', [$model->table . '.id', 'inventory_pack.order_item_id'])
			->where($model->table . '.order_id', $this->_params['order_id'])
			->group_by($model->table . '.inventory_id');


		switch ($sort) {
			case 'name': $model->order_by( 'inventory.name', $order); break;
			case 'desc': $model->order_by( 'inventory.desc', $order); break;
			case 'exp_date': $model->order_by( $model->table . '.exp_date', $order); break;
			case 'ordered': $model->order_by($this->pixie->db->expr('ordered'), $order); break;
		}

		$results = $model->pagination($this->_pagination)->find_all()->as_array();

		$count = $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');
		$this->_pagination->setCount($count);

		return $results;
	}

}

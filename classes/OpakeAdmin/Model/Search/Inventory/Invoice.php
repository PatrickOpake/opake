<?php

namespace OpakeAdmin\Model\Search\Inventory;

use Opake\Model\Search\AbstractSearch;

class Invoice extends AbstractSearch
{

	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$this->_params = [
			'invoice' => trim($request->get('invoice')),
			'manufacturer' => trim($request->get('manufacturer')),
			'item' => trim($request->get('item'))
		];

		$sort = $request->get('sort_by', 'date');
		$order = $request->get('sort_order', 'DESC');

		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->group_by($model->table . '.id');

		if ($this->_params['invoice'] !== '') {
			$model->where($model->table . '.id', $this->_params['invoice']);
		}

		if ($this->_params['manufacturer'] !== '') {
			$model->query->join('inventory_invoice_manufacturer', [$model->table . '.id', 'inventory_invoice_manufacturer.invoice_id'])
				->where('inventory_invoice_manufacturer.vendor_id', $this->_params['manufacturer']);
		}

		if ($this->_params['item'] !== '') {
			$model->query->join('inventory_invoice_item', [$model->table . '.id', 'inventory_invoice_item.invoice_id'])
				->where('inventory_invoice_item.inventory_id', $this->_params['item']);
		}

		switch ($sort) {
			case 'name':
				$model->order_by($model->table . '.name', $order);
				break;
			case 'date':
				$model->order_by($model->table . '.date', $order);
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

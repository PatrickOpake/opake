<?php

namespace OpakeAdmin\Model\Search;

use Opake\Model\Alert\Alert;
use Opake\Model\Search\AbstractSearch;

class Inventory extends AbstractSearch
{
	/**
	 * @var int
	 */
	protected $organizationId = null;

	/**
	 * @return int
	 */
	public function getOrganizationId()
	{
		return $this->organizationId;
	}

	/**
	 * @param int $organizationId
	 */
	public function setOrganizationId($organizationId)
	{
		$this->organizationId = $organizationId;
	}

	public function search($model, $request)
	{

		$model = parent::prepare($model, $request);

		$this->_params = [
			'alert' => trim($request->get('alert')),
			'name' => trim($request->get('name')),
			'mmis' => trim($request->get('mmis')),
			'site' => trim($request->get('site')),
			'location' => trim($request->get('location')),
			'type' => trim($request->get('type')),
			'manf' => trim($request->get('manf')),
			'active' => filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN),
			'vendor' => trim($request->get('vendor')),
			'hcpcs' => trim($request->get('hcpcs')),
			'catalog_num' => trim($request->get('catalog_num')),
			'cdm' => trim($request->get('cdm')),
		    'date_created_from' => trim($request->get('date_created_from')),
		    'date_created_to' => trim($request->get('date_created_to')),
		    'item_number' => trim($request->get('item_number')),
		    'item_name' => trim($request->get('item_name')),
		    'complete_status' => trim($request->get('complete_status')),
		    'manf_id' => trim($request->get('manf_id'))
		];

		$defaultSort = 'name';
		$defaultSortOrder = 'ASC';
		if (!empty($this->_params['alert']) && $this->_params['alert'] == Alert::TYPE_NEW_ITEMS) {
			$defaultSort = 'date_created';
			$defaultSortOrder = 'DESC';
		}

		$sort = $request->get('sort_by', $defaultSort);
		$order = $request->get('sort_order', $defaultSortOrder);

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'), $this->pixie->db->expr('SUM(pack.quantity) as quantity'))
			->join(['inventory_pack', 'pack'], ['inventory.id', 'pack.inventory_id'])
			->join(['location_storage', 'loc'], ['pack.location_id', 'loc.id'])
			->join('site', ['loc.site_id', 'site.id'])
			->group_by('inventory.id');

		if ($this->_params['alert'] !== '') {
			$this->queryAlert($model, (int) $this->_params['alert']);
		}

		if ($this->_params['hcpcs'] !== '') {
			$model->where('hcpcs', $this->_params['hcpcs']);
		}

		if ($this->_params['cdm'] !== '') {
			$model->where('item_number', $this->_params['cdm']);
		}

		if ($this->_params['catalog_num'] !== '') {
			$model->where('and', [
				[$model->table . '.manufacturer_catalog', $this->_params['catalog_num']],
				['or', [$model->table . '.distributor_catalog', $this->_params['catalog_num']]]
			]);
		}

		if ($this->_params['name'] !== '') {
			$model->where('and', [
				[$model->table . '.name', 'like', '%' . $this->_params['name'] . '%'],
				['or', [$model->table . '.desc', 'like', '%' . $this->_params['name'] . '%']]
			]);
		}

		if ($this->_params['type'] !== '') {
			$model->where($model->table . '.type', 'like', '%' . $this->_params['type'] . '%');
		}

		if ($this->_params['manf'] !== '') {
			$model->query->join('vendor', [$model->table . '.manf_id', 'vendor.id']);
			$model->where('vendor.name', 'like', '%' . $this->_params['manf'] . '%');
		}

		if ($this->_params['manf_id'] !== '') {
			$model->where($model->table . '.manf_id', $this->_params['manf_id']);
		}

		if ($this->_params['mmis'] !== '') {
			$model->where($model->table . '.mmis', $this->_params['mmis']);
		}

		if ($this->_params['active']) {
			$model->where($model->table . '.status', 'active');
		}

		if ($this->_params['site'] !== '') {
			$model->where('site.name', 'like', '%' . $this->_params['site'] . '%');
		}

		if ($this->_params['location'] !== '') {
			$model->where('loc.name', 'like', '%' . $this->_params['location'] . '%');
		}

		if ($this->_params['vendor'] !== '') {
			$model->query->join('inventory_supply', [$model->table . '.id', 'inventory_supply.inventory_id']);
			$model->where('inventory_supply.vendor_id', $this->_params['vendor']);
		}

		if ($this->_params['date_created_from'] !== '') {
			$model->where('time_create', '>=', $this->_params['date_created_from'] . ' 00:00:00');
		}

		if ($this->_params['date_created_to'] !== '') {
			$model->where('time_create', '<=', $this->_params['date_created_to'] . ' 23:59:59');
		}

		if ($this->_params['item_number'] !== '') {
			$model->where([$model->table . '.item_number', 'like', '%' . $this->_params['item_number'] . '%']);
		}

		if ($this->_params['item_name'] !== '') {
			$model->where([$model->table . '.name', 'like', '%' . $this->_params['item_name'] . '%']);
		}

		if ($this->_params['complete_status'] !== '') {
			$model->where($model->table . '.complete_status', $this->_params['complete_status']);
		}

		switch ($sort) {
			case 'number':
				$model->order_by($model->table . '.item_number', $order);
				break;
			case 'name':
				$model->order_by($model->table . '.name', $order);
				break;
			case 'type':
				$model->order_by($model->table . '.type', $order)
					->order_by($model->table . '.name', $order);
				break;
			case 'manufacturer':
				$model->query->join('vendor', [$model->table . '.manf_id', 'vendor.id']);
				$model->order_by('vendor' . '.name', $order)
					->order_by($model->table . '.name', $order);
				break;
			case 'mmis':
				$model->order_by($model->table . '.mmis', $order);
				break;
			case 'min_level':
				$model->order_by($model->table . '.min_level', $order)
					->order_by($model->table . '.name', 'ASC');
				break;
			case 'quantity':
				$model->order_by($this->pixie->db->expr('quantity'), $order)
					->order_by($model->table . '.name', 'ASC');
				break;
			case 'date_created':
				$model->order_by($model->table . '.time_create', $order)
					->order_by($model->table . '.id', 'ASC');
				break;
			case 'complete_status':
				$model->order_by($model->table . '.complete_status', $order);
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

	public function searchCardItems($model, $request)
	{
		$model = parent::prepare($model, $request);

		$this->_params = [
			'selected_cases' => trim($request->get('selected_cases')),
			'inventory_type' => trim($request->get('inventory_type')),
			'inventory_manf' => trim($request->get('inventory_manf')),
			'inventory_desc' => trim($request->get('inventory_desc')),
			'inventory_id' => trim($request->get('inventory_id')),

		];

		$selectedCases = json_decode(trim($this->_params['selected_cases']), true);

		if(empty($selectedCases)) {
			$caseModel = $this->pixie->orm->get('Cases_Item')
				->where('organization_id', $this->getOrganizationId())
				->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED);

			$search = new \OpakeAdmin\Model\Search\Cases($this->pixie, false);
			$caseItems = $search->search($caseModel, $request);
			foreach ($caseItems as $item) {
				$selectedCases[] = $item->id();
			}
		}

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'), $this->pixie->db->expr('SUM(pack.quantity) as quantity'),
				$this->pixie->db->expr('SUM(card_staff_item.default_qty) as default_qty'), $this->pixie->db->expr('SUM(card_staff_item.actual_use) as actual_use'))
			->join(['inventory_pack', 'pack'], ['inventory.id', 'pack.inventory_id'])
			->join('card_staff_item', ['card_staff_item.inventory_id',  $model->table. '.id'])
			->join('card_staff', ['card_staff.id', 'card_staff_item.card_id'])
			->group_by('inventory.id');
		$model->where('card_staff.case_id', 'IN', $this->pixie->db->arr($selectedCases));

		if($this->_params['inventory_type'] !== '') {
			$model->where('inventory.type', 'like', '%' . $this->_params['inventory_type'] . '%');
		}

		if($this->_params['inventory_manf'] !== '') {
			$model->query->join('vendor', ['inventory.manf_id', 'vendor.id']);
			$model->where('vendor.name', 'like', '%' . $this->_params['inventory_manf'] . '%');
		}

		if($this->_params['inventory_desc'] !== '') {
			$model->where('inventory.desc', 'like', '%' . $this->_params['inventory_desc'] . '%');
		}

		if($this->_params['inventory_id'] !== '') {
			$model->where('inventory.id',  $this->_params['inventory_id']);
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

	public function getCountByAlert($main_model, $type)
	{
		$model = $this->pixie->orm->get('Inventory');
		$model->query = clone $main_model->query;
		$model->query
			->fields('inventory.id', 'inventory.min_level', $this->pixie->db->expr('SUM(pack.quantity) as quantity'))
			->join(['inventory_pack', 'pack'], ['inventory.id', 'pack.inventory_id'])
			->join(['location_storage', 'loc'], ['pack.location_id', 'loc.id'])
			->group_by('inventory.id');

		if ($type == Alert::TYPE_NEW_ITEMS) {
			$model->query->where($model->table . '.origin', \Opake\Model\Inventory::ORIGIN_CUSTOM_RECORD);
			$model->query->where($model->table . '.complete_status', \Opake\Model\Inventory::COMPLETE_STATUS_INCOMPLETE);
		} else {
			$model->query->where($model->table. '.origin', '!=',\Opake\Model\Inventory::ORIGIN_CUSTOM_RECORD);
			$model->query->join(['alert', 'a'], ['inventory.id', 'a.object_id']);
			$model->query->where('a.type', $this->pixie->db->expr($type));
		}

		return (int) $this->pixie->db
			->query('select')
			->fields($this->pixie->db->expr('COUNT(*) as count'))
			->table($model->query)
			->execute()
			->get('count');
	}

	public function getInventoryTypes($alert = null)
	{
		$model = $this->pixie->orm->get('Inventory_Type');
		$model->query->fields('inventory_type.*');
		$model->query->join('inventory', ['inventory.type', 'inventory_type.name'], 'inner');

		if ($alert) {
			if ($alert == Alert::TYPE_NEW_ITEMS) {
				$model->query->where('inventory.origin', \Opake\Model\Inventory::ORIGIN_CUSTOM_RECORD);
			} else {
				$model->query->where('inventory.origin', '!=',\Opake\Model\Inventory::ORIGIN_CUSTOM_RECORD);
				$model->query->join(['alert', 'a'], ['inventory.id', 'a.object_id']);
				$model->query->where('a.type', $this->pixie->db->expr($alert));
			}
		}

		$model->query->group_by('inventory_type.name');

		return $model->find_all();

	}

	public function getManufacturers($query = null, $alert = null, $organizationId = null)
	{
		$model = $this->pixie->orm->get('Vendor');
		$model->query->fields('vendor.*');
		$model->query->join('inventory', ['inventory.manf_id', 'vendor.id'], 'inner');

		if ($alert) {
			if ($alert == Alert::TYPE_NEW_ITEMS) {
				$model->query->where('inventory.origin', \Opake\Model\Inventory::ORIGIN_CUSTOM_RECORD);
			} else {
				$model->query->where('inventory.origin', '!=', \Opake\Model\Inventory::ORIGIN_CUSTOM_RECORD);
				$model->query->join(['alert', 'a'], ['inventory.id', 'a.object_id']);
				$model->query->where('a.type', $this->pixie->db->expr($alert));
			}
		}

		if ($query) {
			$model->query->where(['vendor.name', 'like', '%' . $query . '%']);
		}
		if ($organizationId) {
			$model->query->where('vendor.organization_id', $organizationId);
			$model->query->where('inventory.organization_id', $organizationId);
		}

		$model->query->group_by('vendor.id');
		$model->query->limit(10);

		return $model->find_all();
	}

	protected function queryAlert($model, $type)
	{
		if ($type == Alert::TYPE_NEW_ITEMS) {
			$model->query->where($model->table. '.origin', \Opake\Model\Inventory::ORIGIN_CUSTOM_RECORD);
		} else {
			$model->query->where($model->table. '.origin', '!=',\Opake\Model\Inventory::ORIGIN_CUSTOM_RECORD);
			$model->query->join(['alert', 'a'], ['inventory.id', 'a.object_id']);
			$model->query->where('a.type', $this->pixie->db->expr($type));
		}
	}
}

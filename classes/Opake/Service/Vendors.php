<?php

namespace Opake\Service;

use Opake\Model\Vendor;

class Vendors extends AbstractService
{

	protected $base_model = 'Vendor';

	public function getManufacturers($org_id)
	{
		return $this->getItem()->where('organization_id', $org_id)->where('is_manf', 1)->find_all();
	}

	public function getDistributors($org_id)
	{
		return $this->getItem()->where('organization_id', $org_id)->where('is_dist', 1)->find_all();
	}

	public function getProducts($vendor)
	{
		$model = $this->orm->get('Inventory');
		$model->query->fields('inventory.*')
			->order_by('name', 'asc');

		if ($vendor->is_manf) {
			$model->where('inventory.manf_id', $vendor->id);
		}
		if ($vendor->is_dist) {
			$sq = $this->db->query('select')
				->table('inventory_supply')
				->where('vendor_id', $vendor->id);

			$model->query->fields('inventory.*', 's.device_id')
				->join([$sq, 's'], ['inventory.id', 's.inventory_id']);

			if ($vendor->is_manf) {
				$model->where(['or', ['s.vendor_id', $vendor->id]]);
			} else {
				$model->where('s.vendor_id', $vendor->id);
			}
		}
		return $model;
	}

}

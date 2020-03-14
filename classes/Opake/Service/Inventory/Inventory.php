<?php

namespace Opake\Service\Inventory;

class Inventory extends \Opake\Service\AbstractService
{

	protected $base_model = 'Inventory';

	/**
	 * Инвентарь по id's
	 * @param Array $ids
	 * @return Opake\Model\Inventory[]
	 */
	public function getListByIds($ids)
	{
		return $this->orm->get('Inventory')
			->where('id', 'IN', $this->pixie->db->expr('(' . implode(', ', $ids) . ')'))
			->find_all();
	}

	/**
	 * Поиск инвентаря по коду
	 * @param String $code
	 * @return Opake\Model\AbstractOrm
	 */
	public function getItemByCode($org_id, $code)
	{
		$model = $this->orm->get('Inventory');
		$model->query->fields($model->table . '.*')
			->join(['inventory_code', 'code'], ['inventory.id', 'code.inventory_id'])
			->group_by('inventory.id');

		if ($org_id) {
			$model->where('organization_id', $org_id);
		}

		return $model->where([
			['barcode', $code],
			['or', [
				['code.code', $code],
				['and', ['code.type', \Opake\Model\Inventory\Code::TYPE_BARCODE]]
			]],
		])
			->find();
	}

	/**
	 * Удаление инвентаря
	 * @param Inventory $inventory
	 */
	public function delete($inventory)
	{
		$this->beginTransaction();

		$invTypes = [\Opake\Model\Alert\Alert::TYPE_EXPIRING, \Opake\Model\Alert\Alert::TYPE_LOW_INVENTORY];
		$alerts = $this->orm->get('Alert_Alert')
			->where('type', 'IN', $this->db->expr("('" . implode("','", $invTypes) . "')"))
			->where('object_id', $inventory->id);

		foreach ($alerts->find_all() as $alert) {
			$alert->delete();
		}

		$inventory->codes->delete_all();
		$inventory->packs->delete_all();
		$inventory->supplies->delete_all();
		$inventory->order_items->delete_all();
		$inventory->card_staff_items->delete_all();
		$inventory->pref_card_staff_items->delete_all();
		$inventory->delete();
		$this->commit();
	}

}

<?php

namespace OpakeAdmin\Events\Timer;

class Expiring extends \Opake\Events\AbstractListener
{

	public function dispatch($obj)
	{

		$model = $this->orm->get('Inventory');
		$model->query
			->fields('inventory.*')
			->join(['inventory_pack', 'pack'], ['inventory.id', 'pack.inventory_id'])
			->join(['alert', 'a'], [
				['inventory.id', 'a.object_id'],
				['and', ['a.type', $this->db->expr(\Opake\Model\Alert\Alert::TYPE_EXPIRING)]]
			])
			->where('a.id', 'IS', $this->db->expr('NULL'))
			->where('pack.exp_date', '<', strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime('+2 week')))
			->group_by('inventory.id');

		foreach ($model->find_all() as $item) {
			$this->pixie->events->fireEvent('update.expiring', $item);
		}
	}

}

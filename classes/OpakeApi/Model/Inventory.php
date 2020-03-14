<?php

namespace OpakeApi\Model;

use Opake\Model\Inventory as OpakeInventory;

class Inventory extends OpakeInventory
{
	use Api;

	public function fromArray($data)
	{
		return $this->apiFill([
			'name' => 'name',
			'manufacturerid' => 'manf_id',
			'description' => 'desc',
			'type' => 'type',
			'remanufacturable' => 'is_remanufacturable',
			'resterilizable' => 'is_resterilizable',
			'parlevel' => 'min_level',
			'mmisid' => 'mmis',
			'unitprice' => 'unit_price',
		], $data);
	}

	public function toArray()
	{

		$packs = [];
		foreach ($this->packs->find_all() as $pack) {
			$packs[] = $pack->toArray();
		}

		return [
			'id' => (int)$this->id,
			'type' => $this->type,
			'image' => $this->getImage(true),
			'name' => $this->name,
			'description' => $this->desc,
			'parlevel' => (int)$this->min_level,
			'remanufacturable' => (boolean)$this->is_remanufacturable,
			'resterilizable' => (boolean)$this->is_resterilizable,
			'itempacks' => $packs,
			'mmisid' => $this->mmis,
			'manufacturerid' => $this->manufacturer->id,
			'manufacturername' => $this->manufacturer->name,
			'unitprice' => $this->unit_price,
		];
	}

}

<?php

namespace OpakeAdmin\Controller\Inventory\Report;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionList()
	{
		$service = $this->services->get('inventory');
		$model = $service->getItem()->where('organization_id',$this->org->id());

		$search = new \OpakeAdmin\Model\Search\Inventory($this->pixie);
		$search->setOrganizationId($this->org->id());
		$results = $search->searchCardItems($model, $this->request);

		$items = [];
		foreach ($results as $item) {
			$items[] = $item->getFormatter('InventoryReportFormatter')->toArray();
		}
		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

}

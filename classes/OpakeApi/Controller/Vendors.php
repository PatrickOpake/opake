<?php

namespace OpakeApi\Controller;

class Vendors extends AbstractController
{

	public function actionVendorslist()
	{
		$service = $this->services->get('vendors');
		$type = $this->request->get('type');
		$org_id = $this->logged()->organization_id;

		if ($type === 'dist') {
			$items = $service->getDistributors($org_id);
		} elseif ($type === 'manf') {
			$items = $service->getManufacturers($org_id);
		} else {
			$items = $service->getList(null, $org_id);
		}

		$list = [];
		foreach ($items as $item) {
			$list[] = $item->toArray(false);
		}

		$this->result = ['vendors' => $list];
	}

	public function actionVendor()
	{
		$vendor = $this->loadModel('Vendor', 'id');
		$this->result = $vendor->toArray();
	}

}

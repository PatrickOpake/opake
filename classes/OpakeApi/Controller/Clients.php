<?php

namespace OpakeApi\Controller;

class Clients extends AbstractController
{

	public function actionList()
	{
		if (!$this->logged()->isInternal()) {
			throw new \OpakeApi\Exception\Forbidden();
		}
		$list = [];
		foreach ($this->services->get('Clients')->getList() as $item) {
			$list[] = $item->toArray();
		}

		$this->result = ['clients' => $list];
	}

	public function actionLocations()
	{
		$list = [];
		foreach ($this->logged()->organization->getLocations() as $item) {
			$list[] = $item->toArray();
		}

		$this->result = ['locations' => $list];
	}

	public function actionOrganizationProfile()
	{
		$model = $this->loadModel('Organization', 'id');
		$this->result = $model->toArray();
	}

}

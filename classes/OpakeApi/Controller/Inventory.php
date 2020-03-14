<?php

namespace OpakeApi\Controller;

class Inventory extends AbstractController
{

	public function actionProduct()
	{
		$id = $this->request->get('id');
		$code = $this->request->get('barcode');

		if (!$id && !$code) {
			throw new \OpakeApi\Exception\BadRequest('\'id\' and \'barcode\' expected');
		}

		$service = $this->services->get('Inventory');
		if ($id) {
			$item = $service->getItem($id);
		} else {
			$item = $service->getItemByCode($this->logged()->organization_id, $code);
		}

		if (!$item->loaded()) {
			throw new \OpakeApi\Exception\PageNotFound();
		}
		$this->result = $item->toArray();
	}

	public function actionSearch()
	{

		$results = $this->services->get('Inventory')->searchApi(
			$this->request->get('key'),
			$this->logged()->organization_id,
			filter_input(INPUT_GET, 'offset', FILTER_VALIDATE_INT),
			filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT)
		);

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->toArray();
		}

		$this->result = new \stdClass();
		$this->result->items = $items;
	}

	public function actionTypes()
	{
		$items = [];
		foreach ($this->services->get('Inventory')->getList('Inventory_Type') as $type) {
			$items[] = [
				'name' => $type->name
			];
		}
		$this->result = ['types' => $items];
	}

	public function actionUoms()
	{
		$items = [];
		foreach ($this->services->get('Inventory')->getList('Inventory_Uom') as $type) {
			$items[] = [
				'name' => $type->name
			];
		}
		$this->result = ['uoms' => $items];
	}

	// Тестовый анонимный доступ
	public function actionAnon()
	{
		$code = $this->request->get('barcode');

		if (!$code) {
			throw new \OpakeApi\Exception\BadRequest('\'barcode\' expected');
		}

		$service = $this->services->get('Inventory');
		$item = $service->getItemByCode(null, $code);

		if (!$item->loaded()) {
			throw new \OpakeApi\Exception\PageNotFound();
		}
		$this->result = $item->toArray();
	}

}

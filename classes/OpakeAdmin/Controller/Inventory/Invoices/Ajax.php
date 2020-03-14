<?php

namespace OpakeAdmin\Controller\Inventory\Invoices;

use Opake\Exception\Forbidden;
use Opake\Exception\InvalidMethod;
use Opake\Exception\PageNotFound;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionList()
	{
		$model = $this->pixie->orm->get('Inventory_Invoice');
		$model->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Inventory\Invoice($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $item) {
			$items[] = $item->toArray();
		}
		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionInvoice()
	{
		$this->result = $this->loadModel('Inventory_Invoice', 'subid')->toArray();
	}

	public function actionSearchInvoices()
	{
		$result = [];
		$model = $this->pixie->orm->get('Inventory_Invoice');
		$model->where('organization_id', $this->org->id);

		$query = $this->request->get('query');
		if ($query) {
			$model->where('name', 'like', '%' . $query . '%');
		}
		$model->limit(12);

		foreach ($model->find_all() as $item) {
			$result[] = [
				'id' => $item->id,
				'name' => $item->name
			];
		}
		$this->result = $result;
	}

	public function actionCreate()
	{
		try {
			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			$model = $this->pixie->orm->get('Inventory_Invoice');
			$model->organization_id = $this->org->id;

			$data = $this->getData(true);
			$data['files'] = $this->request->getFiles();

			$form = new \OpakeAdmin\Form\Inventory\InvoiceForm($this->pixie, $model);

			$form->load($data);
			if (!$form->isValid()) {
				$this->result = [
					'success' => false,
					'errors' => $form->getCommonErrorList()
				];
				return;
			}

			$form->save();

			$this->result = [
				'success' => true,
				'id' => (int) $model->id
			];
		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
		}
	}

	public function actionUpdate()
	{
		try {
			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			$model = $this->loadModel('Inventory_Invoice', 'subid');

			$data = $this->getData(true);

			$form = new \OpakeAdmin\Form\Inventory\InvoiceForm($this->pixie, $model);
			$form->setInlcudeUploadFile(false);

			$form->load($data);
			if (!$form->isValid()) {
				$this->result = [
					'success' => false,
					'errors' => $form->getCommonErrorList()
				];
				return;
			}

			$form->save();

			$this->result = [
				'success' => true,
				'id' => (int) $model->id
			];
		} catch (\Exception $e) {
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
		}
	}

	public function actionDelete()
	{
		if ($this->request->method !== 'POST') {
			throw new InvalidMethod('Invalid method');
		}

		$model = $this->pixie->orm->get('Inventory_Invoice', $this->request->param('subid'));
		if (!$model->loaded()) {
			throw new PageNotFound();
		}
		if ($model->organization_id != $this->org->id()) {
			throw new Forbidden();
		}

		$model->delete();

		$this->result = [
			'success' => true,
		];
	}

}

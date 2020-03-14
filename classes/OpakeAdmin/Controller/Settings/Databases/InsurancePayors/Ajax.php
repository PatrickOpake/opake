<?php

namespace OpakeAdmin\Controller\Settings\Databases\InsurancePayors;

use Opake\Exception\BadRequest;
use Opake\Exception\PageNotFound;
use OpakeAdmin\Form\Insurance\PayorForm;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}
	}

	public function actionIndex()
	{
		$payorModel = $this->orm->get('Insurance_Payor');
		$payorModel->where('actual', 1);

		$insuranceSearch = new \OpakeAdmin\Model\Search\Insurance\Payors($this->pixie);
		$results = $insuranceSearch->search($payorModel, $this->request);

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->getFormatter('PayorsList')
				->toArray();
		}

		$this->result = [
			'items' => $items,
			'totalCount' => $insuranceSearch->getPagination()->getCount()
		];
	}

	public function actionUploadInsurancesDB()
	{
		$files = $this->request->getFiles();
		if (empty($files['file'])) {
			throw new BadRequest('File is required');
		}

		$file = $files['file'];
		if ($file->isEmpty() || $file->hasErrors()) {
			throw new \Exception('Error while file uploading');
		}

		$type = $file->getType();

		$allowedTypes = \OpakeAdmin\Helper\Import\InsurancesDatabase::getAllowedMimeTypes();

		if (!in_array($type, $allowedTypes)) {
			$this->result = [
				'success' => false,
				'errors' => ['Uploaded file is not supported for import']
			];
			return;
		}

		$tmpFile = new \Opake\Helper\File\TemporaryFile($file);
		$tmpFile->create();

		try {
			$importer = new \OpakeAdmin\Helper\Import\InsurancesDatabase($this->pixie);
			$importer->load($tmpFile->getFilePath());
			$tmpFile->cleanup();
		} catch (\Exception $e) {
			$tmpFile->cleanup();
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
			return;
		}

		$this->result = [
			'success' => true
		];
	}


	function actionGetById()
	{
		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$payorId = $this->request->param('id');
		if (!$payorId) {
			throw new BadRequest();
		}

		$payor = $this->orm->get('Insurance_Payor', $payorId);
		if ($payor->loaded()) {
			$this->result = [
				'success' => true,
				'data' => $payor->getFormatter('PayorEdit')->toArray()
			];
		}
		else {
			throw new PageNotFound();
		}
	}


	function actionSave()
	{
		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$data = $this->getData();

		$payorId = isset($data->id) ? (int)$data->id : null;
		$payor = $this->orm->get('Insurance_Payor', $payorId);
		if ($payorId && !$payor->loaded()) {
			throw new BadRequest();
		}

		$payor->beginTransaction();
		try {
			$form = new PayorForm($this->pixie, $payor);
			$form->load($data);

			if ($form->isValid()) {
				$form->save();

				$payor->addresses->delete_all();

				$addresses = $form->getAddresses();
				foreach ($addresses as $address) {
					$addressModel = $this->orm->get('Insurance_Payor_Address', isset($address['id']) ? $address['id'] : null);
					$addressModel->payor_id = $payor->id;
					$this->updateModel($addressModel, $address);
				}

				$payor->commit();

				$this->result = [
					'success' => true,
					'id' => $payor->id(),
				];
			}
			else {
				$payor->rollback();
				$this->result = [
					'success' => false,
					'errors' => $form->getCommonErrorList()
				];
			}
			return;
		}
		catch (\Exception $e) {
			$this->logSystemError($e);
			$payor->rollback();

			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
			return;
		}
	}

	function actionDelete()
	{
		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$data = $this->getData();

		$payorId = $data->id;
		if (!$payorId) {
			throw new BadRequest();
		}

		$payor = $this->orm->get('Insurance_Payor', $payorId);
		if (!$payor->loaded()) {
			throw new PageNotFound();
		}

		$this->pixie->db->begin_transaction();
		try {
			$payor->actual = 0;
			$payor->save();
			$this->pixie->db->commit();
		}
		catch (\Exception $e) {
			$this->logSystemError($e);
			$this->pixie->db->rollback();
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
			return;
		}

		$this->result = [
			'success' => true,
		];
	}
}

<?php

namespace OpakeAdmin\Controller\Patients\Portal\UserDatabase;

use Opake\Exception\PageNotFound;
use Opake\Helper\StringHelper;
use OpakeAdmin\Model\Search\Patients\Users;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}
	}

	public function actionSearch()
	{
		$items = [];
		$model = $this->orm->get('Patient_User');

		$search = new Users($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $this->patientUserToArray($result);
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionGetUser()
	{
		$id = $this->request->param('id');
		$patientUser = $this->orm->get('Patient_User', $id);
		$this->result = $this->patientUserToArray($patientUser);
	}

	public function actionSave()
	{
		$data = $this->getData();

		$patientUserModel = $this->orm->get('Patient_User', isset($data->id) ? $data->id : null);
		if (!$patientUserModel->loaded()) {
			throw new PageNotFound('Patient user doesn\'t exist');
		}

		$patientModel = $patientUserModel->patient;

		$this->pixie->db->begin_transaction();

		try {

			if (!empty($data->patient)) {
				$patientModel->fill($data->patient);
			}

			$patientUserModel->fill([
				'active' => $data->active
			]);

			$this->checkValidationErrors($patientModel);
			$this->checkValidationErrors($patientUserModel);

			$patientModel->save();
			$patientUserModel->save();

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->pixie->db->rollback();
			throw new \Opake\Exception\Ajax($e->getMessage());
		}
		$this->pixie->db->commit();

		$this->result = ['id' => (int) $patientUserModel->id];
	}

	protected function patientUserToArray($result)
	{
		$item = $result->toArray();
		$item['patient'] = $result->patient->toArray();
		$item['full_name'] = $result->patient->getFullName();
		$item['email'] = $result->patient->getEmail();
		$item['organization_name'] = StringHelper::truncate($result->patient->organization->name, 150);

		return $item;
	}
}
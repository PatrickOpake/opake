<?php

namespace OpakeApi\Controller;

use OpakeApi\Model\Cases\OperativeReport;

class Save extends AbstractController
{

	protected $data;

	/**
	 * Возвращает данные запроса
	 * @return object|array
	 * @throws \OpakeApi\Exception\BadRequest
	 */
	public function getData()
	{
		$data = json_decode($this->request->post('data'));
		if (!$data) {
			throw new \OpakeApi\Exception\BadRequest('\'data\' expected or has incorrect format');
		}
		return $data;
	}

	/**
	 * Валидирует и либо обновляет модель, либо пишет ошибки в view
	 *
	 * @param \Opake\Model\AbstractOrm $model
	 * @param array $data
	 * @return boolean
	 */
	protected function updateModel($model, $data)
	{
		if ($data) {
			$model->fill($data);
		}
		$validator = $model->getValidator();
		if ($validator->valid()) {
			try {
				$model->save();
				return true;
			} catch (\Exception $e) {
				$this->logSystemError($e);
				throw $e;
			}
		} else {
			$errors_text = '';
			foreach ($validator->errors() as $field => $errors) {
				$errors_text .= implode('; ', $errors) . '; ';
			}
			throw new \Exception(trim($errors_text, '; '));
		}
	}

	public function actionProduct()
	{
		$this->checkAccess('inventory', 'edit');

		$service = $this->services->get('inventory');
		$data = $this->getData();

		if (isset($data->id)) {
			$model = $service->getItem($data->id);
			if (!$model->loaded() || $model->organization_id != $this->logged()->organization_id) {
				throw new \OpakeApi\Exception\PageNotFound();
			}
		} else {
			$model = $service->getItem();
			$model->organization_id = $this->logged()->organization_id;
		}

		$service->beginTransaction();
		try {
			$this->updateModel($model, $data);
			if (isset($data->itempacks)) {
				$service->updatePacks($model, $data->itempacks);
			}
		} catch (\Exception $e) {
			$this->logSystemError($e);
			$service->rollback();
			throw new \OpakeApi\Exception\BadRequest($e->getMessage());
		}
		$service->commit();
		$this->pixie->events->fireEvent('save.packs', $model);

		$this->result = ['id' => (int)$model->id];
	}

	public function actionOrder()
	{
		$service = $this->services->get('orders');

		$data = $this->getData();

		$model = $service->getItem();
		$model->organization_id = $this->logged()->organization_id;

		$service->beginTransaction();
		try {
			$this->updateModel($model, $data);
			$service->saveItems($model, $data->items);
		} catch (\Exception $e) {
			$this->logSystemError($e);
			$service->rollback();
			throw new \OpakeApi\Exception\BadRequest($e->getMessage());
		}
		$service->commit();
		$service->postProcessing();

		$this->result = ['id' => (int)$model->id];
	}

	public function actionMove()
	{
		$service = $this->services->get('inventory');
		$data = $this->getData();

		$service->beginTransaction();
		try {
			$ids = $service->moveItems($data->items);
		} catch (\Exception $e) {
			$this->logSystemError($e);
			$service->rollback();
			throw new \OpakeApi\Exception\BadRequest($e->getMessage());
		}
		$service->commit();

		$this->result = ['ids' => $ids];
	}

	public function actionReport()
	{
		$service = $this->pixie->services->get('cases_operativeReports');
		$data = $this->getData();

		if (isset($data->caseid)) {
			$case = $this->orm->get('Cases_Item', $data->caseid);
		}
		if(isset($data->reportid)) {
			$report = $this->orm->get('Cases_OperativeReport', $data->reportid);
			if (!$report->loaded()) {
				throw new \OpakeApi\Exception\PageNotFound();
			}
		}
		if (!isset($report) || !isset($case)) {
			throw new \OpakeApi\Exception\BadRequest('Case doesn\'t exist');
		}
		try {
			if ($data) {
				$report->fill($data);
			}
			$validator = $report->getValidator();
			if ($validator->valid()) {
				$report->updateDynamicVariables($case);
				foreach ($data->fields as $item) {
					if($item->field->field === 'staff') {
						foreach ($item->value as $surgeonField) {
							if($report->type == $surgeonField->field->field) {
								$report->is_active = $surgeonField->field->active;
								break 2;
							}
						}
					}

				}
				$report->is_exist_template = 1;
				$report->save();
			} else {
				$errors_text = '';
				foreach ($validator->errors() as $field => $errors) {
					$errors_text .= implode('; ', $errors) . '; ';
				}
				throw new \Exception(trim($errors_text, '; '));
			}

			$this->updateModel($case, $data);
			if(isset($data->fields)) {
				$service->saveReportTemplate($case->organization_id, $data, $case);
			}
		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \OpakeApi\Exception\BadRequest($e->getMessage());
		}
		$this->pixie->events->fireEvent('update.case', $case);

		$this->result = ['id' => (int)$report->id];
	}

}

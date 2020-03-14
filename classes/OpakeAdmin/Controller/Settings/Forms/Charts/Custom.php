<?php

namespace OpakeAdmin\Controller\Settings\Forms\Charts;

use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Role;

class Custom extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionForm()
	{
		$model = $this->loadModel('Forms_Document', 'subid');
		$this->result = $model->toCustomArray();
	}

	public function actionSave()
	{
		$data = $this->getData();

		$model = $this->orm->get('Forms_Document', isset($data->id) ? $data->id : null);

		$actionQueue = $this->pixie->activityLogger
			->newModelActionQueue($model);

		if ($model->loaded()) {
			$actionQueue->addAction(ActivityRecord::ACTION_CHART_EDIT_CHART);
		} else {
			$actionQueue->addAction(ActivityRecord::ACTION_CHART_CREATE_CHART);
		}

		$actionQueue->assign();

		try {
			$service = $this->services->get('Forms');
			$service->saveFormsDocument((array) $data, null, $this->org, $model);
			$actionQueue->registerActions();
		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$this->result = [
			'id' => (int) $model->id
		];
	}

	public function actionPreview()
	{
		$this->checkAccess('forms', 'view');
		if ($this->logged()->role_id == Role::FullAdmin || $this->logged()->isInternal()) {
			$data = $this->request->post();
			$view = $this->pixie->view('settings/forms/charts/export/preview');
			$view->doc = $data;
			$view->org = $this->org;

			list($pdf, $errors) = \Opake\Helper\Export::pdf($view->render(), null, ['landscape' => isset($data['is_landscape'])]);

			if ($errors) {
				throw new \Opake\Exception\Ajax('PDF generation failed: ' . $errors);
			} else {
				$this->response->file('application/pdf', 'chart_preview.pdf', $pdf, false);
				$this->execute = false;
			}
		} else {
			throw new \Opake\Exception\Forbidden('Can\'t access to page');
		}
	}

}

<?php

namespace OpakeAdmin\Controller\Settings\Alerts;


class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$site = $this->loadModel('Site', 'subid');
		$this->result = [
			'site' => $site->getFormatter('AlertSetting')->toArray(),
		];
	}

	public function actionSave()
	{
		$data = $this->getData();
		if ($data) {
			$model = $this->orm->get('Site_Alert', isset($data->id) ? $data->id : null);
			$model->fill($data);

			$model->beginTransaction();
			try {
				$this->updateModel($model, $data, true);
			} catch (\Exception $e) {
				$this->logSystemError($e);
				$model->rollback();
				throw new \Opake\Exception\Ajax($e->getMessage());
			}
			$model->commit();

			$this->result = ['id' => (int)$model->id];
		}
	}
}

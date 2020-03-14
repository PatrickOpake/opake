<?php

namespace OpakeAdmin\Controller\Settings\SmsTemplate;

use Opake\Model\Analytics\UserActivity\ActivityRecord;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$template = $this->orm->get('SmsTemplate')->where('organization_id', $this->org->id)->find();
		$this->result = $template->toArray();
	}

	public function actionSave()
	{
		$data = $this->getData();
		if ($data) {
			$model = $this->orm->get('SmsTemplate', isset($data->id) ? $data->id : null);
			$data->organization_id = $this->org->id;

			$model->beginTransaction();
			try {
				$queue = $this->pixie->activityLogger->newModelActionQueue($model);
				$queue->addAction(ActivityRecord::ACTION_SETTINGS_EDIT_SMS_TEMPLATE);
				$queue->assign();

				$this->updateModel($model, $data);

				$queue->registerActions();

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

<?php

namespace OpakeAdmin\Controller\Settings\Logs\Navicure;

use Opake\Exception\PageNotFound;
use OpakeAdmin\Controller\AuthPage;
use Opake\Exception\BadRequest;

class Navicure extends AuthPage
{

	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->view->initMenu('settings');
		$this->view->setActiveMenu('logs.navicure');
		$this->view->topMenuActive = 'settings';
		$this->view->setBreadcrumbs([
			'/settings/fields/' => 'Settings',
			'/settings/logs/navicure' => 'Logs',
			'' => 'Navicure'
		]);
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->subview = 'settings/logs/navicure/index';
	}

	public function actionViewContent()
	{
		$logRecordId = $this->request->param('id');
		$logRecord = $this->orm->get('Billing_Navicure_Log', $logRecordId);

		if (!$logRecord->loaded()) {
			throw new PageNotFound();
		}

		$text = explode("~", $logRecord->data);
		$text = implode("~\r\n", $text);

		$this->response->file(
			'text/plain',
			uniqid() . '.' . $logRecord->getTransactionName(),
			$text,
			false,
			false
		);

		$this->execute = false;
	}

}

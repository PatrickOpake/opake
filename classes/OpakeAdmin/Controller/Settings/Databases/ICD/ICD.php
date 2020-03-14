<?php

namespace OpakeAdmin\Controller\Settings\Databases\ICD;

use Opake\Helper\Pagination;
use OpakeAdmin\Controller\AuthPage;
use Opake\Exception\BadRequest;

class ICD extends AuthPage
{
	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->service = $this->services->get('settings');

		$this->view->initMenu('settings');
		$this->view->setActiveMenu('databases.icd');
		$this->view->topMenuActive = 'settings';
		$this->view->setBreadcrumbs([
			'/settings/fields/' => 'Settings',
			'/settings/databases/icd' => 'Databases',
			'' => 'ICDs'
		]);
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->subview = 'settings/databases/icd/index';
	}

	public function actionViewYear()
	{
		$this->view->year_id = $this->request->param('id');
		$this->view->subview = 'settings/databases/icd/view-year';
	}

	public function actionActivity()
	{
		if ($id = $this->request->param('id')) {
			$item = $this->orm->get('ICD', $id);
			if (!$item->loaded()) {
				$this->view->errors[] = 'Unknown item';
			}
			if (isset($item->active)) {
				$item->active = $item->active ? false : true;
				$item->save();
			} else {
				$item->delete();
			}
		}
		$this->redirect($_SERVER['HTTP_REFERER']);
	}
}

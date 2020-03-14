<?php

namespace OpakeAdmin\Controller\Settings\Databases\CPT;

use Opake\Helper\Pagination;
use OpakeAdmin\Controller\AuthPage;
use Opake\Exception\BadRequest;

class CPT extends AuthPage
{
	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->service = $this->services->get('settings');

		$this->view->initMenu('settings');
		$this->view->setActiveMenu('databases.cpt');
		$this->view->topMenuActive = 'settings';
		$this->view->setBreadcrumbs([
			'/settings/fields/' => 'Settings',
			'/settings/databases/cpt' => 'Databases',
			'' => 'CPTs'
		]);
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->subview = 'settings/databases/cpt/index';
	}

	public function actionViewYear()
	{
		$this->view->year_id = $this->request->param('id');
		$this->view->subview = 'settings/databases/cpt/view-year';
	}

	public function actionActivity()
	{
		if ($id = $this->request->param('id')) {
			$item = $this->orm->get('CPT', $id);
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
	
	/**
	 * Валидирует и либо обновляет модель, либо пишет ошибки в view
	 *
	 * @param \Opake\Model\AbstractOrm $model
	 * @param array $data
	 * @return boolean
	 */
	protected function updateModel($model, $data)
	{
		$model->fill($data);
		$validator = $model->getValidator();

		if ($validator->valid()) {
			try {
				$model->save();
				return true;
			} catch (\Exception $e) {
				$this->view->errors = [$e->getMessage()];
			}
		} else {
			$this->view->errors = [];
			foreach ($validator->errors() as $field => $errors) {
				$this->view->errors[$field] = implode(', ', $errors);
			}
		}
		return false;
	}
}

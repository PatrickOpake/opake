<?php

namespace OpakeAdmin\Controller\Settings;

use Opake\Helper\Pagination;

class Fields extends \OpakeAdmin\Controller\AuthPage
{

	protected $service;

	protected $model_name = '';

	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->view->initMenu('settings');

		$this->service = $this->services->get('settings');
		$action = $this->request->param('action');

		if ($action !== 'activity') {

			$this->view->setActiveMenu('fields.' . $action);
			$tabs = $this->view->getMenuConfig(1);

			$this->model_name = $tabs[$action]['model'];
			$this->view->model_name = $this->model_name;

			$this->view->setBreadcrumbs([
				'/settings/fields/' => 'Settings',
				'' => 'Editable Fields'
			]);
			$this->view->topMenuActive = 'settings';

			$this->view->set_template('inner');
		}
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

	protected function getModel($model)
	{
		$item = $this->orm->get($model);
		if ($this->request->method === 'POST') {
			if ($this->updateModel($item, $_POST)) {
				$this->view->setMessage('Item created');
				$item = $this->orm->get($model);
			}
		}
		return $item;
	}

	protected function actionActivity()
	{
		if (($id = $this->request->param('id')) && ($model = $this->request->get('model'))) {
			$item = $this->orm->get($model, $id);
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

	public function actionIndex()
	{
		$this->view->subview = 'settings/practice-groups/index';
	}

	public function actionDepartments()
	{
		$this->view->subview = 'settings/departments/index';
	}

	public function actionCpt()
	{
		$this->view->model = $this->getModel($this->model_name);
		$pages = new Pagination($this->service->getCount($this->model_name), $this->request->get('p'), $this->request->get('l'));
		$this->view->list = $this->service->getList($this->model_name, null, $pages);
		$this->view->pages = $pages;
		$this->view->subview = 'settings/fields/case';
	}

	public function actionItem()
	{
		$this->view->model = $this->getModel($this->model_name);
		$pages = new Pagination($this->service->getCount($this->model_name), $this->request->get('p'), $this->request->get('l'));
		$this->view->list = $this->service->getList($this->model_name, null, $pages);
		$this->view->pages = $pages;
		$this->view->subview = 'settings/fields/item_type';
	}

	public function actionShipping()
	{
		$this->view->model = $this->getModel($this->model_name);
		$pages = new Pagination($this->service->getCount($this->model_name), $this->request->get('p'), $this->request->get('l'));
		$this->view->list = $this->service->getList($this->model_name, null, $pages);
		$this->view->pages = $pages;
		$this->view->subview = 'settings/fields/index';
	}

}

<?php

namespace OpakeAdmin\Controller\Clients\Sites;

use Opake\Exception\BadRequest;
use Opake\Exception\Forbidden;
use Opake\Exception\InvalidMethod;
use Opake\Exception\PageNotFound;
use Opake\Helper\TimeFormat;
use Opake\Helper\UploadedFile\UploadedFileHelper;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use OpakeAdmin\Model\Search\Site as SiteSearch;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$items = [];
		$model = $this->orm->get('Site')->where('organization_id', $this->org->id);

		$search = new SiteSearch($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->getFormatter('List')->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionSite()
	{
		$model = $this->loadModel('Site', 'subid');
		$this->result = $model->getFormatter('Form')->toArray();
	}

	public function actionGetChargeable()
	{
		$model = $this->loadModel('Site', 'subid');

		$this->result = [
			'charge_price' => number_format($model->chargeable, 2, '.', '')
		];
	}

	public function actionSaveChargeable()
	{
		$model = $this->loadModel('Site', 'subid');
		$data = $this->getData();
		$model->updateChargeable($data->charge_price);

		$this->result = ['id' => (int) $model->id()];
	}

	public function actionDelete()
	{
		$model = $this->loadModel('Site', 'subid');
		$model->active = 0;

		$model->save();

		$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_SETTINGS_REMOVE_SITE)
			->setModel($model)
			->register();
	}

	public function actionSave()
	{
		if ($this->request->method !== 'POST') {
			throw new InvalidMethod('Method not allowed');
		}

		$data = $this->getData();

		$site = $this->pixie->orm->get('Site', (isset($data->id)) ? $data->id : null);

		if (isset($data->id) && !$site->loaded()) {
			throw new PageNotFound('Unknown site');
		}

		if (!$site->loaded()) {
			if (!$this->pixie->permissions->checkAccess('sites', 'create')) {
				throw new Forbidden();
			}
		} else {
			if (!$this->pixie->permissions->checkAccess('sites', 'edit', $site)) {
				throw new Forbidden();
			}
		}

		$data->country_id = null;
		if (!empty($data->country)) {
			$data->country_id = $data->country->id;
		}
		$data->city_id = null;
		if (!empty($data->city)) {
			$data->city_id = $data->city->id;
		}
		$data->state_id = null;
		if (!empty($data->state)) {
			$data->state_id = $data->state->id;
		}
		$data->pay_city_id = null;
		if (!empty($data->pay_city)) {
			$data->pay_city_id = $data->pay_city->id;
		}
		$data->pay_country_id = null;
		if (!empty($data->pay_country)) {
			$data->pay_country_id = $data->pay_country->id;
		}
		$data->pay_state_id = null;
		if (!empty($data->pay_state)) {
			$data->pay_state_id = $data->pay_state->id;
		}

		if (isset($data->department_ids)) {
			$data->departments = $data->department_ids;
		}

		if (isset($data->time_create)) {
			unset($data->time_create);
		}

		$site->fill($data);
		if (!$site->loaded()) {
			$site->time_create = TimeFormat::formatToDBDatetime(new \DateTime());
			$site->organization_id = $this->org->id();
		}

		$validator = $site->getValidator();

		$formErrors = [];
		if ($validator->valid()) {

			$queue = $this->pixie->activityLogger->newModelActionQueue($site);
			if (!$site->loaded()) {
				$queue->addAction(ActivityRecord::ACTION_SETTINGS_ADD_SITE);
			} else {
				$queue->addAction(ActivityRecord::ACTION_SETTINGS_EDIT_SITE);
			}
			$queue->assign();

			$site->save();

			$locations = [];
			if (!empty($data->room_names)) {
				foreach ($data->room_names as $name) {
					$loc = $this->orm->get('Location')->where('name', $name)->where('site_id', $site->id())->find();
					$loc->site_id = $site->id();
					$loc->name = $name;
					if ($loc->getValidator()->valid()) {
						$locations[] = $loc;
					}
				}
			}

			$location_storage = [];
			if (!empty($data->storage_names)) {
				foreach ($data->storage_names as $name) {
					$loc = $this->orm->get('Location_Storage')->where('name', $name)->where('site_id', $site->id())->find();
					$loc->site_id = $site->id();
					$loc->name = $name;
					if ($loc->getValidator()->valid()) {
						$location_storage[] = $loc;
					}
				}
			}

			$service = $this->services->get('Clients_Sites');
			$service->updateList($site->locations->find_all(), $locations);
			$service->updateList($site->storage->find_all(), $location_storage);

			$queue->registerActions();

		} else {
			$formErrors = [];
			foreach ($validator->errors() as $field => $errors) {
				$formErrors[] = implode(', ', $errors);
			}
		}

		if ($formErrors) {
			$this->result = ['errors' => $formErrors];
		} else {
			$this->result = ['id' => (int) $site->id()];
		}
	}
}
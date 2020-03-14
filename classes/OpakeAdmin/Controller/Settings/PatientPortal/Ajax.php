<?php

namespace OpakeAdmin\Controller\Settings\PatientPortal;

use Opake\Exception\BadRequest;
use PHPixie\Exception\PageNotFound;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionGetPortalSettings()
	{
		$model = $this->orm->get('Patient_Portal');
		$model->where('organization_id', $this->org->id());
		$model = $model->find();

		if (!$model->loaded()) {
			$model = $this->orm->get('Patient_Portal');
			$model->organization_id = $this->org->id();
			$model->title = $this->org->name;
			$model->active = 0;
			$model->alias = $this->prepareOrgAlias($this->org->name);
			$model->icon_file_id = $this->org->logo_id;
			$model->save();
		}

		$this->result = $model->toArray();
	}

	public function actionSave()
	{
		$data = $this->getData();
		if ($data) {
			$model = $this->orm->get('Patient_Portal');
			$model->where('organization_id', $this->org->id());
			$model = $model->find();

			if (!$model->loaded()) {
				throw new BadRequest('Unknown model');
			}

			$this->pixie->db->begin_transaction();
			try {

				$model->fill($data);
				$model->alias = $this->prepareOrgAlias($model->alias);

				$this->checkValidationErrors($model);
				$model->save();

			} catch (\Exception $e) {
				$this->logSystemError($e);
				$this->pixie->db->rollback();
				throw new \Opake\Exception\Ajax($e->getMessage());
			}

			$this->pixie->db->commit();

			$this->result = ['id' => (int) $model->id];
		}
	}

	public function actionPublishPortal()
	{
		$model = $this->orm->get('Patient_Portal');
		$model->where('organization_id', $this->org->id());
		$model = $model->find();
		if (!$model->loaded()) {
			throw new PageNotFound('Unknown portal model');
		}

		$model->active = 1;
		$model->save();

		$this->result = 'ok';
	}

	public function actionUnpublishPortal()
	{
		$model = $this->orm->get('Patient_Portal');
		$model->where('organization_id', $this->org->id());
		$model = $model->find();
		if (!$model->loaded()) {
			throw new PageNotFound('Unknown portal model');
		}

		$model->active = 0;
		$model->save();

		$this->result = 'ok';
	}

	protected function prepareOrgAlias($alias)
	{
		$alias = trim($alias);
		$alias = strtolower($alias);
		$alias = str_replace('/', '', $alias);
		$alias = str_replace(' ', '-', $alias);

		return $alias;
	}
}
<?php

namespace OpakeAdmin\Controller\Settings\CaseTypes;

use Opake\Exception\BadRequest;
use OpakeAdmin\Helper\Export\ProceduresExport;
use OpakeAdmin\Model\Search\Cases\CaseType as CaseTypeSearch;

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
		$model = $this->orm->get('Cases_Type')->where('organization_id', $this->org->id)->where('archived', '!=', 1);

		$search = new CaseTypeSearch($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionUpload()
	{
		$files = $this->request->getFiles();
		if (empty($files['file'])) {
			throw new BadRequest('File is required');
		}

		$file = $files['file'];
		if ($file->isEmpty() || $file->hasErrors()) {
			throw new \Exception('Error while file uploading');
		}

		$type = $file->getType();

		$allowedTypes = \OpakeAdmin\Helper\Import\ChargeMaster::getAllowedMimeTypes();

		if (!in_array($type, $allowedTypes)) {
			$this->result = [
				'success' => false,
				'errors' => ['Uploaded file is not supported for import']
			];
			return;
		}

		$tmpFile = new \Opake\Helper\File\TemporaryFile($file);
		$tmpFile->create();

		try {
			$importer = new \OpakeAdmin\Helper\Import\Procedures($this->pixie);
			$importer->setOrganizationId($this->org->id());
			$importer->load($tmpFile->getFilePath());
			$tmpFile->cleanup();
		} catch (\Exception $e) {
			$tmpFile->cleanup();
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
			return;
		}

		$this->result = [
			'success' => true
		];
	}

	public function actionSave()
	{
		$data = $this->getData();
		if ($data) {
			$codeQuery = $this->pixie->db->query('select')
				->table('cpt')
				->fields('id')
				->where('code', $data->code)
				->execute()
				->current();
			$data->cpt_id = $codeQuery ? $codeQuery->id : null;

			$model = $this->orm->get('Cases_Type', isset($data->id) ? $data->id : null);

			if (!$model->loaded()) {
				$model->organization_id = $this->org->id;
			} elseif ($model->organization_id !== $this->org->id) {
				throw new \Opake\Exception\Ajax('Case Type doesn\'t exist');
			}
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

	public function actionActivate()
	{
		$model = $this->loadModel('Cases_Type', 'subid');
		$model->active = 1;

		$model->save();
	}

	public function actionDeactivate()
	{
		$model = $this->loadModel('Cases_Type', 'subid');
		$model->active = 0;

		$model->save();
	}

	public function actionCaseType()
	{
		$model = $this->loadModel('Cases_Type', 'subid');
		$this->result = $model->toArray();
	}

	public function actionCaseTypes()
	{
		$types = [];
		$query = $this->request->get('query');
		$yearAdding = $this->request->get('year_adding');
		$usedCodesIds = explode(',', $this->request->get('used_codes_ids'));

		$case_types = $this->orm->get('Cases_Type')
			->where('and', [$this->pixie->db->expr("organization_id"), $this->org->id])
			->where('and', [$this->pixie->db->expr("active"), true])
			->where('and', [$this->pixie->db->expr("archived"), '!=', true]);

		if ($query !== null) {
			$case_types->where('and', [
				['or', [$this->pixie->db->expr("name"), 'like', '%' . $query . '%']],
				['or', [$this->pixie->db->expr("code"), 'like', '%' . $query . '%']]
			]);
		}

		if ($yearAdding) {
			if ($yearAdding == 2016) {
				$case_types->where('and', [$this->pixie->db->expr("is_2016"), 1]);
			} else if ($yearAdding == 2017) {
				$case_types->where('and', [$this->pixie->db->expr("is_2017"), 1]);
			}
		}

		if ($usedCodesIds and count($usedCodesIds)) {
			$case_types->where('id', 'NOT IN', $this->pixie->db->arr($usedCodesIds));
		}

		$case_types->order_by('name', 'asc')->limit(12);
		foreach ($case_types->find_all() as $type) {
			$types[] = $type->toArray();
		}

		$this->result = $types;
	}

	public function actionCaseTypesCount()
	{
		$case_types = $this->orm->get('Cases_Type')
			->where('and', [$this->pixie->db->expr("organization_id"), $this->org->id])
			->where('and', [$this->pixie->db->expr("active"), true]);

		$this->result = [
			'count' => $case_types->count_all()
		];
	}
}

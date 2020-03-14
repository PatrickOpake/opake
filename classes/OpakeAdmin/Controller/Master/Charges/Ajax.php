<?php

namespace OpakeAdmin\Controller\Master\Charges;

use Opake\Exception\BadRequest;
use Opake\Model\Analytics\UserActivity\ActivityRecord;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionList()
	{
		$this->checkAccess('sites', 'view');
		$siteId = $this->request->param('subid');
		$model = $this->pixie->orm->get('Master_Charge');

		$search = new \OpakeAdmin\Model\Search\Master\Charge($this->pixie);
		$search->setOrganizationId($this->org->id());
		$search->setSiteId($siteId);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $item) {
			$items[] = $item->toArray();
		}
		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionSave()
	{
		$service = $this->services->get('Inventory');
		$items = $this->getData();

		if ($items) {
			$cptModifierCombinations = [];
			$service->beginTransaction();
			foreach ($items as $key => $item) {
				$model = $this->orm->get('Master_Charge', isset($item->id) ? $item->id : null);
				if (!$model->loaded()) {
					$model->organization_id = $this->org->id;
				} elseif ($model->organization_id !== $this->org->id) {
					throw new \Opake\Exception\Ajax('Charge Master doesn\'t exist');
				}

				try {
					if ($item) {
						$model->fill($item);
					}
					if(isset($model->id) && $model->id && $model->isAllFieldsIsEmpty()) {
						$this->orm->get('Master_Charge', $item->id)->delete();
						continue;
					}

					$model->order = $key;
					$actionQueue = $this->pixie->activityLogger
						->newModelActionQueue($model)
						->addAction(ActivityRecord::ACTION_MASTER_CHARGE_SAVE_EDITED)
						->assign();

					$this->checkValidationErrors($model);

					foreach ($cptModifierCombinations as $combination) {
						if ($combination['cpt'] == $model->cpt) {
							if (
								(($combination['cpt_modifier1'] == $model->cpt_modifier1) && ($combination['cpt_modifier2'] == $model->cpt_modifier2))
								|| (($combination['cpt_modifier1'] == $model->cpt_modifier2) && ($combination['cpt_modifier2'] == $model->cpt_modifier1))
							) {
								throw new \Exception('Cpt and modifier combination could be used only once;');
							}
						}
					}

					$model->save();



					$actionQueue->registerActions();

					$cptModifierCombinations[] = [
						'cpt' => $model->cpt,
						'cpt_modifier1' => $model->cpt_modifier1,
						'cpt_modifier2' => $model->cpt_modifier2
					];

				} catch (\Exception $e) {
					$this->logSystemError($e);
					$service->rollback();
					throw new \Opake\Exception\Ajax($e->getMessage());
				}
			}
			$service->commit();

			$this->result = ['result' => 'ok'];
		}
	}

	public function actionSearchCPT()
	{
		$result = [];
		$q = $this->request->get('query');
		$siteId = $this->request->get('siteId');
		if(empty($siteId)) {
			$siteId = $this->logged()->getDefaultSite()->id();
		}
		$charge = $this->orm->get('Master_Charge')
			->where('organization_id', $this->org->id);
		if($siteId) {
			$charge->where('site_id', $siteId);
		}
		if ($q !== null) {
			$charge->where([
				['cpt', 'like', '%' . $q . '%']
			]);
		}
		$charge->query->group_by('cpt');
		$charge->order_by('cpt', 'asc')->limit(12);

		foreach ($charge->find_all() as $item) {
			$result[] = $item->toArray();
		}

		$this->result = $result;
	}

	public function actionUploadChargeMaster()
	{
		$this->checkAccess('sites', 'view');
		$siteId = $this->request->param('subid');
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
			$importer = new \OpakeAdmin\Helper\Import\ChargeMaster($this->pixie);
			$importer->setOrganizationId($this->org->id());
			$importer->setSiteId($siteId);
			$importer->load($tmpFile->getFilePath());

			$this->logUploadToAudit($tmpFile, $siteId);

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

	protected function logUploadToAudit($tmpFile, $siteId)
	{
		$uploadedFile = $this->pixie->orm->get('UploadedFile');
		$uploadedFile->storeLocalFile($tmpFile->getFilePath());
		$uploadedFile->save();
		$this->pixie->activityLogger
			->newModelActionQueue($uploadedFile)
			->addAction(ActivityRecord::ACTION_MASTER_CHARGE_UPLOAD)
			->setAdditionalInfo('site_id', $siteId)
			->assign()
			->registerActions();
	}
}

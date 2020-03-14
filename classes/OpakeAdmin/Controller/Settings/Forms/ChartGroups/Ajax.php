<?php

namespace OpakeAdmin\Controller\Settings\Forms\ChartGroups;

use Opake\Exception\BadRequest;
use Opake\Exception\PageNotFound;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use OpakeAdmin\Form\Charts\ChartGroupForm;
use OpakeAdmin\Helper\Printing\PrintCompiler;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$this->checkAccess('forms', 'view');

		$model = $this->orm->get('Forms_ChartGroup')
			->where('organization_id', $this->org->id());

		$search = new \OpakeAdmin\Model\Search\Forms\ChartGroup($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionSave()
	{
		$data = $this->getData(true);
		$model = $this->orm->get('Forms_ChartGroup', (!empty($data['id'])) ? $data['id'] : null);


		if (!empty($data->id) && !$model->loaded()) {
			throw new PageNotFound();
		}

		$model->organization_id = (int) $this->org->id();

		$form =  new ChartGroupForm($this->pixie, $model);
		$form->load($data);


		if ($form->isValid()) {

			$actionQueue = $this->pixie->activityLogger
				->newModelActionQueue($model);

			if (!$model->loaded()) {
				$actionQueue->addAction(ActivityRecord::ACTION_CHART_GROUP_CREATE);
			} else {
				$actionQueue->addAction(ActivityRecord::ACTION_CHART_GROUP_EDIT);
			}

			$actionQueue->assign();

			$form->save();

			if (isset($data['document_ids'])) {
				$model->updateDocuments($data['document_ids']);
			}

			$actionQueue->registerActions();

			$this->result = [
				'success' => true,
				'id' => (int) $model->id()
			];

		} else {

			$this->result = [
				'success' => false,
				'errors' => $form->getCommonErrorList()
			];

		}
 	}

	public function actionDocuments()
	{
		$model = $this->orm->get('Forms_Document')
			->where('organization_id', $this->org->id());

		$this->result = [];

		foreach ($model->find_all() as $item) {
			$this->result[] = [
				'id' => (int) $item->id(),
				'name' => $item->name
			];
		}
	}

	public function actionDelete()
	{
		$id = $this->request->post('id');

		if (!$id) {
			throw new BadRequest('Bad Request');
		}

		$model = $this->orm->get('Forms_ChartGroup')
			->where('organization_id', $this->org->id())
			->where('id', $id)
			->find();

		if (!$model->loaded()) {
			throw new PageNotFound('Chart group is not found');
		}

		$model->delete();

		$this->result = [
			'success' => true
		];

		$this->pixie->activityLogger
			->newModelActionQueue($model)
			->addAction(ActivityRecord::ACTION_CHART_GROUP_REMOVE)
			->assign()
			->registerActions();
	}

	public function actionCompileChartGroup()
	{
		try {

			$chartGroupId = $this->request->post('id');

			if (!$chartGroupId) {
				throw new BadRequest('Bad Request');
			}

			$model = $this->orm->get('Forms_ChartGroup')
				->where('organization_id', $this->org->id())
				->where('id', $chartGroupId)
				->find();


			if (!$model->loaded()) {
				throw new PageNotFound('Chart group is not found');
			}

			$documentsToPrint = [];

			foreach ($model->getDocuments() as $document) {
				$documentsToPrint[] = \OpakeAdmin\Helper\Printing\Document\Cases\Chart::createDocument($document);
			}

			$helper = new PrintCompiler();
			$printResult = $helper->compile($documentsToPrint);

			$this->result = [
				'success' => true,
				'url' => $printResult->getResultUrl(),
				'print' => $printResult->isReadyToPrint()
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}
}
<?php

namespace OpakeAdmin\Controller\Settings\Forms\Charts;

use Opake\Exception\BadRequest;
use Opake\Exception\Forbidden;
use Opake\Exception\InvalidMethod;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use OpakeAdmin\Controller\File\Ajax as FileAjax;
use OpakeAdmin\Helper\PDF\PreviewImageGenerator;

class Ajax extends FileAjax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionUpload()
	{

		try {

			$service = $this->services->get('forms');

			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			/** @var \Opake\Request $req */
			$req = $this->request;

			$postData = $req->post();
			if (!$postData) {
				$postData = [];
			}

			$postData['files'] = $req->getFiles();

			$document = $this->orm->get('Forms_Document', ((!empty($postData['id'])) ? $postData['id'] : null));

			$form = new \OpakeAdmin\Form\Charts\ChartFileUploadForm($this->pixie);

			$form->load($postData);
			if (!$form->isValid()) {
				$this->result = [
					'success' => false,
					'errors' => $form->getCommonErrorList()
				];

				return;
			}

			$uploadedFile = $service->uploadFile($req);

			if ($document->loaded()) {
				$documentId = $document->id;
				$actionQueue = $this->pixie->activityLogger->newModelActionQueue($document);
				$actionQueue->addAction(ActivityRecord::ACTION_CHART_REUPLOAD_CHART)
					->assign();

				$previewImageGenerator = new PreviewImageGenerator($document->file);
				$previewImageGenerator->clearImagesCache();

				$service->reuploadDocument($postData, $uploadedFile, $document);

				$document->dynamic_fields->delete_all();

				$actionQueue->registerActions();
			} else {
				$model = $service->saveFormsDocument($postData, $uploadedFile, $this->org);
				$documentId = $model->id;

				$actionQueue = $this->pixie->activityLogger->newModelActionQueue($model);
				$actionQueue->addAction(ActivityRecord::ACTION_CHART_UPLOAD_CHART)
					->assign()
					->registerActions();
			}

			$this->result = [
				'success' => true,
				'id' => $documentId
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
		}
	}

	public function actionIndex()
	{
		$items = [];
		$caseid =  $this->request->get('caseId');

		$model = $this->orm->get('Forms_Document')->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Forms\Document($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$document = $result->getFormatter('SettingsList')->toArray();
			if (empty($document['file']['id'])) {
				$document['url'] = '/file/genpdf?id=' . $document['id'] . '&caseid=' . $caseid;
			}

			$items[] = $document;
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionDelete()
	{
		$service = $this->services->get('forms');
		$doc = $this->loadModel('Forms_Document', 'subid');
		if ($doc->organization_id !== $this->org->id) {
			throw new \Opake\Exception\Ajax('Document doesn\'t exist');
		}
		$service->delete($doc);
		$this->result = [
			'success' => true
		];

		$this->pixie->activityLogger
			->newModelActionQueue($doc)
			->addAction(ActivityRecord::ACTION_CHART_REMOVE_CHART)
			->assign()->registerActions();
	}

	public function actionUpdate()
	{
		try {
			$service = $this->services->get('forms');

			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}
			$action = $this->request->post('action');

			$data = $this->getData();

			if ($data->organization_id != $this->org->id || !$data->id) {
				throw new \Opake\Exception\Ajax('Document doesn\'t exist');
			}

			if ($action === 'move') {
				$service->moveDocument($data);
			} else if ($action === 'rename') {

				$form = new \OpakeAdmin\Form\Charts\ChartRenameForm($this->pixie);
				$form->load($data);
				if (!$form->isValid()) {
					$this->result = [
						'success' => false,
						'errors' => $form->getCommonErrorList()
					];

					return;
				}

				$service->renameDocument($data);
			} else if ($action === 'assign') {
				$service->assignDocument($data, $this->org);
			}

			$this->result = [
				'success' => true
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
		}
	}

	public function actionExportDocument()
	{
		$doc = $this->loadModel('Forms_Document', 'subid');
		$caseid =  $this->request->get('caseid');
		$case = null;
		if ($caseid) {
			$case = $this->orm->get('Cases_Item', $caseid);
			if (!$case->loaded()) {
				throw new \Opake\Exception\PageNotFound();
			}
		}
		
		if ($doc->organization_id != $this->org->id()) {
			throw new Forbidden();
		}

		$documentToPrint = \OpakeAdmin\Helper\Printing\Document\Cases\Chart::createDocument($doc);
		$helper = new \OpakeAdmin\Helper\Printing\PrintCompiler();
		$printResult = $helper->compile([$documentToPrint]);

		$file = $printResult->file;
		$mimeType = $file->mime_type;

		$fileName = $doc->getFileNameForExport();

		$this->response->file($mimeType, $fileName, $file->readContent());

		$this->execute = false;
	}

	public function actionCompileCharts()
	{
		try {

			$documentIds = $this->request->post('documents');

			if (!$documentIds) {
				throw new BadRequest('Bad Request');
			}

			$models = $this->pixie->orm->get('Forms_Document')
				->where('organization_id', $this->org->id())
				->where('id', 'IN', $this->pixie->db->arr($documentIds))
				->order_by('name')
				->find_all();

			$documentsToPrint = [];
			foreach ($models as $model) {
				$documentsToPrint[] = \OpakeAdmin\Helper\Printing\Document\Cases\Chart::createDocument($model);
			}


			$helper = new \OpakeAdmin\Helper\Printing\PrintCompiler();
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

	public function actionAllChartGroups()
	{
		$model = $this->orm->get('Forms_ChartGroup')
			->where('organization_id', $this->org->id());

		$this->result = [];

		foreach ($model->find_all() as $item) {
			$this->result[] = [
				'id' => (int) $item->id(),
				'name' => $item->name
			];
		}
	}

}
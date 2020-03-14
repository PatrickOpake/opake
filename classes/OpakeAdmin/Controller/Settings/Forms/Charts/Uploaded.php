<?php

namespace OpakeAdmin\Controller\Settings\Forms\Charts;

use Opake\Exception\BadRequest;
use Opake\Exception\Forbidden;
use OpakeAdmin\Form\Charts\ChartUploadedForm;
use OpakeAdmin\Helper\Chart\PDF\DynamicFieldsWriter;
use OpakeAdmin\Helper\Printing\Utils\Chart\ChartTemporaryFile;
use OpakeAdmin\Helper\Printing\Utils\Chart\HeadersWriter;

class Uploaded extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionForm()
	{
		$model = $this->loadModel('Forms_Document', 'subid');
		if ($model->organization_id != $this->org->id()) {
			throw new Forbidden();
		}
		$this->result = $model->getFormatter('UploadedForm')->toArray();
	}

	public function actionSave()
	{
		$model = $this->loadModel('Forms_Document', 'subid');
		if ($model->organization_id != $this->org->id()) {
			throw new Forbidden();
		}

		$data = $this->getData(true);

		$form = new ChartUploadedForm($this->pixie, $model);
		$form->load($data);

		if ($form->isValid()) {
			$form->save();

			if ($model->file->isPDF()) {
				$this->updateDynamicFields($model, $data);
			}

			$this->result = [
				'success' => true
			];
		} else {
			$this->result = [
				'success' => false,
				'errors' => $form->getCommonErrorList()
			];
		}
	}

	protected function updateDynamicFields($model, $data)
	{
		$dynamicFields = [];
		foreach ($model->dynamic_fields->find_all() as $field) {
			$dynamicFields[$field->id] = $field;
		}

		if (!empty($data['dynamic_fields'])) {
			foreach ($data['dynamic_fields'] as $fieldData) {
				if (!empty($fieldData['id']) && isset($dynamicFields[$fieldData['id']])) {
					$field = $dynamicFields[$fieldData['id']];
					unset($dynamicFields[$fieldData['id']]);
				} else {
					$field = $this->pixie->orm->get('Forms_PDF_DynamicField');
					$field->doc_id = $model->id;
					$field->name = $fieldData['key'];
				}
				$field->page = $fieldData['page'];
				$field->x = $fieldData['x'];
				$field->y = $fieldData['y'];
				$field->width = $fieldData['width'];
				$field->height = $fieldData['height'];
				$field->save();
			}
		}

		foreach ($dynamicFields as $field) {
			$field->delete();
		}
	}

	public function actionPreview()
	{
		$model = $this->loadModel('Forms_Document', 'subid');
		if ($model->organization_id != $this->org->id()) {
			throw new Forbidden();
		}

		if (!$model->file->isPDF()) {
			throw new BadRequest('Not allowed for non pdf');
		}

		$data = $this->getData(true);

		$form = new ChartUploadedForm($this->pixie, $model);
		$form->load($data);
		$form->fillModel();

		$tmpFile = new ChartTemporaryFile($model);
		$tmpFile->createFile();

		if (!empty($data['dynamic_fields'])) {
			$variables = [];
			foreach ($data['dynamic_fields'] as $fieldData) {
				$variables[$fieldData['page']][] = [
					$fieldData['key'],
					$fieldData['x'],
					$fieldData['y'],
					$fieldData['width'],
					$fieldData['height']
				];
			}

			$writer = new DynamicFieldsWriter($tmpFile->getFilePath(), $variables);
			$writer->setPreviewOnly(true);
			$writer->writeFields();
		}

		if ($model->include_header) {
			$headersWriter = new HeadersWriter($tmpFile->getFilePath());
			$headersWriter->setOrganization($this->org);
			$headersWriter->writeHeaders();
		}

		$result = $tmpFile->readContent();
		$tmpFile->cleanup();

		$this->response->file('application/pdf', 'chart_preview.pdf', $result, false);
		$this->execute = false;
	}

}

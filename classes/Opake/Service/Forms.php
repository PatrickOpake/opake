<?php

namespace Opake\Service;

use Opake\Exception\BadRequest;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Forms\Document;

class Forms extends AbstractService
{

	public function saveFormsDocument($data, $uploadedFile, $org, $model = null)
	{
		if (!$model) {
			$model = $this->orm->get('Forms_Document');
		}
		$model->organization_id = $org->id;
		$model->segment = $data['segment'];
		$model->name = $data['name'];
		$model->type = Document::TYPE_OTHER;
		$model->include_header = 0;
		if (isset($data['include_header'])) {
			$model->include_header = filter_var($data['include_header'], FILTER_VALIDATE_BOOLEAN);
		}

		if (!empty($uploadedFile)) {
			$model->uploaded_file_id = $uploadedFile->id();
		} else {
			$model->own_text = $data['own_text'];
			if (isset($data['is_landscape'])) {
				$model->is_landscape = (bool) $data['is_landscape'];
			}
		}

		$validator = $model->getValidator();
		if ($validator->valid()) {

			if ($model->segment == \Opake\Model\Forms\Document::SEGMENT_INTAKE) {
				if ($model->doc_type_id && $model->doc_type->loaded()) {
					$typeModel = $model->doc_type;
					$typeModel->name = $model->name;
					$typeModel->save();
				} else {
					$typeModel = $this->orm->get('Cases_Registration_Document_Type');
					$typeModel->organization_id = $org->id;
					$typeModel->name = $model->name;
					$typeModel->is_required = false;
					$typeModel->save();

					$model->doc_type_id = $typeModel->id;
				}
			}

			$model->save();

			return $model;

		} else {
			$errors_text = '';
			foreach ($validator->errors() as $field => $errors) {
				$errors_text .= implode('; ', $errors) . '; ';
			}
			$error = trim($errors_text, '; ');

			throw new \Exception($error);
		}
	}

	public function moveDocument($data)
	{
		$model = $this->orm->get('Forms_Document', $data->id);
		if ($model->segment !== $data->segment) {
			$model->segment = $data->segment;
			if ($model->type !== Document::TYPE_OTHER) {
				$model->type = Document::TYPE_OTHER;
			}

			$actionQueue = $this->pixie->activityLogger
				->newModelActionQueue($model)
				->addAction(ActivityRecord::ACTION_CHART_MOVE_CHART)
				->assign();

			$model->save();

			$actionQueue->registerActions();
		}
	}

	public function renameDocument($data)
	{
		$model = $this->orm->get('Forms_Document', $data->id);
		if ($model->name !== $data->name) {
			$model->name = $data->name;
			if ($model->doc_type_id) {
				$typeModel = $this->orm->get('Cases_Registration_Document_Type', $model->doc_type_id);
				if ($typeModel->loaded()) {
					$typeModel->name = $model->name;
					$typeModel->save();
				}
			}

			$actionQueue = $this->pixie->activityLogger
				->newModelActionQueue($model)
				->addAction(ActivityRecord::ACTION_CHART_RENAME_CHART)
				->assign();

			$model->save();

			$actionQueue->registerActions();
		}
	}

	public function assignDocument($data, $org)
	{
		$model = $this->orm->get('Forms_Document', $data->id);

		$actionQueue = $this->pixie->activityLogger
			->newModelActionQueue($model)
			->addAction(ActivityRecord::ACTION_CHART_ASSIGN_CHART)
			->assign();

		$this->removeSites($model->id);

		if ($data->sites) {
			if ($model->is_all_sites) {
				$model->is_all_sites = false;
			}

			foreach ($data->sites as $site) {
				if (is_object($site)) {
					$site_id = $site->id;
				} else {
					$site_id = $site;
				}

				$this->addSiteToDoc($model->id, $site_id);
			}

		} else {
			$model->is_all_sites = false;
		}

		$formChartGroups = $model->getChartGroups();
		$documentChartGroupIds = $data->chart_group_ids;
		$formChartGroupsById = [];
		foreach ($formChartGroups as $chartGroup) {
			$formChartGroupsById[$chartGroup->id()] = $chartGroup;
		}

		foreach ($documentChartGroupIds as $chartGroupId) {
			if (!isset($formChartGroupsById[$chartGroupId])) {
				$chartGroup = $this->orm->get('Forms_ChartGroup', $chartGroupId);
				$usedDocumentIds = $chartGroup->getDocumentIds();
				$usedDocumentIds[] = $model->id();
				$chartGroup->updateDocuments($usedDocumentIds);
			}
		}

		foreach ($formChartGroups as $chartGroup) {
			if (!in_array($chartGroup->id(), $documentChartGroupIds)) {
				$usedDocumentIds = $chartGroup->getDocumentIds();
				$index = array_search($model->id(), $usedDocumentIds);
				if ($index !== false) {
					unset($usedDocumentIds[$index]);
					$chartGroup->updateDocuments($usedDocumentIds);
				}
			}
		}

		$model->save();

		$actionQueue->registerActions();

		/*if ($model->doc_type_id) {
			$docModels = $this->orm->get('Cases_Registration');
			foreach ($docModels->find_all() as $docModel) {
				if ($docModel->loaded()) {
					$docModel->updateForms();
				}
			}
		}*/
	}

	public function reuploadDocument($data, $uploadedFile, $document)
	{
		$document->name = $data['name'];
		if (isset($data['include_header'])) {
			$document->include_header = filter_var($data['include_header'], FILTER_VALIDATE_BOOLEAN);
		}
		if ($document->uploaded_file_id && $document->file->loaded()) {
			$document->file->removeFile();
			$document->file->delete();
		}
		if (!empty($uploadedFile)) {
			$document->uploaded_file_id = $uploadedFile->id();
		}
		$document->save();
	}

	public function uploadFile($req)
	{
		$files = $req->getFiles();

		if (empty($files['uploadedFile'])) {
			throw new BadRequest('Empty file');
		}
		/** @var \Opake\Request\RequestUploadedFile $upload */
		$upload = $files['uploadedFile'];

		if ($upload->isEmpty()) {
			throw new BadRequest('Empty file');
		}

		if ($upload->hasErrors()) {
			throw new \Exception('An error occurred while file loading');
		}

		/** @var \Opake\Model\UploadedFile $uploadedFile */
		$uploadedFile = $this->pixie->orm->get('UploadedFile');
		$uploadedFile->storeFile($upload, [
			'is_protected' => true,
			'protected_type' => 'forms'
		]);
		$uploadedFile->save();

		return $uploadedFile;
	}

	/**
	 * @param \Opake\Model\Forms\Document $doc
	 */
	public function delete($doc)
	{
		if ($doc->uploaded_file_id) {
			$doc->file->removeFile();
			$doc->file->delete();
		}

		if ($doc->doc_type_id) {
			$this->db->query('delete')
				->table('case_registration_documents')
				->where('uploaded_file_id', 'IS NULL', $this->db->expr(''))
				->where('document_type', $doc->doc_type_id)
				->execute();
		}

		$doc->delete();
	}

	protected function addCaseTypeToDoc($doc_id, $case_type_id)
	{
		$this->db->query('insert')
			->table('forms_document_case_type')
			->data(['doc_id' => $doc_id, 'case_type_id' => $case_type_id])
			->execute();
	}

	protected function removeCaseTypes($doc_id)
	{
		$this->pixie->db->query('delete')
			->table('forms_document_case_type')
			->where('doc_id', $doc_id)
			->execute();
	}

	protected function addSiteToDoc($doc_id, $site_id)
	{
		$this->db->query('insert')
			->table('forms_document_site')
			->data(['doc_id' => $doc_id, 'site_id' => $site_id])
			->execute();
	}

	protected function removeSites($doc_id)
	{
		$this->pixie->db->query('delete')
			->table('forms_document_site')
			->where('doc_id', $doc_id)
			->execute();
	}

	protected function getSitesCount($orgId)
	{
		return $this->pixie->db->query('count')
			->table('site')
			->where('organization_id', $orgId)
			->where('active', true)
			->execute();
	}

	protected function getCaseTypesCount($orgId)
	{
		return $this->pixie->db->query('count')
			->table('case_type')
			->where('organization_id', $orgId)
			->where('active', true)
			->execute();
	}
}

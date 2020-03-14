<?php

namespace OpakeAdmin\Controller\Cases\Ajax;

class Discharge extends \OpakeAdmin\Controller\Ajax
{

	protected $docPath = '/uploads/cases/discharge/';
	protected $docModel = 'Cases_Discharge';
	protected $protectedDocType = 'cases_discharge';

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionUpload()
	{
		$case = $this->loadModel('Cases_Item', 'subid');

		try {

			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			/** @var \Opake\Request $req */
			$req = $this->request;

			$files = $req->getFiles();

			if (empty($files['file'])) {
				throw new BadRequest('Empty file');
			}
			/** @var \Opake\Request\RequestUploadedFile $upload */
			$upload = $files['file'];

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
				'protected_type' => $this->protectedDocType
			]);
			$uploadedFile->save();

			$hp = $this->orm->get($this->docModel);
			$hp->case_id = $case->id;
			$hp->uploaded_file_id = $uploadedFile->id();
			$hp->save();

			$this->result = 'ok';

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}

	}

	public function actionRemove()
	{
		$doc = $this->loadModel($this->docModel, 'subid');
		if ($doc->case->organization_id !== $this->org->id) {
			throw new \Opake\Exception\Ajax('Document doesn\'t exist');
		}
		$doc->delete();
		$this->result = 'ok';
	}

	public function actionList()
	{
		$case = $this->loadModel('Cases_Item', 'subid');

		$result = [];
		foreach ($case->discharge_docs->find_all() as $doc) {
			$result[] = $doc->toArray();
		}
		$this->result = $result;
	}

}

<?php

namespace OpakeAdmin\Controller\Cases\Ajax;

use Opake\Exception\BadRequest;
use Opake\Exception\InvalidMethod;

class Intake extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionUploadDriversLicense()
	{
		try {
			$case = $this->loadModel('Cases_Item', 'subid');

			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			/** @var \Opake\Request $req */
			$req = $this->request;

			$files = $req->getFiles();
			if (empty($files['file'])) {
				throw new BadRequest('Empty file');
			}

			$upload = $files['file'];

			if (!$upload->isEmpty() && !$upload->hasErrors()) {
				/** @var \Opake\Model\UploadedFile $uploadedFile */
				$uploadedFile = $this->pixie->orm->get('UploadedFile');
				$uploadedFile->storeFile($upload, [
					'is_protected' => true,
					'protected_type' => 'case'
				]);
				$uploadedFile->save();

				$oldFile = $case->drivers_license->find();
				if (!$oldFile->loaded()) {
					$oldFile = null;
				}

				$case->addDriversLicense($uploadedFile);
			}

			$this->result = 'ok';

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}

	}

	public function actionUploadInsuranceCard()
	{
		try {
			$case = $this->loadModel('Cases_Item', 'subid');

			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			/** @var \Opake\Request $req */
			$req = $this->request;

			$files = $req->getFiles();
			if (empty($files['file'])) {
				throw new BadRequest('Empty file');
			}

			$upload = $files['file'];

			if (!$upload->isEmpty() && !$upload->hasErrors()) {
				/** @var \Opake\Model\UploadedFile $uploadedFile */
				$uploadedFile = $this->pixie->orm->get('UploadedFile');
				$uploadedFile->storeFile($upload, [
					'is_protected' => true,
					'protected_type' => 'case'
				]);
				$uploadedFile->save();

				$oldFile = $case->insurance_card->find();
				if (!$oldFile->loaded()) {
					$oldFile = null;
				}

				$case->addInsuranceCard($uploadedFile);
			}

			$this->result = 'ok';

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}

	}

	public function actionRemoveDriversLicense()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$case->drivers_license->delete();
	}

	public function actionRemoveInsuranceCard()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$case->insurance_card->delete();
	}
}

<?php

namespace OpakeAdmin\Controller\Profiles\Credentials;

use Opake\Exception\BadRequest;
use Opake\Exception\InvalidMethod;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionCredentials()
	{
		$alerts = [];
		$userId = $this->request->param('subid');

		$credentials = $this->pixie->orm->get('User_Credentials')
			->where('user_id', $userId)
			->find();

		if ($credentials->loaded()) {
			$modelAlert = $this->pixie->orm->get('User_Credentials_Alert')
				->where([
					['credentials_id', $credentials->id()],
				]);
			foreach ($modelAlert->find_all() as $alert) {
				$alerts[$alert->field] = true;
			}

			$this->result = [
				'success' => true,
				'credentials' => $credentials->toArray(),
				'alert' => $alerts
			];
		} else {
			$this->result = [
				'success' => false,
				'credentials' => null,
				'alert' => $alerts
			];
		}
	}

	public function actionSave()
	{
		$userId = $this->request->param('subid');
		$data = $this->getData();

		$model = $this->pixie->orm->get('User_Credentials')
			->where('user_id', $userId)
			->find();

		if (!$model->loaded()) {
			$model = $this->pixie->orm->get('User_Credentials');
		}

		$model->user_id = $userId;

		try {
			if ($data) {
				$model->fill($data);
			}
			$model->save();
			$this->pixie->events->fireEvent('update.expiring_credentials', $model);
		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$this->result = [
			'id' => (int)$model->id,
			'success' => true
		];
	}

	public function actionUploadFile()
	{
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

			$upload = $files['file'];
			if (!$upload->isEmpty() && !$upload->hasErrors()) {
				/** @var \Opake\Model\UploadedFile $uploadedFile */
				$uploadedFile = $this->pixie->orm->get('UploadedFile');
				$uploadedFile->storeFile($upload, [
					'is_protected' => true
				]);
				$uploadedFile->save();

				$this->result = [
					'success' => true,
					'file' => $this->getUploadedFileArray($uploadedFile)
				];
			}

		} catch (\Exception $e) {
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	protected function getUploadedFileArray($file)
	{
		return [
			'id' => $file->id,
			'uploaded_date' => date('D M d Y H:i:s O', strtotime($file->uploaded_date)),
			'url' => $file->getWebPath(),
			'mime_type' => $file->mime_type,
			'file_name' => $file->original_filename
		];
	}

}

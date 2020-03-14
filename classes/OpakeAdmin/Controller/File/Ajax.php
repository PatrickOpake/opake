<?php

namespace OpakeAdmin\Controller\File;

use Opake\Exception\BadRequest;
use Opake\Exception\InvalidMethod;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function actionUpload()
	{
		try {

			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			/** @var \Opake\Request $req */
			$req = $this->request;

			$files = $req->getFiles();

			$isMultiple = $req->get('multiple') ?: $req->post('multiple');

			if ($isMultiple) {
				if (!is_array($files['file'])) {
					throw new \Exception('Not an array passed');
				}

				$uploadResults = [];
				foreach ($files['file'] as $upload) {
					try {
						$uploadResults[] = $this->loadSingleFile($upload);
					} catch (\Exception $e) {
						$this->logSystemError($e);
						$uploadResults[] = [
							'success' => false,
							'error' => $e->getMessage()
						];
					}
				}

				$this->result = [
					'success' => true,
					'files' => $uploadResults
				];

			} else {
				if (empty($files['file'])) {
					throw new BadRequest('Empty file');
				}
				/** @var \Opake\Request\RequestUploadedFile $upload */
				$upload = $files['file'];
				$this->result = $this->loadSingleFile($upload);
			}

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	protected function getStoreSettings()
	{
		$req = $this->request;
		$isProtected = $req->get('protected') ?: $req->post('protected');
		$protectedType = $req->get('protected_type') ?: $req->post('protected_type');

		return [
			'is_protected' => $isProtected,
			'protected_type' => $protectedType
		];
	}


	/**
	 * @param \Opake\Request\RequestUploadedFile $upload
	 * @return array
	 * @throws BadRequest
	 * @throws \Exception
	 */
	protected function loadSingleFile($upload)
	{
		if ($upload->isEmpty()) {
			throw new BadRequest('Empty file');
		}

		if ($upload->hasErrors()) {
			throw new \Exception('An error occurred while file loading');
		}

		/** @var \Opake\Model\UploadedFile $model */
		$model = $this->pixie->orm->get('UploadedFile');
		$model->storeFile($upload, $this->getStoreSettings());
		$model->save();

		return [
			'success' => true,
			'file_id' => $model->id(),
			'path' => $model->getWebPath(),
		];
	}
}
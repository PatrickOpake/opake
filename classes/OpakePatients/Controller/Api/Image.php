<?php

namespace OpakePatients\Controller\Api;

use OpakePatients\Controller\AbstractAjax;
use Opake\Exception\BadRequest;
use Opake\Exception\InvalidMethod;

class Image extends AbstractAjax
{

	public function actionUploadContent()
	{
		try {

			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			/** @var \Opake\Request $req */
			$req = $this->request;

			$files = $req->getFiles();

			$data = $req->post('data');
			$settingName = $req->get('upload_type') ? : $req->post('upload_type');

			if (!$data) {
				throw new BadRequest('Data is empty');
			}

			$dataURI = \Opake\Request\DataURI::decode($data);
			$extension = \Opake\Helper\UploadedFile\UploadedFileHelper::getExtensionByMimeType($dataURI->getMimeType());
			if (!$extension) {
				throw new \Exception('Unknown file extension');
			}
			$fileName = 'image.' . $extension;

			/** @var \Opake\Model\UploadedFile\Image $model */
			$model = $this->pixie->orm->get('UploadedFile_Image');
			$model->initImageSettings($settingName);

			$model->storeContent($fileName, $dataURI->getContent(), [
				'mime_type' => $dataURI->getMimeType()
			]);
			$model->save();
			$model->createThumbnails();

			$currentSettings = $model->getImageSettings();
			$thumbnails = [];
			if (isset($currentSettings['thumbnails'])) {
				foreach ($currentSettings['thumbnails'] as $name => $conf) {
					$thumbnails[$name] = $model->getThumbnailWebPath($name);
				}
			}

			$this->result = [
				'success' => true,
				'image_id' => $model->id(),
				'original' => $model->getWebPath(),
				'thumbnails' => $thumbnails
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
		}
	}

	public function actionUpload()
	{
		try {

			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			/** @var \Opake\Request $req */
			$req = $this->request;

			$files = $req->getFiles();

			$isMultiple = $req->get('multiple') ? : $req->post('multiple');
			$settingName = $req->get('upload_type') ? : $req->post('upload_type');

			if ($isMultiple) {
				if (!is_array($files['file'])) {
					throw new \Exception('Not an array passed');
				}

				$uploadResults = [];
				foreach ($files['file'] as $upload) {
					try {
						$uploadResults[] = $this->loadSingleImage($upload, $settingName);
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
					'images' => $uploadResults
				];

			} else {
				if (empty($files['file'])) {
					throw new BadRequest('Empty file');
				}
				/** @var \Opake\Request\RequestUploadedFile $upload */
				$upload = $files['file'];
				$this->result = $this->loadSingleImage($upload, $settingName);
			}

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}


	/**
	 * @param \Opake\Request\RequestUploadedFile $upload
	 * @param string $settingName
	 * @return array
	 * @throws BadRequest
	 * @throws \Exception
	 */
	protected function loadSingleImage($upload, $settingName)
	{
		if ($upload->isEmpty()) {
			throw new BadRequest('Empty file');
		}

		if ($upload->hasErrors()) {
			throw new \Exception('An error occurred while file loading');
		}

		/** @var \Opake\Model\UploadedFile\Image $model */
		$model = $this->pixie->orm->get('UploadedFile_Image');
		$model->initImageSettings($settingName);

		$model->storeFile($upload);
		$model->save();
		$model->createThumbnails();

		$currentSettings = $model->getImageSettings();
		$thumbnails = [];
		if (isset($currentSettings['thumbnails'])) {
			foreach ($currentSettings['thumbnails'] as $name => $conf) {
				$thumbnails[$name] = $model->getThumbnailWebPath($name);
			}
		}

		return [
			'success' => true,
			'image_id' => $model->id(),
			'original' => $model->getWebPath(),
			'thumbnails' => $thumbnails
		];
	}
}

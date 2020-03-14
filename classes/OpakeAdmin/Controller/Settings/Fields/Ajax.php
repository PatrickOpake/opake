<?php

namespace OpakeAdmin\Controller\Settings\Fields;

use Opake\Exception\BadRequest;
use Opake\Exception\InvalidMethod;
use Opake\Helper\Pagination;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();

		if (!$this->logged() || !$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}
	}

	public function actionReuploadImage()
	{

		try {
			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			$req = $this->request;
			$typeId = $req->post('item_id');

			$model = $this->orm->get('Inventory_Type', $typeId);

			if (!$model->loaded()) {
				throw new \Opake\Exception\PageNotFound();
			}

			$files = $req->getFiles();

			if (empty($files['file'])) {
				throw new BadRequest('Empty file');
			}

			$upload = $files['file'];
			$image = $this->loadSingleImage($upload);

			if ($model->image->loaded()) {
				$model->image->removeFile();
			}

			$model->image_id = $image['image_id'];
			$model->save();

			$this->result = [
				'success' => true,
			];


		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
			'success' => false,
			'error' => [$e->getMessage()]
			];
		}
	}

	protected function loadSingleImage($upload)
	{
		if ($upload->isEmpty()) {
			throw new BadRequest('Empty file');
		}

		if ($upload->hasErrors()) {
			throw new \Exception('An error occurred while file loading');
		}

		/** @var \Opake\Model\UploadedFile\Image $model */
		$model = $this->pixie->orm->get('UploadedFile_Image');
		$model->initImageSettings();

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

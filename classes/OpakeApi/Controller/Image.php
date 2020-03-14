<?php

namespace OpakeApi\Controller;

use Opake\Helper\Config;

class Image extends AbstractController
{

	public function actionOrder()
	{
		$order = $this->loadModel('Order', 'id', 'post');

		$image = new \Opake\Model\Order\Image($this->pixie);
		$image->order_id = $order->id;
		$image->index = $this->request->post('image_index');
		$image->image_id = $this->uploadFile();
		$image->save();
	}

	public function actionInventory()
	{
		$inventory = $this->loadModel('Inventory', 'id', 'post');

		if (filter_var($this->request->post('remove'), FILTER_VALIDATE_BOOLEAN)) {
			if ($inventory->image->loaded()) {
				$inventory->image->removeFile();
			}
		} else {
			$inventory->image_id = $this->uploadFile();
		}
		$inventory->save();
	}

	protected function uploadFile()
	{

		$files = $this->request->getFiles();
		if (!isset($files['image'])) {
			throw new \OpakeApi\Exception\BadRequest("'image' expected");
		}

		$model = $this->pixie->orm->get('UploadedFile_Image');
		$model->initImageSettings('inventory');
		$model->storeFile($files['image']);
		$model->save();
		$model->createThumbnails();

		return $model->id();
	}
}

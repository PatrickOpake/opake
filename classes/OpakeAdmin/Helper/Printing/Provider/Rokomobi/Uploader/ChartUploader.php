<?php

namespace OpakeAdmin\Helper\Printing\Provider\Rokomobi\Uploader;

use Opake\Application;
use Opake\Model\UploadedFile;
use OpakeAdmin\Helper\Printing\Provider\Rokomobi\Uploader;
use OpakeAdmin\Helper\Printing\Provider\Rokomobi\UploadResult;

class ChartUploader extends Uploader
{
	public function upload()
	{
		/** @var \OpakeAdmin\Helper\Printing\Document\Cases\Chart $document */
		$document = $this->document;
		$form = $document->getForm();

		if ($form->uploaded_file_id) {
			if ($form->file->loaded()) {
				if ($form->file->mime_type !== \OpakeAdmin\Helper\Printing\PrintCompiler::MIME_TYPE_PDF) {
					return null;
				}
			}
		}

		if ($form->remote_file_id) {
			$remoteFile = $form->remote_file;
			return new UploadResult($remoteFile->content_item_id, $remoteFile->asset_id);
		} else if ($form->uploaded_file_id) {
			if (!$form->file->loaded()) {
				throw new \Exception('File of form ' . $form->id() . ' is not found');
			}

			$remoteDocModel = $this->uploadLocalFile($form->file);
			$remoteDocModel->save();

			$form->remote_file_id = $remoteDocModel->id();
			$form->save();

			return new UploadResult($remoteDocModel->content_item_id, $remoteDocModel->asset_id);
		} else if ($form->own_text !== null) {
			return parent::upload();
		}

		return null;

	}

	/**
	 *
	 * @param UploadedFile $file
	 * @return \Opake\Model\RemoteStorageDocument
	 */
	protected function uploadLocalFile($file)
	{
		$contentItem = $this->service->uploadFile($file->original_filename, $file->getSystemPath(), $file->mime_type, false);

		$app = Application::get();
		$remoteDocModel = $app->orm->get('RemoteStorageDocument');
		$remoteDocModel->filename = $file->original_filename;
		$remoteDocModel->content_item_id = $contentItem->getObjectId();
		$remoteDocModel->asset_id = $contentItem->getFirstAsset()->getObjectId();

		return $remoteDocModel;
	}
}
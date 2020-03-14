<?php

namespace OpakeAdmin\Form\Inventory;

use Opake\Form\AbstractForm;

class InvoiceForm extends AbstractForm
{

	protected $inlcudeUploadFile = true;

	public function setInlcudeUploadFile($inlcudeUploadFile)
	{
		$this->inlcudeUploadFile = (bool) $inlcudeUploadFile;
	}

	protected function prepareValuesForModel($data)
	{
		$result = parent::prepareValuesForModel($data);

		if (!empty($result['files']) && !empty($result['files']['uploadedFile'])) {
			/** @var \Opake\Request\RequestUploadedFile $upload */
			$upload = $result['files']['uploadedFile'];

			/** @var \Opake\Model\UploadedFile $uploadedFile */
			$uploadedFile = $this->pixie->orm->get('UploadedFile');
			$uploadedFile->storeFile($upload, [
				'is_protected' => true,
				'protected_type' => 'inventory_invoice'
			]);
			$uploadedFile->save();
			$result['uploaded_file_id'] = $uploadedFile->id;
		}

		return $result;
	}

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('name')->rule('filled')->error('You must specify name');
		$validator->field('date')->rule('filled')->error('You must specify date');
		$validator->field('date')->rule('date')->error('Incorrect Date format');
		$validator->field('manufacturers')->rule('filled')->error('You must select at least one manufacturer');

		if ($this->inlcudeUploadFile) {
			$validator->field('files')->rule('filled')->error('Uploaded file is empty');
			$validator->field('files')->rule('callback', function($files) {
					if (!isset($files['uploadedFile'])) {
						return false;
					}
					$file = $files['uploadedFile'];
					if ($file->isEmpty()) {
						return false;
					}
					return true;
				})->error('Uploaded file is empty')
				->only_if_valid(true);

			$validator->field('files')->rule('callback', function($files) {
					if (isset($files['uploadedFile'])) {
						$file = $files['uploadedFile'];
						if (!$file->hasErrors()) {
							return true;
						}
					}
					return false;
				})->error('An error occured while file uploading')
				->only_if_valid(true);

			$validator->field('files')->rule('callback', function($files) {
					if (isset($files['uploadedFile'])) {
						$file = $files['uploadedFile'];
						if ($file->getType() === 'application/pdf') {
							return true;
						}
					}
					return false;
				})->error('Only PDF files are supported')
				->only_if_valid(true);

			$validator->field('files')->rule('callback', function($files) {
					if (isset($files['uploadedFile'])) {
						$file = $files['uploadedFile'];
						try {
							$fpdi = new \FPDI();
							$fpdi->setSourceFile($file->getTmpName());
							return true;
						} catch (\Exception $e) {
							return false;
						}
					}

					return false;
				})->error('This PDF document uses an unsupported compression format')
				->only_if_valid(true);
		}
	}

	/**
	 * @return array
	 */
	protected function getFields()
	{
		$fields = array_merge(parent::getFields(), [
			'name',
			'date',
			'manufacturers',
			'items'
		]);
		if ($this->inlcudeUploadFile) {
			$fields = array_merge($fields, [
				'files'
			]);
		}
		return $fields;
	}

}

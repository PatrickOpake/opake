<?php

namespace OpakeAdmin\Form\Cases;

use Opake\Form\AbstractForm;
use OpakeAdmin\Form\Charts\ChartRenameForm;

class AdditionalChartUploadForm extends AbstractForm
{

	protected $newDocument = true;

	/**
	 * @return boolean
	 */
	public function isNewDocument()
	{
		return $this->newDocument;
	}

	/**
	 * @param boolean $newDocument
	 */
	public function setIsNewDocument($newDocument)
	{
		$this->newDocument = $newDocument;
	}

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		if ($this->isNewDocument()) {
			$validator->field('name')->rule('filled')->error('You must specify name');
			$validator->field('name')->rule('callback', function($name) {
				return ChartRenameForm::isChartNameCorrect($name);
			})->error('Name contains incorrect characters');
		}

		$validator->field('files')->rule('filled')->error('Uploaded file is empty');
		$validator->field('files')->rule('callback', function($files) {
			if (!isset($files['file'])) {
				return false;
			}
			$file = $files['file'];
			if ($file->isEmpty()) {
				return false;
			}
			return true;
		})->error('Uploaded file is empty')
			->only_if_valid(true);

		$validator->field('files')->rule('callback', function($files) {
			if (isset($files['file'])) {
				$file = $files['file'];
				if (!$file->hasErrors()) {
					return true;
				}
			}
			return false;
		})->error('An error occured while file uploading')
			->only_if_valid(true);

		$validator->field('files')->rule('callback', function($files) {
			if (isset($files['file'])) {
				$file = $files['file'];
				if ($file->getType() === 'application/pdf') {
					return true;
				}
			}
			return false;
		})->error('Only PDF files are supported')
			->only_if_valid(true);

		$validator->field('files')->rule('callback', function($files) {
			if (isset($files['file'])) {
				$file = $files['file'];
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

	/**
	 * @return array
	 */
	protected function getFields()
	{
		return [
			'name',
			'files'
		];
	}
}
<?php

namespace OpakeAdmin\Form\Charts;

use Opake\Form\AbstractForm;

class ChartFileUploadForm extends AbstractForm
{
	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('segment')->rule('filled')->error('Segment is reqiuired for a chart');
		$validator->field('name')->rule('filled')->error('You must specify name');
		$validator->field('name')->rule('callback', function($name) {
			return ChartRenameForm::isChartNameCorrect($name);
		})->error('Name contains incorrect characters');

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

	/**
	 * @return array
	 */
	protected function getFields()
	{
		return [
			'segment',
			'name',
			'files'
		];
	}
}
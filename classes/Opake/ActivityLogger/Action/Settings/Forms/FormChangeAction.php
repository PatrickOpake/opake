<?php

namespace Opake\ActivityLogger\Action\Settings\Forms;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Comparer\SettingsFormsComparer;
use Opake\ActivityLogger\Extractor\Settings\Forms\FormExtractor;

class FormChangeAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();

		return [
			'name' => $model->name,
			'segment' => $model->segment
		];
	}

	/**
	 * @return array
	 */
	protected function getFieldsForCompare()
	{
		return [
			'uploaded_file_id',
			'name',
			'own_text',
			'include_header',
			'is_all_sites',
			'is_all_case_types',
			'sites',
			'case_types'
		];
	}

	public function createComparer()
	{
		return new SettingsFormsComparer();
	}

	public function createExtractor()
	{
		return new FormExtractor();
	}
}
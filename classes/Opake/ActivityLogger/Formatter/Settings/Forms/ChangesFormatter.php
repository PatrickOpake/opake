<?php

namespace Opake\ActivityLogger\Formatter\Settings\Forms;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class ChangesFormatter extends DefaultFormatter
{
	protected function prepareDataBeforeFormat($data)
	{
		if (isset($data['is_all_sites'])) {
			if ($data['is_all_sites'] == 1) {
				$data['sites'] = 'All';
			}
			unset($data['is_all_sites']);
		}

		if (isset($data['is_all_case_types'])) {
			if ($data['is_all_case_types'] == 1) {
				$data['case_types'] = 'All';
			}
			unset($data['is_all_case_types']);
		}

		return $data;
	}


	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'sites':
				return $this->formatSites($value);

			case 'case_types':
				return $this->formatCaseTypes($value);

			case 'include_header':
				return FormatterHelper::formatYesNo($value);

			case 'uploaded_file_id':
				return LinkFormatterHelper::formatUploadedFileLink($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'uploaded_file_id' => 'File',
			'sites' => 'Sites',
			'case_types' => 'Case Types',
			'name' => 'Name',
			'own_text' => 'Text',
			'include_header' => 'Include Header Details',
		];
	}

	protected function formatCaseTypes($value)
	{
		if (!is_array($value)) {
			return $value;
		}

		return FormatterHelper::formatProceduresList($this->pixie, $value);
	}

	protected function formatSites($value)
	{
		if (!is_array($value)) {
			return $value;
		}

		return FormatterHelper::formatSitesList($this->pixie, $value);
	}
}
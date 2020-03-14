<?php

namespace Opake\ActivityLogger\Action\Patient;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Extractor\Insurance\InsuranceExtractor;

class InsuranceChangeAction extends ModelAction
{

	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'patient' => $model->patient_id,
			'number' => $this->getExtractor()->getAdditionalInfo('number')
		];
	}

	/**
	 * @return array
	 */
	protected function getSearchParams()
	{
		return [
			'patient_id' => $this->getExtractor()->getModel()->patient_id
		];
	}

	/**
	 * @return array
	 */
	protected function getIgnoredFieldsForCompare()
	{
		return [];
	}

	protected function createExtractor()
	{
		return new InsuranceExtractor();
	}

}
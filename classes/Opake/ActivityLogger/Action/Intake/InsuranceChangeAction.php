<?php

namespace Opake\ActivityLogger\Action\Intake;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Extractor\Insurance\InsuranceExtractor;

class InsuranceChangeAction extends ModelAction
{
	protected function fetchDetails()
	{
		return [
			'number' => $this->getExtractor()->getAdditionalInfo('number')
		];
	}

	protected function getSearchParams()
	{
		return [
			'case_id' => $this->getInsuranceCaseId()
		];
	}

	protected function getInsuranceCaseId()
	{
		$model = $this->getExtractor()->getModel();
		$registrationId = $model->registration_id;


		$row = $this->pixie->db
			->query('select')
			->table('case_registration')
			->fields('case_id')
			->where('id', $registrationId)
			->execute()->current();

		return ($row) ? $row->case_id : null;
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
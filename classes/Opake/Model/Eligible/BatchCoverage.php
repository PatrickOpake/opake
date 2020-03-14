<?php

namespace Opake\Model\Eligible;

use Opake\Model\AbstractModel;

class BatchCoverage extends AbstractModel
{
	public $id_field = 'id';

	public $table = 'case_batch_eligibility';

	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'date_received' => null,
		'entries_sent' => 0,
		'eligible' => 0,
		'not_eligible' => 0,
		'insufficient_data' => 0,
		'coverage' => null,
	];

	protected $has_many = [
		'case_insurances' => [
			'model' => 'Cases_Item',
			'through' => 'case_batch_eligibility_cases',
			'key' => 'batch_id',
			'foreign_key' => 'case_insurance_id'
		],
	];

	protected $decodedCoverage;

	public function getCoverageArray()
	{
		if (!$this->coverage) {
			return [];
		}

		if ($this->decodedCoverage === null) {
			$this->decodedCoverage = json_decode($this->coverage, true);
		}

		return $this->decodedCoverage;
	}
}
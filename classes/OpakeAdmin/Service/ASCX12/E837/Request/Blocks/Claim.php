<?php

namespace OpakeAdmin\Service\ASCX12\E837\Request\Blocks;

use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;
use OpakeAdmin\Service\Navicure\Claims\Procedures\ClaimProceduresContainer;

class Claim extends AbstractRequestSegment
{
	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var bool
	 */
	protected $originalClaim = false;

	/**
	 * @var \Opake\Model\Billing\Navicure\Claim
	 */
	protected $claimEntry;

	/**
	 * @var ClaimProceduresContainer
	 */
	protected $billsContainer;

	/**
	 * Claim constructor.
	 * @param \Opake\Model\Cases\Item $case
	 * @param bool $originalClaim
	 */
	public function __construct(\Opake\Model\Cases\Item $case, \Opake\Model\Billing\Navicure\Claim $claimEntry, $originalClaim, $billsContainer)
	{
		$this->case = $case;
		$this->originalClaim = $originalClaim;
		$this->claimEntry = $claimEntry;
		$this->billsContainer = $billsContainer;
	}

	protected function generateSegmentsBeforeChildren($data)
	{

		$case = $this->case;

		$claimIdString = str_pad($this->claimEntry->id(), 4, '0', STR_PAD_LEFT);
		$codingBillsChargesSum = $this->billsContainer->getTotalSum();
		$codingBillsChargesSum = round($codingBillsChargesSum, 2);

		$claimFrequencyCode = (!$this->originalClaim) ? '1' : '7';

		//Loop 2300 - Claim
		$claimData = [
			'CLM',
			$claimIdString,
			$codingBillsChargesSum,
			'',
			'',
			$this->mergeComponents([
				$this->getPlaceOfServiceOrFacilityCode(),
				$this->getCodeQualifier(),
				$claimFrequencyCode
			])
		];

		$data[] = array_merge($claimData, $this->getAdditionalCodes());

		// REF - PAYER CLAIM CONTROL NUMBER
		if ($this->originalClaim) {
			$data[] = [
				'REF',
				'F8',
				$this->originalClaim
			];
		}

		//up to 12 diagnosis
		$diagnosisList = $case->coding->diagnoses
			->limit(12)
			->find_all()
			->as_array();

		$icdCodes = [];
		foreach ($diagnosisList as $index => $diagnosis) {
			$type = ($index == 0) ? 'ABK' : 'ABF';
			$icdCodes[] = $this->mergeComponents([$type, $this->prepareIcdCode($diagnosis->icd->code)]);
		}

		if ($case->coding->amount_paid) {
			$data[] = [
				'AMT',
				'F5',
				$case->coding->amount_paid
			];
		}

		$data[] = array_merge([
			'HI'
		], $icdCodes);

		return $data;
	}

	protected function getPlaceOfServiceOrFacilityCode()
	{
		$placeOfServiceCode = '24'; //Ambulatory Surgical Center
		return $placeOfServiceCode;
	}

	protected function getCodeQualifier()
	{
		return 'B';
	}

	protected function getAdditionalCodes()
	{
		return ['Y', 'A', 'Y', 'I'];
	}

	protected function prepareIcdCode($code)
	{
		return str_replace('.', '', $code);
	}
}
<?php

namespace OpakeAdmin\Service\ASCX12\E837\Request\Blocks;

use Opake\Model\Insurance\AbstractType;
use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;

class Payor extends AbstractRequestSegment
{
	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance
	 */
	protected $codingInsurance;

	/**
	 * Payor constructor.
	 * @param \Opake\Model\Cases\Item $case
	 * @param \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance $codingInsurance
	 */
	public function __construct(\Opake\Model\Cases\Item $case, \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance $codingInsurance)
	{
		$this->codingInsurance = $codingInsurance;
		$this->case = $case;
	}

	protected function generateSegmentsBeforeChildren($data)
	{
		$insurance = $this->codingInsurance;

		$data[] = [
			'NM1',
			'PR',
			'2',
			$this->prepareString($insurance->getInsuranceCompanyName(), 60),
			'',
			'',
			'',
			'',
			'PI',
			$this->getInsuranceCompanyCode()
		];

		if ($insurance->getAddress()) {
			$address = $this->prepareAddress($insurance->getAddress());
			$data[] = [
				'N3',
				$address[0]
			];
		}

		$city = $insurance->getCity();
		$state = $insurance->getState();
		$zipCode = $insurance->getZipCode();
		if ($city) {
			$data[] = [
				'N4',
				$this->prepareString($city->name, 30),
				$state ? $this->prepareString($state->code, 2) : '',
				$zipCode ? $this->prepareNumber($zipCode, 15) : ''
			];
		}

		return $data;
	}

	protected function getInsuranceCompanyCode()
	{
		$code = $this->codingInsurance->getCMS1500PayerId();
		if (!$code) {
			throw new \Exception('The insurance company has no Electronic 1500 Payer ID code');
		}

		return $code;
	}

}
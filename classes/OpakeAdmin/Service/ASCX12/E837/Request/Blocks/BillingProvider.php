<?php

namespace OpakeAdmin\Service\ASCX12\E837\Request\Blocks;

use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;

class BillingProvider extends AbstractRequestSegment
{
	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * BillingProvider constructor.
	 * @param \Opake\Model\Cases\Item $case
	 */
	public function __construct(\Opake\Model\Cases\Item $case)
	{
		$this->case = $case;
	}


	protected function generateSegmentsBeforeChildren($data)
	{

		$site = $this->case->location->site;

		if (!$site || !$site->loaded()) {
			throw new \Exception('Site for case is not defined');
		}

		if (!$site->name) {
			throw new \Exception('Site Name is not filled for site ' . $site->name);
		}

		if (!$site->npi) {
			throw new \Exception('NPI is not filled for site ' . $site->name);
		}

		if (!$site->federal_tax) {
			throw new \Exception('TIN is not filled for site ' . $site->name);
		}

		if (!$site->state->loaded()) {
			throw new \Exception('Pay-To State is not entered for site ' . $site->name);
		}

		if (!$site->city->loaded()) {
			throw new \Exception('City is not entered for site ' . $site->name);
		}

		if (!$site->address) {
			throw new \Exception('Address is not entered for site ' . $site->name);
		}

		if (!$site->zip_code) {
			throw new \Exception('Zip-code is not entered for site ' . $site->name);
		}

		//Loop 2000A Billing Provider HL Loop
		$data[] = [
			'HL',
			'1',
			'',
			'20',
			'1'
		];

		$data[] = [
			'PRV',
			'BI',
			'PXC',
			$this->getTaxonomyCode()
		];

		//Loop 2010AA Billing Provider
		$data[] = [
			'NM1',
			'85',
			'2',
			$this->prepareString($site->name, 60),
			'',
			'',
			'',
			'',
			'XX',
			$this->prepareNumber($site->npi, 10)
		];
		$address = $this->prepareAddress($site->address, 55);
		$data[] = [
			'N3',
			$address[0]
		];

		$data[] = [
			'N4',
			$this->prepareString($site->city->name, 30),
			$this->prepareString($site->state->code, 2),
			$this->prepareNumber($site->zip_code, 15)
		];
		$data[] = [
			'REF',
			'EI',
			$this->prepareNumber($site->federal_tax, 10)
		];

		return $data;
	}

	protected function getTaxonomyCode()
	{
		//hardcoded value for a hotfix, will be moved to site profile after
		return '261QA1903X';
	}


}
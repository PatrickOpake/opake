<?php

namespace OpakeAdmin\Service\ASCX12\E837\Request\Blocks;

use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;
use Opake\Model\Cases;

class SenderAndReceiver extends AbstractRequestSegment
{
	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var \Opake\Model\Organization
	 */
	protected $caseOrganization;

	/**
	 * @var \Opake\Model\Site
	 */
	protected $caseSite;

	/**
	 * @var \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance
	 */
	protected $codingInsurance;

	/**
	 *
	 * @param Cases\Item $case
	 * @param \Opake\Model\Organization $caseOrganization
	 * @param \Opake\Model\Site $caseSite
	 * @param \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance $codingInsurance
	 */
	public function __construct(Cases\Item $case, \Opake\Model\Organization $caseOrganization, \Opake\Model\Site $caseSite, \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance $codingInsurance)
	{
		$this->case = $case;
		$this->caseOrganization = $caseOrganization;
		$this->caseSite = $caseSite;
		$this->codingInsurance = $codingInsurance;
	}


	protected function generateSegmentsBeforeChildren($data)
	{
		$organizationName = $this->prepareString($this->caseOrganization->name, 60);
		$organizationId = $this->caseOrganization->id();

		if (!$organizationName) {
			throw new \Exception('Organization name is required');
		}

		$siteContactName = $this->prepareString($this->caseSite->contact_name, 60);
		$siteContactPhone = $this->caseSite->contact_phone;

		if (!$siteContactName) {
			throw new \Exception('Contact name is not entered for the site');
		}

		if (!$siteContactName) {
			throw new \Exception('Contact phone is not entered for the site');
		}

		$primaryInsuranceCompanyName = $this->prepareString($this->codingInsurance->getInsuranceCompanyName(), 60);
		$primaryInsuranceCompanyId = $this->codingInsurance->getCMS1500PayerId();

		//Loop 1000A Submitter
		$data[] = [
			'NM1',
			'41', //Submitter
			'2', //Non-person
			$organizationName,
			'',
			'',
			'',
			'',
			'46', //ID qualifier, ETIN
			str_pad((string) $organizationId, 2, '0', STR_PAD_LEFT)
		];
		$data[] = [
			'PER',
			'IC',
			$siteContactName,
			'TE',
			$this->prepareNumber($siteContactPhone)
		];

		//Loop 1000B Receiver
		$data[] = [
			'NM1',
			'40', //Receiver
			'2',
			$primaryInsuranceCompanyName,
			'',
			'',
			'',
			'',
			'46',
			$primaryInsuranceCompanyId
		];

		return $data;
	}


}
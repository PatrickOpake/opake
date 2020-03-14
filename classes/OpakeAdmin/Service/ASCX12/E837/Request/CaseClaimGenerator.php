<?php

namespace OpakeAdmin\Service\ASCX12\E837\Request;

use Opake\Model\Cases;
use Opake\Model\Insurance\AbstractType;
use OpakeAdmin\Helper\Billing\Insurance\RegularInsurance;
use OpakeAdmin\Service\ASCX12\AbstractGenerator;
use OpakeAdmin\Service\ASCX12\E837\Request\Blocks\BillingProvider;
use OpakeAdmin\Service\ASCX12\E837\Request\Blocks\Claim;
use OpakeAdmin\Service\ASCX12\E837\Request\Blocks\Patient;
use OpakeAdmin\Service\ASCX12\E837\Request\Blocks\Payor;
use OpakeAdmin\Service\ASCX12\E837\Request\Blocks\PayToProvider;
use OpakeAdmin\Service\ASCX12\E837\Request\Blocks\ReferringProvider;
use OpakeAdmin\Service\ASCX12\E837\Request\Blocks\RenderingProvider;
use OpakeAdmin\Service\ASCX12\E837\Request\Blocks\SenderAndReceiver;
use OpakeAdmin\Service\ASCX12\E837\Request\Blocks\ServicesInfo;
use OpakeAdmin\Service\ASCX12\E837\Request\Blocks\Subscriber;
use OpakeAdmin\Service\ASCX12\E837\Request\Headers\BHTBeginningOfHierarchicalTransaction;
use OpakeAdmin\Service\ASCX12\E837\Request\Headers\STTransactionSet;
use OpakeAdmin\Service\ASCX12\General\Request\GSHeader;
use OpakeAdmin\Service\ASCX12\General\Request\ISAHeader;

class CaseClaimGenerator extends AbstractGenerator
{
	/**
	 * @var Cases\Item
	 */
	protected $case;

	/**
	 * @var \Opake\Model\Billing\Navicure\Claim
	 */
	protected $claim;

	/**
	 * @var array
	 */
	protected $collectionOfBills;


	/**
	 * @param Cases\Item $case
	 * @param \Opake\Model\Billing\Navicure\Claim $claim
	 */
	public function __construct(Cases\Item $case, \Opake\Model\Billing\Navicure\Claim $claim, $collectionOfBills)
	{
		parent::__construct();

		$this->case = $case;
		$this->claim = $claim;
		$this->collectionOfBills = $collectionOfBills;
	}

	public function generateContent()
	{
		$claimIdString = $this->formatClaimId($this->claim->id());
		$requestDateTime = new \DateTime();
		$interchange = new ISAHeader($requestDateTime);
		$gsHeader = new GSHeader($requestDateTime, 'HC');
		$interchange->addChildSegment($gsHeader);
		$stHeader = new STTransactionSet($claimIdString);
		$gsHeader->addChildSegment($stHeader);
		$bhtHeader = new BHTBeginningOfHierarchicalTransaction($requestDateTime, $claimIdString);
		$stHeader->addChildSegment($bhtHeader);


		$caseOrganization = $this->case->organization;
		$caseSite = $this->case->location->site;
		$caseCoding = $this->case->coding;
		$originalClaimIdString = $caseCoding->original_claim_id ?
			$this->formatClaimId($caseCoding->original_claim_id) : null;

		if (!$caseCoding->isPrimaryInsuranceAssigned()) {
			$orderList = AbstractType::getInsuranceOrderList();
			$insuranceTypeLabel = '';
			if(isset($orderList[$caseCoding->insurance_order])) {
				$insuranceTypeLabel = $orderList[$caseCoding->insurance_order] . ' insurance';
			}
			$codingInsurance = $caseCoding->getAssignedInsurance();
		} else {
			$insuranceTypeLabel = 'primary insurance';
			$codingInsurance = $caseCoding->getPrimaryInsurance();
		}

		if (!$codingInsurance) {
			throw new \Exception('Case has no active ' . $insuranceTypeLabel . ' selected');
		}
		$primaryInsurance = $codingInsurance->getCaseInsurance();
		if (!$primaryInsurance || !$primaryInsurance->loaded()) {
			throw new \Exception('Unsupported ' . $insuranceTypeLabel . ' type');
		}
		if ($primaryInsurance->isDescriptionInsurance()) {
			throw new \Exception('Unsupported ' . $insuranceTypeLabel .' type');
		}

		/*$primaryUser = $this->case->getFirstSurgeon();

		if (!$primaryUser || !$primaryUser->loaded()) {
			throw new \Exception('Primary user for case is not defined');
		}*/

		$codingInsurance->setUsePayerDataIfMissed(true);

		$bhtHeader->addChildSegment(new SenderAndReceiver($this->case, $caseOrganization, $caseSite, $codingInsurance));
		$bhtHeader->addChildSegment(new BillingProvider($this->case));
		//$bhtHeader->addChildSegment(new PayToProvider($primaryUser, $caseSite));
		$bhtHeader->addChildSegment(new Subscriber($this->case, $codingInsurance));
		$bhtHeader->addChildSegment(new Payor($this->case, $codingInsurance));
		$bhtHeader->addChildSegment(new Patient($this->case, $codingInsurance));
		$bhtHeader->addChildSegment(new Claim($this->case, $this->claim, $originalClaimIdString, $this->collectionOfBills));
		$bhtHeader->addChildSegment(new RenderingProvider($this->case));
		$bhtHeader->addChildSegment(new ReferringProvider($this->case));
		$bhtHeader->addChildSegment(new ServicesInfo($this->case, $this->collectionOfBills));

		return $this->generateStructureContent($interchange);
	}
}


<?php

namespace OpakeAdmin\Controller\Cases\Ajax\Coding;

use Opake\Exception\Forbidden;
use Opake\Exception\PageNotFound;
use Opake\Helper\TimeFormat;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Billing\PaperClaim;
use OpakeAdmin\Service\Navicure\Claims\ClaimGenerator;

class Claim extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionGetClaims()
	{
		$caseId = $this->request->param('subid');
		$claims = $this->orm->get('Billing_Navicure_Claim')
			->where('case_id', $caseId)
			->where([
				['type', \Opake\Model\Billing\Navicure\Claim::TYPE_ELECTRONIC_UB04_CLAIM],
				['or', ['type', \Opake\Model\Billing\Navicure\Claim::TYPE_ELECTRONIC_1500_CLAIM]],
			])
			->order_by('last_transaction_date', 'DESC')
			->order_by('id', 'ASC')
			->find_all();

		$activeClaims = [];
		foreach ($claims as $claim) {
			$activeClaims[] = $claim->getFormatter('Coding')
				->toArray();
		}

		$this->result = [
			'success' => true,
		    'active_claims' => $activeClaims
		];

	}

	public function actionGetAllClaims()
	{
		$caseId = $this->request->param('subid');
		$claims = $this->orm->get('Billing_Navicure_Claim')
			->where('case_id', $caseId)
			//->order_by('last_transaction_date', 'DESC')
			->order_by('id', 'DESC')
			->find_all();

		$activeClaims = [];
		foreach ($claims as $claim) {
			$activeClaims[] = $claim->getFormatter('Coding')
				->toArray();
		}

		$this->result = [
			'success' => true,
			'claims' => $activeClaims
		];

	}

	public function actionCheckCaseErrors()
	{
		$caseId = $this->request->param('subid');
		$case = $this->orm->get('Cases_Item', $caseId);
		if (!$case->loaded()) {
			throw new PageNotFound();
		}

		$generator = new \OpakeAdmin\Service\Navicure\Claims\Generator\ProfessionalClaimGenerator($case);
		$professionalClaimErrors = $generator->getCaseErrors();

		$generator = new \OpakeAdmin\Service\Navicure\Claims\Generator\InstitutionalClaimGenerator($case);
		$institutionalClaimGenerator = $generator->getCaseErrors();

		$result = [
			'common' => [],
		    'professional' => [],
		    'institutional' => []
		];

		foreach ($professionalClaimErrors as $error) {
			if (in_array($error, $institutionalClaimGenerator)) {
				if (!in_array($error, $result['common'])) {
					$result['common'][] = $error;
				}
			} else {
				$result['professional'][] = $error;
			}
		}

		foreach ($institutionalClaimGenerator as $error) {
			if (in_array($error, $professionalClaimErrors)) {
				if (!in_array($error, $result['common'])) {
					$result['common'][] = $error;
				}
			} else {
				$result['institutional'][] = $error;
			}
		}

		$this->result = [
			'success' => true,
		    'errors' => $result
		];
	}

	public function actionSendClaim()
	{
		$data = $this->getData();
		$caseId = $data->case;
		$claimTypes = $data->claim_types;
		$case = $this->orm->get('Cases_Item', $caseId);

		if ($case->organization_id != $this->org->id()) {
			throw new Forbidden();
		}

		if (!$case->loaded()) {
			throw new PageNotFound();
		}

		try {
			if ($claimTypes->electronicProfessionalClaim) {
				$chunkedBills = ClaimGenerator::splitClaims($case);
				foreach ($chunkedBills as $chunkedBill) {
					$generator = new \OpakeAdmin\Service\Navicure\Claims\Generator\ProfessionalClaimGenerator($case, $chunkedBill);
					$claim = $generator->tryToSendClaim();
					$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_BILLING_CLAIM_ELECTRONIC_1500_SENT)
						->setModel($claim)
						->register();
				}
			}

			if ($claimTypes->electronicInstitutionalClaim) {
				$chunkedBills = ClaimGenerator::splitClaims($case);
				foreach ($chunkedBills as $chunkedBill) {
					$generator = new \OpakeAdmin\Service\Navicure\Claims\Generator\InstitutionalClaimGenerator($case, $chunkedBill);
					$claim = $generator->tryToSendClaim();
					$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_BILLING_CLAIM_ELECTRONIC_UB04_SENT)
						->setModel($claim)
						->register();
				}
			}

			if ($claimTypes->paperUB04Claim) {
				$claim = $this->savePaperClaim($case, \Opake\Model\Billing\Navicure\Claim::TYPE_UB04);
				$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_BILLING_CLAIM_PAPER_UB04_SENT)
					->setModel($claim)
					->register();
			}

			if ($claimTypes->paper1500Claim) {
				$claim = $this->savePaperClaim($case, \Opake\Model\Billing\Navicure\Claim::TYPE_1500);
				$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_BILLING_CLAIM_PAPER_1500_SENT)
					->setModel($claim)
					->register();
			}

			$this->result = [
				'success' => true
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
			    'errors' => [
				    $e->getMessage()
			    ]
			];
		}
	}

	public function actionForceUpdateStatus()
	{
		$handler = new \OpakeAdmin\Service\Navicure\Claims\ResponseHandler();
		$handler->handleIncomingFiles();
	}

	public function actionMarkAsReadyToSend()
	{
		$data = $this->getData();
		$case = $this->orm->get('Cases_Item', $data->case);
		$coding = $case->coding;
		if (!empty($data->claim_types->electronicProfessionalClaim)) {
			$coding->is_ready_professional_claim = 1;
		}
		if (!empty($data->claim_types->electronicInstitutionalClaim)) {
			$coding->is_ready_institutional_claim = 1;
		}
		$coding->save();
		$this->result = [
			'electronicProfessionalClaim' => (bool) $coding->is_ready_professional_claim,
			'electronicInstitutionalClaim' => (bool) $coding->is_ready_institutional_claim,
		];
	}

	protected function savePaperClaim($case, $type)
	{
		$primaryInsurance = $case->coding->getPrimaryInsurance();
		$model = $this->orm->get('Billing_Navicure_Claim');
		$model->case_id = $case->id();
		$model->last_transaction_date = TimeFormat::formatToDBDatetime(new \DateTime());
		$model->sending_date = TimeFormat::formatToDBDatetime(new \DateTime());
		$model->type = $type;

		if (!$primaryInsurance || !$case->coding->isPrimaryInsuranceAssigned()) {
			$assignedInsurance = $case->coding->getAssignedInsurance();
			if ($assignedInsurance) {
				$caseInsurance = $assignedInsurance->getCaseInsurance();
				if ($caseInsurance && $caseInsurance->isRegularInsurance()) {
					$model->insurance_payer_id = $caseInsurance->getInsuranceDataModel()->insurance->id();
				}
			}
		} else {
			$caseInsurance = $primaryInsurance->getCaseInsurance();
			if ($caseInsurance && $caseInsurance->isRegularInsurance()) {
				$model->insurance_payer_id = $caseInsurance->getInsuranceDataModel()->insurance->id();
			}
		}
		$model->save();
		$model->copyPrimaryInsurance();
		$model->save();

		return $model;
	}
}
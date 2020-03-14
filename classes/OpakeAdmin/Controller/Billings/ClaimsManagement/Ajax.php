<?php

namespace OpakeAdmin\Controller\Billings\ClaimsManagement;

use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Billing\Navicure\Claim;
use Opake\Model\Billing\PaperClaim;
use OpakeAdmin\Helper\Printing\Document\Common\ContentPDFDocument;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$items = [];

		$search = new \OpakeAdmin\Model\Search\Billing\ClaimsManagement($this->pixie);
		$search->setOrganizationId($this->org->id());
		$results = $search->search(
			$this->orm->get('Billing_Navicure_Claim')
			->where([
				['type', Claim::TYPE_ELECTRONIC_UB04_CLAIM],
				['or', ['type', Claim::TYPE_ELECTRONIC_1500_CLAIM]],
			]),
			$this->request
		);

		foreach ($results as $result) {
			$items[] = $result
				->getFormatter('ListEntry')
				->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionPaperClaims()
	{
		$items = [];

		$search = new \OpakeAdmin\Model\Search\Billing\PaperClaims($this->pixie);
		$search->setOrganizationId($this->org->id());
		$results = $search->search(
			$this->orm->get('Billing_Navicure_Claim')
			->where('and', [
				['or', ['type', Claim::TYPE_1500]],
				['or', ['type', Claim::TYPE_UB04]],
			]),
			$this->request
		);

		foreach ($results as $result) {
			$items[] = $result
				->getFormatter('PaperListEntry')
				->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionCompileCodingDocuments()
	{
		try {

			$claims = $this->request->post('claims');

			if (!$claims || !is_array($claims)) {
				throw new \Exception('Claims list is empty');
			}

			$documentsToPrint = [];
			$caseIds = [];
			$patients = [];
			foreach ($claims as $claimId) {
					$claimModel = $this->pixie->orm->get('Billing_Navicure_Claim', $claimId);
					if ($claimModel->loaded()) {
						$case = $claimModel->case;
						$patients[] = $case->registration->getFullName();
						$caseIds[] = $case->id();
						$helper = new \OpakeAdmin\Helper\Billing\Coding\UB04($case);
						$result = $helper->compile();
						$documentsToPrint[] = new ContentPDFDocument($result);

						$helper = new \OpakeAdmin\Helper\Billing\Coding\CMS1500($case);
						$result = $helper->compile();
						$documentsToPrint[] = new ContentPDFDocument($result);
					}
			}

			if (!$documentsToPrint) {
				throw new \Exception('Document for print list is empty');
			}

			$helper = new \OpakeAdmin\Helper\Printing\PrintCompiler();
			$result = $helper->compile($documentsToPrint);

			$this->pixie->activityLogger
				->newAction(ActivityRecord::ACTION_PAPER_CLAIMS_PRINT)
				->setArray(['cases' => $caseIds, 'patients' => $patients])
				->register();

			$this->result = [
				'success' => true,
				'id' => $result->id(),
				'url' => $result->getResultUrl()
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
		}
	}
}
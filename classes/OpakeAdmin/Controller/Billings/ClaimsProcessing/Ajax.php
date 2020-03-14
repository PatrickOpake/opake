<?php

namespace OpakeAdmin\Controller\Billings\ClaimsProcessing;

use Opake\Exception\BadRequest;
use Opake\Exception\PageNotFound;
use Opake\Model\Billing\Navicure\Claim;
use Opake\Model\Billing\Navicure\Payment;
use OpakeAdmin\Service\Navicure\Claims\ClaimGenerator;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionBunches()
	{
		$items = [];

		$search = new \OpakeAdmin\Model\Search\Billing\ClaimsProcessing\Bunch($this->pixie);
		$search->setOrganizationId($this->org->id());
		$results = $search->search(
			$this->orm->get('Billing_Navicure_Payment_Bunch'),
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

	public function actionPayments()
	{
		$items = [];

		$search = new \OpakeAdmin\Model\Search\Billing\ClaimsProcessing\Payment($this->pixie);
		$search->setBunchId($this->request->param('subid'));
		$results = $search->search(
			$this->orm->get('Billing_Navicure_Payment'),
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

	public function actionPaymentsTable()
	{
		$items = [];

		$search = new \OpakeAdmin\Model\Search\Billing\ClaimsProcessing\Payment($this->pixie);
		$search->setTableType($this->request->param('subid'));
		$results = $search->search(
			$this->orm->get('Billing_Navicure_Payment'),
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

	public function actionChangeBunchStatus()
	{
		$bunchId = $this->request->param('subid');
		$newStatus = $this->request->post('newStatus');

		$bunch = $this->orm->get('Billing_Navicure_Payment_Bunch')
			->where('id', $bunchId)
			->where('organization_id', $this->org->id())
			->find();

		if (!$bunch->loaded()) {
			throw new PageNotFound();
		}

		$bunch->status = $newStatus;
		$bunch->save();

		$this->result = [
			'success' => true
		];
	}

	public function actionCheckBunchProcessed()
	{
		$bunchId = $this->request->param('subid');

		$bunch = $this->orm->get('Billing_Navicure_Payment_Bunch')
			->where('id', $bunchId)
			->where('organization_id', $this->org->id())
			->find();

		if (!$bunch->loaded()) {
			throw new PageNotFound();
		}

		$isProcessed = true;
		foreach ($bunch->payments->find_all() as $payment) {
			if ($payment->status != Payment::STATUS_PROCESSED && $payment->status != Payment::STATUS_RESUBMITTED) {
				$isProcessed = false;
				break;
			}
		}

		if ($isProcessed) {
			$bunch->status = Payment\Bunch::STATUS_PROCESSED;
			$bunch->save();
		}

		$this->result = [
			'success' => true
		];
	}

	public function actionResubmitClaims()
	{
		$data = $this->getData();

		if (empty($data->payments)) {
			throw new BadRequest('Bad Request');
		}

		foreach ($data->payments as $paymentId) {
			$paymentModel = $this->orm->get('Billing_Navicure_Payment', $paymentId);
			if ($paymentModel->loaded()) {
				if ($paymentModel->claim->case->organization_id == $this->org->id()) {
					try {

						if ($paymentModel->claim->type == Claim::TYPE_ELECTRONIC_1500_CLAIM) {
							$chunkedBills = ClaimGenerator::splitClaims($paymentModel->claim->case);
							foreach ($chunkedBills as $chunkedBill) {
								$generator = new \OpakeAdmin\Service\Navicure\Claims\Generator\ProfessionalClaimGenerator($paymentModel->claim->case, $chunkedBill);
								$generator->tryToSendClaim();
							}
						} else if ($paymentModel->claim->type == Claim::TYPE_ELECTRONIC_UB04_CLAIM) {
							$chunkedBills = ClaimGenerator::splitClaims($paymentModel->claim->case);
							foreach ($chunkedBills as $chunkedBill) {
								$generator = new \OpakeAdmin\Service\Navicure\Claims\Generator\InstitutionalClaimGenerator($paymentModel->claim->case, $chunkedBill);
								$generator->tryToSendClaim();
							}
						} else {
							throw new \Exception('Can\'t resubmit a claim, unknown type');
						}

						$paymentModel->status = Payment::STATUS_RESUBMITTED;
						$paymentModel->save();

					} catch (\Exception $e) {
						$this->pixie->logger->exception($e);
					}
				}
			}
		}

		$this->result = [
			'success' => true
		];
	}

	public function actionProcessClaims()
	{
		$data = $this->getData();

		if (empty($data->payments)) {
			throw new BadRequest('Bad Request');
		}

		foreach ($data->payments as $paymentId) {
			$paymentModel = $this->orm->get('Billing_Navicure_Payment', $paymentId);
			if ($paymentModel->loaded()) {
				if ($paymentModel->claim->case->organization_id == $this->org->id()) {
					$paymentModel->status = Payment::STATUS_PROCESSED;
					$paymentModel->save();
				}
			}
		}

		$this->result = [
			'success' => true
		];
	}

	public function actionChangePaymentStatus()
	{
		$paymentId = $this->request->param('subid');
		$newStatus = $this->request->post('newStatus');

		$payment = $this->orm->get('Billing_Navicure_Payment', $paymentId);

		if (!$payment->loaded()) {
			throw new PageNotFound();
		}

		$payment->status = $newStatus;
		$payment->save();

		$this->result = [
			'success' => true
		];
	}
}
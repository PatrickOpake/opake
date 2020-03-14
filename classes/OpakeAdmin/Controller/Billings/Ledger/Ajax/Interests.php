<?php

namespace OpakeAdmin\Controller\Billings\Ledger\Ajax;

use Opake\Exception\BadRequest;
use Opake\Exception\Forbidden;
use Opake\Exception\PageNotFound;

class Interests extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionApplyInterests()
	{
		$this->checkAccess('billing', 'view');
		$this->pixie->db->begin_transaction();
		try {

			$data = $this->getData(true);
			$interests = $data['interests'];

			if ($interests) {
				foreach ($interests as $interestData) {
					$interestModel = $this->pixie->orm->get('Billing_Ledger_InterestPayment');
					$interestModel->case_id = $interestData['case_id'];
					$interestModel->amount = $interestData['amount'];
					$interestModel->date = $interestData['date'];
					$interestModel->save();
				}
			}

			$this->pixie->db->commit();

			$this->result = [
				'success' => true
			];

		} catch (\Exception $e) {
			$this->pixie->db->rollback();
			$this->logSystemError($e);

			$this->result = [
				'success' => false,
			    'errors' => [$e->getMessage()]
			];
		}
	}

	public function actionRemoveInterest()
	{
		$this->checkAccess('billing', 'view');
		$data = $this->getData(true);
		$id = $data['id'];

		if (!$id) {
			throw new BadRequest;
		}

		$interestModel = $this->pixie->orm->get('Billing_Ledger_InterestPayment', $id);
		if (!$interestModel->loaded()) {
			throw new PageNotFound;
		}
		if ($interestModel->case->organization_id != $this->org->id()) {
			throw new Forbidden;
		}

		$interestModel->delete();

		$this->result = [
			'success' => true
		];

	}

	public function actionUpdateInterest()
	{
		$this->checkAccess('billing', 'view');
		$data = $this->getData(true);

		if (!isset($data['id'], $data['amount'])) {
			throw new BadRequest;
		}

		$payment = $this->orm->get('Billing_Ledger_InterestPayment', $data['id']);
		if (!$payment->loaded()) {
			throw new PageNotFound;
		}
		if ($payment->case->organization_id != $this->org->id()) {
			throw new Forbidden;
		}

		$payment->amount = $data['amount'];
		$payment->save();

		$this->result = [
			'success' => true
		];
	}
}
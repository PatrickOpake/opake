<?php

namespace OpakeAdmin\Controller\Billings\LedgerPaymentActivity;

use Opake\Exception\BadRequest;
use Opake\Helper\StringHelper;
use OpakeAdmin\Helper\Export\PaymentActivityExport;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$this->checkAccess('billing', 'view');

		$search = new \OpakeAdmin\Model\Search\Billing\LedgerPaymentActivity($this->pixie);
		$search->setOrganizationId($this->org->id());
		$models = $search->search(
			$this->pixie->orm->get('Billing_Ledger_AppliedPayment'),
			$this->request
		);

		$result = [];
		foreach ($models as $model) {
			$result[] = $model->getFormatter('PaymentActivityListEntry')->toArray();
		}

		$this->result = [
			'items' => $result,
			'total_count' => $search->getPagination()->getCount()
		];
	}


	public function actionExport()
	{
		$this->checkAccess('billing', 'view');

		$search = new \OpakeAdmin\Model\Search\Billing\LedgerPaymentActivity($this->pixie, false);
		$search->setOrganizationId($this->org->id());
		$models = $search->search(
			$this->pixie->orm->get('Billing_Ledger_AppliedPayment'),
			$this->request
		);

		$export = new PaymentActivityExport($this->pixie);
		$export->setModels($models);
		$xls = $export->exportToExcel();

		$this->result = [
			'success' => true,
			'url' => $xls->getWebPath()
		];
	}
}
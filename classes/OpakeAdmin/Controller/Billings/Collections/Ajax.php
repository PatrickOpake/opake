<?php

namespace OpakeAdmin\Controller\Billings\Collections;

use OpakeAdmin\Helper\Export\CollectionListExport;

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

		$model = $this->orm->get('Cases_Item')
			->where('organization_id', $this->org->id)
			->where('and', [
				['or', ['appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED]],
				['or', ['is_remained_in_billing', 1]]
			]);

		$search = new \OpakeAdmin\Model\Search\Billing\Collections($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->getFormatter('CollectionList')->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionExportCollection()
	{
		$model = $this->orm->get('Cases_Item')
			->where('organization_id', $this->org->id)
			->where('and', [
				['or', ['appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED]],
				['or', ['is_remained_in_billing', 1]]
			]);

		$search = new \OpakeAdmin\Model\Search\Billing\Collections($this->pixie, false);
		$collections = $search->search($model, $this->request);
		$export = new CollectionListExport($this->pixie);
		$export->setModels($collections);
		$xls = $export->exportToExcel();

		$this->result = [
			'success' => true,
			'url' => $xls->getWebPath()
		];
	}

	public function actionSaveBillingStatuses()
	{
		$data = $this->getData();

		try {
			$this->pixie->db->begin_transaction();
			foreach ($data as $item) {
				$case = $this->orm->get('Cases_Item', $item->case_id);
				if ($case->loaded()) {
					if ($case->organization_id == $this->org->id()) {
						$case->billing_status = $item->billing_status;
						$case->save();
					}
				}
			}

			$this->pixie->db->commit();

			$this->result = [
				'success' => true,
			];
		} catch (\Exception $e) {
			$this->pixie->logger->exception($e);
			$this->pixie->db->rollback();
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()],
			];
			return;
		}
	}

}

<?php

namespace OpakeAdmin\Controller\Orders\Ajax;

use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Order\Outgoing\Mail\Receiver;

class Outgoing extends \OpakeAdmin\Controller\Ajax {

	protected $_service;

	public function before() {
		parent::before();
		$this->iniOrganization($this->request->param('id'));

		$this->_service = $this->services->get('orders_outgoing');
	}

	protected function getOrder() {
		$model = $this->_service->getItem($this->request->param('subid'));

		if (!$model->loaded() || $model->organization_id !== $this->org->id) {
			throw new \Exception('Not found');
		}
		return $model;
	}

	protected function getOrderItem() {
		$model = $this->_service->getOrderItem($this->request->param('subid'));

		if (!$model->loaded() || $model->order->organization_id !== $this->org->id) {
			throw new \Exception('Not found');
		}
		return $model;
	}

	public function actionIndex() {

		$items = [];

		$model = $this->_service->getItem()->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Order\Outgoing($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->toShortArray();
		}

		$this->result = [
		    'items' => $items,
		    'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionOrder() {
		$this->result = $this->getOrder()->toArray();
	}

	public function actionSave() {

		$ids = $this->request->post('items');
		$order = $this->_service->getItem($this->request->param('subid', ''));

		if (!$order->loaded()) {
			$order->organization_id = $this->org->id;
			$order->save();
		}

		if ($ids) {
			$items = $this->services->get('inventory')->getListByIds($ids);

			foreach ($items as $item) {
				if ($item->organization_id !== $this->org->id) {
					throw new \Exception('Not found ' . $item->id);
				}
				$this->_service->addItems($order, $items);
				$this->services->get('alert')->deleteByInventory(\Opake\Model\Alert\Alert::TYPE_LOW_INVENTORY, $ids);
			}
		}
		$this->result = [
		    'id' => (int) $order->id
		];
	}

	public function actionDelete() {
		$order = $this->getOrder();
		if ($order->isActive()) {
			$order->delete();
			$this->result = 'ok';
		}
	}

	public function actionUpdateCount() {
		$model = $this->getOrderItem();

		if ($model->order->isActive()) {
			$count = $this->request->get('count');
			if ($count) {
				$this->_service->updateCount($model, $count);
			}
			$this->result = 'ok';
		}
	}

	public function actionDeleteItem() {
		$model = $this->getOrderItem();
		if ($model->order->isActive()) {
			$model->delete();
			$this->result = 'ok';
		}
	}

	public function actionPlace()
	{
		$order = $this->getOrder();
		$emails = $this->getData();

		foreach ($emails as $data) {
			if (isset($data->to)) {
				$vendorId = ((isset($data->vendor->vendor)) ? $data->vendor->vendor->id : $data->vendor->id);
				$vendor = $this->orm->get('Vendor', $vendorId);

				if (!$vendor->loaded() || $vendor->organization_id !== $this->org->id) {
					throw new \Opake\Exception\Ajax('Not found');
				}

				$view = $this->pixie->view('orders/outgoing/export');
				$view->org = $this->org;
				$view->vendor = $vendor;
				$view->list = $order->groups->where('vendor_id', $vendor->id)->find()->items->find_all();

				$filename = $this->pixie->app_dir . '_tmp/order_' . $order->id . '_' . $vendor->id . '.pdf';
				list($pdf, $errors) = \Opake\Helper\Export::pdf($view->render(), $filename);

				try {
					\Opake\Helper\Mailer::send($data, $this->logged(), true, $filename);
				} catch (\Exception $e) {
					throw new \Opake\Exception\Ajax($e->getMessage());
				} finally {
					if (is_file($filename)) {
						unlink($filename);
					}
				}
			}
		}
		$order->date = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB);
		$order->save();

		$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_INVENTORY_CREATE_ORDER)
			->setModel($order)
			->register();
	}

	public function actionSaveWithoutSending()
	{
		$order = $this->getOrder();
		$orderRequestData = $this->getData();

		$mailModelQuery = $this->orm->get('Order_Outgoing_Mail');
		$mailModelQuery->where('order_outgoing_id', $order->id);
		foreach($mailModelQuery->find_all() as $mailModel) {
				$id = $mailModel->id();
				$mailModel->delete();

				$receiverModelQuery = $this->orm->get('Order_Outgoing_Mail_Receiver');
				$receiverModelQuery->where('order_outgoing_mail_id', $id);
				$receiverModelQuery->delete_all();
		}
		foreach ($orderRequestData as $data) {
			if (isset($data->to)) {

				/** @var \Opake\Model\Order\Outgoing\Mail $mailModel */
				$mailModel = $this->orm->get('Order_Outgoing_Mail');
				$mailModel->order_outgoing_id = $order->id;
				if(isset($data->subject)) {
					$mailModel->subject = $data->subject;
				}
				if(isset($data->body)) {
					$mailModel->body = $data->body;
				}
				$mailModel->save();

				if (!empty($data->to) && is_array($data->to)) {
					foreach ($data->to as $addr) {
						/** @var Receiver $receiverModel */
						$receiverModel = $this->orm->get('Order_Outgoing_Mail_Receiver');
						$receiverModel->email = $addr;
						$receiverModel->receiver_type = Receiver::TYPE_TO;
						$receiverModel->order_outgoing_mail_id = $mailModel->id();
						$receiverModel->save();
					}
				} else {
					$receiverModel = $this->orm->get('Order_Outgoing_Mail_Receiver');
					$receiverModel->email = $data->to;
					$receiverModel->receiver_type = Receiver::TYPE_TO;
					$receiverModel->order_outgoing_mail_id = $mailModel->id();
					$receiverModel->save();
				}

				if (!empty($data->cc)) {
					foreach ($data->cc as $addr) {
						/** @var Receiver $receiverModel */
						$receiverModel = $this->orm->get('Order_Outgoing_Mail_Receiver');

						$receiverModel->email = $addr;
						$receiverModel->receiver_type = Receiver::TYPE_CC;
						$receiverModel->order_outgoing_mail_id = $mailModel->id();
						$receiverModel->save();
					}
				}

				if (!empty($data->bcc)) {
					foreach ($data->bcc as $addr) {
						/** @var Receiver $receiverModel */
						$receiverModel = $this->orm->get('Order_Outgoing_Mail_Receiver');

						$receiverModel->email = $addr;
						$receiverModel->receiver_type = Receiver::TYPE_BCC;
						$receiverModel->order_outgoing_mail_id = $mailModel->id();
						$receiverModel->save();
					}
				}
			}
		}
	}

	public function actionExport()
	{
		$order = $this->getOrder();
		$vendor = $this->orm->get('Vendor', trim($this->request->get('vendor_id')));

		if (!$vendor->loaded() || $vendor->organization_id !== $this->org->id) {
			throw new \Opake\Exception\Ajax('Not found');
		}

		$filename = 'order_export_' . $order->id . '_' . $vendor->id . '.pdf';
		$view = $this->pixie->view('orders/outgoing/export');
		$view->org = $this->org;
		$view->vendor = $vendor;
		$view->list = $order->groups->where('vendor_id', $vendor->id)->find()->items->find_all();
		list($pdf, $errors) = \Opake\Helper\Export::pdf($view->render());

		if ($errors) {
			throw new \Opake\Exception\Ajax('PDF generation failed: ' . $errors);
		} else {
			$this->response->file('application/pdf', $filename, $pdf);
			$this->execute = false;
		}
	}

}

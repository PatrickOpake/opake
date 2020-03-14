<?php

namespace OpakeAdmin\Controller\Orders\Ajax;

use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Order;

class Received extends \OpakeAdmin\Controller\Ajax {

	protected $_service;

	public function before() {
		parent::before();
		$this->iniOrganization($this->request->param('id'));
		$this->_service = $this->services->get('orders');
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

		$service = $this->_service;
		$model = $service->getItem()->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Order\Received($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->toArray();
		}

		$this->result = [
		    'items' => $items,
		    'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionOrder() {
		$this->result = $this->getOrder()->toArray();
	}

	public function actionSearchItems() {
		$items = [];

		$model =  $this->pixie->orm->get('Order_Item');
		$search = new \OpakeAdmin\Model\Search\Order\Received($this->pixie);
		$results = $search->searchItems($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionInventoryList() {
		$result = [];
		$ids = $this->request->post('items');

		if ($ids) {
			$items = $this->services->get('inventory')->getListByIds($ids);
			foreach ($items as $item) {
				$result[] = $item->toShortArray();
			}
		}

		$this->result = [
			'inventories' => $result,
		];
	}

	public function actionSaveOrder() {
		$data = $this->request->post('order');
		$order = $this->_service->getItem($this->request->param('subid', ''));
		$this->updateModel($order, $data);

		$items = $data['items'];
		if (!empty($items)) {
			foreach ($items as $item) {
				$orderItem = $this->_service->getOrderItem($item['id']);
				$orderItem->fill($item);
				$orderItem->save();
			}
		}

		$this->result = [
			'id' => (int) $order->id
		];
	}

	public function actionSaveOutsideOrder() {
		$data = $this->request->post('order');
		$order = $this->_service->getItem();
		$order->organization_id = $this->org->id;

		if(empty($data['items'])) {
			throw new \Opake\Exception\Ajax('You should add at least one item');
		}

		//TODO: Сделать валидацию required в модели, только для этого экшона
		if(empty($data['po_id'])) {
			throw new \Opake\Exception\Ajax('The P.O.# field is required');
		}

		$this->_service->beginTransaction();
		try {
			$this->updateModel($order, $data);
			$items = $data['items'];
			if (!empty($items)) {
				foreach ($items as $item) {
					$orderItem = $this->pixie->orm->get('Order_Item');
					$orderItem->order_id = $order->id;
					$orderItem->fill($item);
					$orderItem->ordered = $orderItem->received;
					$orderItem->save();
				}
			}
		} catch (\Exception $e) {
			$this->logSystemError($e);

			$this->_service->rollback();
			throw new \Opake\Exception\Ajax($e->getMessage());
		}
		$this->_service->commit();

		$this->result = [
			'id' => (int) $order->id
		];
	}

	public function actionGetStatuses() {
		$this->result = Order::getStatuses();
	}

	public function actionOrderReceived()
	{
		$order = $this->_service->getItem($this->request->param('subid'));
		if ($order && $order->loaded()) {
			$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_INVENTORY_RECEIVE_ORDER)
				->setModel($order)
				->register();

			$this->result = 'ok';
		}
	}

	public function actionShippingTypes() {
		$items = [];
		foreach ($this->services->get('settings')->getList('Order_ShippingType') as $type) {
			$items[] = [
				'id' => $type->id,
				'name' => $type->name
			];
		}
		$this->result = $items;
	}

	public function actionOrderItem() {
		$orderItem = $this->getOrderItem($this->request->param('subid'));
		$this->result = $orderItem->toArray();
	}

	public function actionIsPOUnique() {
		$po_id = $this->request->get('po_id');
		$order = $this->_service->getItem($this->request->param('subid', ''));
		$count = $order->where('po_id', $po_id)->count_all();
		$this->result = [
			'exist' => (int) $count
		];
	}

}

<?php

namespace Opake\Service;

use Opake\Model\Analytics\UserActivity\ActivityRecord;

class Cards extends AbstractService
{

	protected $logChanges = false;

	/**
	 * @return boolean
	 */
	public function isLogChanges()
	{
		return $this->logChanges;
	}

	/**
	 * @param boolean $logChanges
	 */
	public function setLogChanges($logChanges)
	{
		$this->logChanges = $logChanges;
	}


	public function updateItems($model, $items_data, $isNewCard = false)
	{
		$oldItems = [];
		foreach ($model->items->find_all() as $itemModel) {
			$oldItems[$itemModel->id()] = $itemModel;
		}

		$model->items->delete_all();

		$newItems = [];
		foreach ($items_data as $data) {
			$item = $this->orm->get($this->base_model . '_Item');
			$item->card_id = $model->id;
			$item->fill($data);

			$action = null;
			if ($this->isLogChanges()) {
				if (!empty($data->id) && isset($oldItems[$data->id])) {
					$action = $this->pixie->activityLogger->newAction(ActivityRecord::ACTION_CLINICAL_EDIT_INVENTORY_ITEM);
					$action->setNewAndOldModels($item, $oldItems[$data->id]);
				} else {
					$action = $this->pixie->activityLogger->newAction(ActivityRecord::ACTION_CLINICAL_ADD_INVENTORY_ITEM);
					$action->setModel($item);
				}
			}

			$item->save();

			if ($action) {
				$action->register();
			}

			$newItems[$item->id()] = $item;
		}

		if ($isNewCard && isset($model->case_id) && ($this->base_model == 'Card_Staff')) {
			$this->createRequestedItems($model);
		}

		foreach ($oldItems as $itemId => $item) {
			if (!in_array($itemId, array_keys($newItems))) {
				if ($this->isLogChanges()) {
					$action = $this->pixie->activityLogger->newAction(ActivityRecord::ACTION_CLINICAL_REMOVE_INVENTORY_ITEM);
					$action->setModel($item);
					$action->register();
				}
			}
		}
	}

	public function updateNotes($model, $notes_data)
	{

		$oldItems = [];
		foreach ($model->notes->find_all() as $itemModel) {
			$oldItems[$itemModel->id()] = $itemModel;
		}

		$model->notes->delete_all();

		$newItems = [];
		foreach ($notes_data as $data) {
			$item = $this->orm->get($this->base_model . '_Note');
			$item->card_id = $model->id;
			$item->fill($data);

			$action = null;
			if ($this->isLogChanges()) {
				if (!empty($data->id) && isset($oldItems[$data->id])) {
					$action = $this->pixie->activityLogger->newAction(ActivityRecord::ACTION_CLINICAL_EDIT_CHECKLIST_ITEM);
					$action->setNewAndOldModels($item, $oldItems[$data->id]);
				} else {
					$action = $this->pixie->activityLogger->newAction(ActivityRecord::ACTION_CLINICAL_ADD_CHECKLIST_ITEM);
					$action->setModel($item);
				}
			}

			$item->save();

			if ($action) {
				$action->register();
			}

			$newItems[$item->id()] = $item;
		}

		foreach ($oldItems as $itemId => $item) {
			if (!in_array($itemId, array_keys($newItems))) {
				if ($this->isLogChanges()) {
					$action = $this->pixie->activityLogger->newAction(ActivityRecord::ACTION_CLINICAL_REMOVE_CHECKLIST_ITEM);
					$action->setModel($item);
					$action->register();
				}
			}
		}
	}

	protected function createRequestedItems($card)
	{
		$requestedItemsStageId = $this->getRequestedItemsStageId();

		foreach ($card->case->equipments->find_all() as $equipment) {
			$item = $this->orm->get('Card_Staff_Item');
			$item->card_id = $card->id;
			$item->inventory_id = $equipment->id;
			$item->stage_id = $requestedItemsStageId;
			$item->default_qty = 1;
			$item->save();
		}

		foreach ($card->case->implant_items->find_all() as $implant) {
			$item = $this->orm->get('Card_Staff_Item');
			$item->card_id = $card->id;
			$item->inventory_id = $implant->id;
			$item->stage_id = $requestedItemsStageId;
			$item->default_qty = 1;
			$item->save();
		}
	}

	protected function getRequestedItemsStageId()
	{
		$query = $this->pixie->db->query('select')
			->table('pref_card_stage')
			->fields('id')
			->where(['is_requested_items', 1])
			->execute()
			->current();

		return $query->id;
	}
}

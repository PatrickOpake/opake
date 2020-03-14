<?php

namespace OpakeAdmin\Helper\Printing\Document\PrefCard;

use Opake\Model\PrefCard\Staff;
use OpakeAdmin\Helper\Printing\Document\PDFCompileDocument;

class StaffPrefCard extends PDFCompileDocument
{
	/**
	 * @var Staff
	 */
	protected $card;

	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var \Opake\Model\User
	 */
	protected $user;

	/**
	 * @param Staff $card
	 * @param \Opake\Model\Cases\Item $case
	 * @param \Opake\Model\User $user
	 */
	public function __construct($card, $case = null, $user = null)
	{
		$this->card = $card;
		$this->case = $case;
		$this->user = $user;
	}

	public function getFileName()
	{
		$filename = 'Staff_Pref_Card_' . $this->card->id();
		$filename .= '.pdf';

		return $filename;
	}

	protected function generateView()
	{
		$app = \Opake\Application::get();
		$view = $app->view('cards/export/staff_pref_card');
		$card = $this->card;
		$view->card = $card;
		$view->notes = $card->notes
			->find_all()
			->as_array();

		$items = $card->items->find_all()->as_array();
		if (count($items)) {
			uasort(
				$items,
				function ($item1, $item2) {
					if (!isset($item1->position)) {
						return 1;
					}
					if (!isset($item2->position)) {
						return -1;
					}
					if ($item1->position == $item2->position) {
						return ($item1->id < $item2->id) ? -1 : 1;
					}

					return ($item1->position < $item2->position) ? -1 : 1;
				}
			);
		}

		$stages = $app->orm->get('PrefCard_Stage')->find_all();
		$stagesWithItems = [];
		$stagePositions = $card->getStages(true);
		foreach ($stages as $stage) {
			$stageWithItems = [
				'stage_name' => $stage->name,
				'items' => []
			];
			if (isset($stagePositions[$stage->id]) && isset($stagePositions[$stage->id]['position'])) {
				$stageWithItems['position'] = $stagePositions[$stage->id]['position'];
			}
			foreach ($items as $item) {
				if ($item->stage_id == $stage->id) {
					$stageWithItems['items'][] = $item;
				}
			}
			$stagesWithItems[] = $stageWithItems;
		}
		$itemsWithoutStage = [
			'stage_name' => 'Items without stage',
			'items' => []
		];
		if (isset($stagePositions['null']) && isset($stagePositions['null']['position'])) {
			$itemsWithoutStage['position'] = $stagePositions['null']['position'];
		}
		foreach ($items as $item) {
			if (!$item->stage_id || !$item->stage->loaded()) {
				$itemsWithoutStage['items'][] = $item;
			}
		}
		$stagesWithItems[] = $itemsWithoutStage;

		if (count($stagePositions)) {
			uasort(
				$stagesWithItems,
				function ($item1, $item2) {
					if (!isset($item1['position'])) {
						return 1;
					}
					if (!isset($item2['position'])) {
						return -1;
					}

					return ($item1['position'] < $item2['position']) ? -1 : 1;
				}
			);
		}

		$view->stages_with_items = $stagesWithItems;
		if ($this->case) {
			$view->case = $this->case;
		}
		if ($this->user) {
			$view->user = $this->user;
		}

		return $view;
	}
}
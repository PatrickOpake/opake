<?php

namespace OpakeAdmin\Helper\Analytics\Reports\CasesReport\RowSource;

class InventoryItemRowSource extends CaseCancellationRowSource
{
	/**
	 * @var \Opake\Model\Inventory
	 */
	protected $inventoryItem;

	/**
	 * @var int
	 */
	protected $inventoryDefaultQuantity;

	/**
	 * @var int
	 */
	protected $inventoryActualUse;

	/**
	 * @param \Opake\Model\Cases\Item $case
	 * @param \Opake\Model\Card\Staff\Item | \Opake\Model\PrefCard\Staff\Item $caseCardItem
	 */
	public function __construct($case, $cancellation, $caseCardItem)
	{
		parent::__construct($case, $cancellation);

		$this->inventoryItem = $caseCardItem->inventory;
		$this->inventoryDefaultQuantity = (int) $caseCardItem->default_qty;
		if ($caseCardItem instanceof \Opake\Model\Card\Staff\Item) {
			$this->inventoryActualUse = (int) $caseCardItem->actual_use;
		}
	}

	/**
	 * @return \Opake\Model\Inventory
	 */
	public function getInventoryItem()
	{
		return $this->inventoryItem;
	}

	/**
	 * @param \Opake\Model\Inventory $inventoryItem
	 */
	public function setInventoryItem($inventoryItem)
	{
		$this->inventoryItem = $inventoryItem;
	}

	/**
	 * @return int
	 */
	public function getInventoryDefaultQuantity()
	{
		return $this->inventoryDefaultQuantity;
	}

	/**
	 * @param int $inventoryDefaultQuantity
	 */
	public function setInventoryDefaultQuantity($inventoryDefaultQuantity)
	{
		$this->inventoryDefaultQuantity = $inventoryDefaultQuantity;
	}

	/**
	 * @return int
	 */
	public function getInventoryActualUse()
	{
		return $this->inventoryActualUse;
	}

	/**
	 * @param int $inventoryActualUse
	 */
	public function setInventoryActualUse($inventoryActualUse)
	{
		$this->inventoryActualUse = $inventoryActualUse;
	}
}
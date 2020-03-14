<?php

namespace OpakeAdmin\Service\Navicure\Claims;

use OpakeAdmin\Service\Navicure\Claims\Procedures\ClaimProceduresContainer;

class ProceduresSplitter
{

	const MAX_CHARGES_SUM = 99999.99;
	const MAX_CLAIMS = 100;

	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @param \Opake\Model\Cases\Item $case
	 */
	public function __construct(\Opake\Model\Cases\Item $case)
	{
		$this->case = $case;
	}

	public function makeClaimProcedureContainers()
	{
		$containers = [];
		foreach ($this->getCaseBillsByOneQuantity() as $bill) {
			/** @var ClaimProceduresContainer $container */
			foreach ($containers as $container) {
				if (($container->getTotalSum() + (float) $bill->charge) <= static::MAX_CHARGES_SUM) {
					$container->addBill($bill);
					continue 2;
				}
			}
			$container = new ClaimProceduresContainer();
			$container->addBill($bill);
			$containers[] = $container;
		}

		if (count($containers) > static::MAX_CLAIMS) {
			throw new \Exception("Count of claims for the case more than " . static::MAX_CLAIMS);
		}

		return $containers;
	}

	protected function getCaseBillsByOneQuantity()
	{
		if ($this->case->coding->loaded()) {
			foreach ($this->case->coding->bills->find_all() as $bill) {
				if ($bill->quantity !== '' && $bill->quantity !== null) {
					for ($i = 1; $i <= $bill->quantity; $i++) {
						yield $bill;
					}
				} else {
					yield $bill;
				}
			}
		}
	}
}
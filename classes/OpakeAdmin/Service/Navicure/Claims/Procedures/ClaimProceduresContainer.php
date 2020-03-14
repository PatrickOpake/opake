<?php

namespace OpakeAdmin\Service\Navicure\Claims\Procedures;

class ClaimProceduresContainer
{
	/**
	 * @var array
	 */
	protected $billDetails = [];

	/**
	 * @var float
	 */
	protected $totalSum = 0;

	/**
	 * @param \Opake\Model\Cases\Coding\Bill $bill
	 */
	public function addBill($bill)
	{
		$this->totalSum += (float) $bill->charge;

		foreach ($this->billDetails as $index => $billData) {
			if ($this->billDetails[$index]['bill'] === $bill) {
				$this->billDetails[$index]['quantity'] += 1;
				return;
			}
		}

		$this->billDetails[] = [
			'bill' => $bill,
		    'quantity' => 1
		];
	}

	/**
	 * @return float
	 */
	public function getTotalSum()
	{
		return $this->totalSum;
	}

	/**
	 * @return array
	 */
	public function getBillsWithQuantities()
	{
		$result = [];
		foreach ($this->billDetails as $billData) {
			$result[] = [$billData['bill'], $billData['quantity']];
		}

		return $result;
	}
}
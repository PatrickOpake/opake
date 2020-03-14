<?php

namespace OpakeAdmin\Helper\Analytics\Reports\CasesReport\RowSource;

class ProcedureRowSource extends BaseCaseRowSource
{
	/**
	 * @var \Opake\Model\Cases\Coding\Bill
	 */
	protected $bill;

	/**
	 * @param \Opake\Model\Cases\Item $case
	 * @param \Opake\Model\Cases\Coding\Bill $bill
	 */
	public function __construct($case, $bill)
	{
		parent::__construct($case);

		$this->bill = $bill;
	}

	/**
	 * @return \Opake\Model\Cases\Coding\Bill
	 */
	public function getBill()
	{
		return $this->bill;
	}

	/**
	 * @param \Opake\Model\Cases\Coding\Bill $bill
	 */
	public function setBill($bill)
	{
		$this->bill = $bill;
	}
}
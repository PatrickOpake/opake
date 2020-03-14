<?php

namespace OpakeAdmin\Helper\Analytics\Reports\CasesReport\RowSource;

class CaseCancellationRowSource extends BaseCaseRowSource
{
	/**
	 * @var \Opake\Model\Cases\Cancellation
	 */
	protected $caseCancellation;

	/**
	 * @param \Opake\Model\Cases\Item $case
	 * @param \Opake\Model\Cases\Cancellation $caseCancellation
	 */
	public function __construct($case, $caseCancellation)
	{
		parent::__construct($case);

		$this->caseCancellation = $caseCancellation;
	}

	/**
	 * @return \Opake\Model\Cases\Cancellation
	 */
	public function getCaseCancellation()
	{
		return $this->caseCancellation;
	}

	/**
	 * @param \Opake\Model\Cases\Cancellation $caseCancellation
	 */
	public function setCaseCancellation($caseCancellation)
	{
		$this->caseCancellation = $caseCancellation;
	}
}
<?php

/*
 * Abstract billing/coding form
 */

namespace OpakeAdmin\Helper\Billing\Coding;

class AbstractForm
{

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var \FPDI
	 */
	protected $pdf;

	/**
	 * @param \Opake\Model\Cases\Item $case
	 */
	public function __construct($case)
	{
		$this->pixie = \Opake\Application::get();
		$this->case = $case;

		// initiate FPDI
		$this->pdf = new \FPDI();
	}

}

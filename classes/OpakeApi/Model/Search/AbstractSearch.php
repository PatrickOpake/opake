<?php

namespace OpakeApi\Model\Search;

abstract class AbstractSearch
{

	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	 */
	public $pixie;

	/**
	 * Params
	 * @var array
	 */
	protected $_params = [];

	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}

	abstract public function search($request);
}

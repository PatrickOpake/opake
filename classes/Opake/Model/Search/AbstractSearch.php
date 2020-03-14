<?php

namespace Opake\Model\Search;

use Opake\Helper\Pagination;

abstract class AbstractSearch
{

	/**
	 * Pixie Dependency Container
	 * @var \Opake\Application
	 */
	public $pixie;

	/**
	 * Params
	 * @var array
	 */
	protected $_params = [];

	/**
	 * Pagination
	 * @var Pagination
	 */
	protected $_pagination;

	public function __construct($pixie, $pagination = true)
	{
		$this->pixie = $pixie;
		if ($pagination) {
			$this->_pagination = new Pagination();
		}
	}

	public function getParams()
	{
		return $this->_params;
	}

	public function getPagination()
	{
		return $this->_pagination;
	}

	protected function prepare($main_model, $request)
	{
		if ($this->_pagination) {
			$this->_pagination->setPage($request->get('p'));
			$this->_pagination->setLimit($request->get('l'));
		}

		$class = get_class($main_model);
		$model = new $class($this->pixie);
		$model->query = clone $main_model->query;
		return $model;
	}

	protected function caseSql($array)
	{
		return implode(' ', array_map(function ($v, $k) {
			return sprintf("when '%s' then '%s'", $k, $v);
		}, $array, array_keys($array)));
	}

	abstract public function search($model, $request);

}

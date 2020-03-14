<?php

namespace Opake\Helper;

class Pagination
{

	/**
	 * Количество на страницу
	 * @var int
	 */
	protected $_limit = 20;

	/**
	 * Общее количесто элементов
	 * @var int
	 */
	protected $_count = 0;

	/**
	 * Номер страницы
	 * @var int
	 */
	protected $_page = 0;

	/**
	 * Варианты для селекта количества элементов на страницу
	 * @var int
	 */
	protected $_limit_options = [10, 20, 30, 40, 50];

	/**
	 * Количество видимых страниц
	 * @var int
	 */
	protected $_visible_pages = 5;


	public function __construct($count = null, $page = null, $limit = null)
	{
		if ($count) {
			$this->_count = $count;
		}
		if ($page) {
			$this->_page = $page;
		}
		if ($limit) {
			$this->_limit = $limit;
		}
	}

	public function setCount($count)
	{
		$this->_count = $count;
	}

	public function setPage($page)
	{
		if ($page) {
			$this->_page = $page;
		}
	}

	public function setLimit($limit)
	{
		if ($limit) {
			$this->_limit = $limit;
		}
	}

	public function getCount()
	{
		return $this->_count;
	}

	public function getLimit()
	{
		return $this->_limit;
	}

	public function getPage()
	{
		return $this->_page;
	}

	public function __toString()
	{
		return '<pages count="' . $this->_count . '" page="' . $this->_page . '" limit="' . $this->_limit . '" ></pages>';
	}

}

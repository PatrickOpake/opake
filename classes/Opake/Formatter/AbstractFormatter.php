<?php

namespace Opake\Formatter;

use Opake\Model\AbstractModel;

abstract class AbstractFormatter
{

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var AbstractModel
	 */
	protected $model;

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * @param AbstractModel $model
	 * @param array $config
	 */
	public function __construct($model, $config = [])
	{
		$this->pixie = \Opake\Application::get();

		$this->model = $model;
		$this->config = $config;

		$this->init();
	}

	abstract public function toArray();

	protected function init()
	{

	}

}
<?php

namespace Opake\Events;

abstract class AbstractListener
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	protected $orm;
	protected $db;
	protected $services;

	public function __construct($pixie)
	{
		$this->orm = $pixie->orm;
		$this->db = $pixie->db;
		$this->services = $pixie->services;

		$this->pixie = $pixie;
	}
}

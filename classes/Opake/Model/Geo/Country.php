<?php

namespace Opake\Model\Geo;

/**
 * Country ORM
 *
 * Database (list of countries) get from https://en.wikipedia.org/wiki/ISO_3166-1_alpha-3
 *
 */
class Country extends \Opake\Model\AbstractModel
{

	const US_ID = 235;

	public $id_field = 'id';
	public $table = 'country';
	protected $_row = [
		'id' => null,
		'name' => '',
		'iso3' => '',
		'priority' => null
	];

	protected $baseFormatter = [
		'class' => '\Opake\Formatter\Geo\Country'
	];

	public function getList()
	{
		return $this->order_by('priority', 'desc')->order_by('name')->find_all();
	}

}

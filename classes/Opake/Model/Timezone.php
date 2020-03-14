<?php

namespace Opake\Model;

/**
 * Timezone ORM
 *
 * Database (list of timezone) get from php function DateTimeZone::listAbbreviations
 *
 */
class Timezone extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'timezone';
	protected $_row = [
		'id' => null,
		'name' => null,
		'code' => null
	];

	const DEFAULT_TIMEZONE = 293; // new york

	public static function getTimezoneList($pixie)
	{
		return $pixie->orm->get('timezone')->order_by('name', 'asc')->find_all()->as_array();
	}

}

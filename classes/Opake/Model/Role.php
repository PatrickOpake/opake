<?php

namespace Opake\Model;

class Role extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'role';

	const FullAdmin = 1;
	const FullClinical = 3;
	const Doctor = 5;
	const SatelliteOffice = 7;
	const Dictation = 9;
	const Biller = 11;
	const Scheduler = 13;

	protected $_row = array(
		'id' => null,
		'name' => null,
	);

	public function toArray()
	{
		return [
			'id' => (int) $this->id,
			'name' => $this->name
		];
	}

}

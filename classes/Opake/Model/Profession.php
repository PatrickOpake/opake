<?php

namespace Opake\Model;

/**
 * User Profession
 *
 */
class Profession extends AbstractModel
{

	const MATERIAL_MANAGER = 1;
	const ADMINISTRATOR = 2;
	const SURGEON = 3;
	const ANESTHESIOLOGIST = 4;
	const NURSE = 5;
	const SCRUB_TECHNOLOGIST = 6;
	const PHYSICIAN_ASSISTANT = 7;
	const NURSE_ANESTHETIST = 8;
	const NURSE_PRACTITIONER = 19;
	const CHIROPRACTOR = 20;
	const DICTATION = 21;
	const BILLER = 22;

	public $id_field = 'id';
	public $table = 'profession';
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

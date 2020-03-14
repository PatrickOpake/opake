<?php

namespace Opake\Model\Insurance\Data;

use Opake\Model\AbstractModel;

class Description extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'insurance_data_description';

	protected $_row = [
		'id' => null,
		'description' => null
	];

	public function getValidator($key = null)
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('description')->rule('filled')->error('You must specify Description');

		return $validator;
	}

	public function fromBaseInsurance(\Opake\Model\Insurance\Data\Description $insurance)
	{
		$this->description = $insurance->description;
	}
}
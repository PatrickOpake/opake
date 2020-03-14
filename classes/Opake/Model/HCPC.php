<?php

namespace Opake\Model;

class HCPC extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'hcpc';
	protected $_row = [
		'id' => null,
		'code' => null,
		'seqnum' => null,
		'recid' => null,
		'long_description' => '',
		'short_description' => '',
		'price' => null,
		'abu' => null
	];

	protected $has_many = [
		'hcpc_years' => [
			'model' => 'HCPCYear',
			'through' => 'hcpc_to_hcpc_year',
			'key' => 'hcpc_id',
			'foreign_key' => 'year_id'
		]
	];

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();

		$validator->field('code')->rule('filled')->error('You must specify HCPC');
		$validator->field('code')->rule('max_length', 10)->error('HCPC must be less than or equal to 10 characters');
		$validator->field('seqnum')->rule('max_length', 10)->error('SEQNUM must be less than or equal to 300 characters');
		$validator->field('recid')->rule('max_length', 10)->error('RECID must be less than or equal to 500 characters');
		$validator->field('long_description')->rule('max_length', 10000)->error('Long Description must be less than or equal to 1000 characters');
		$validator->field('short_description')->rule('max_length', 500)->error('Short Description must be less than or equal to 300 characters');
		$validator->field('abu')->rule('numeric', $this)->error('ABU must be numeric');

		return $validator;
	}

}

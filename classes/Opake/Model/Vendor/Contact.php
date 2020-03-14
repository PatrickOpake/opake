<?php

namespace Opake\Model\Vendor;

use Opake\Model\AbstractModel;

class Contact extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'vendor_contact';
	protected $_row = [
		'id' => null,
		'vendor_id' => null,
		'name' => '',
		'phone' => '',
		'email' => ''
	];
	protected $belongs_to = [
		'vendor' => [
			'model' => 'Vendor',
			'key' => 'vendor_id'
		]
	];

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('phone')->rule('unique', $this)->error(sprintf('Contact with phone %s already exists', $this->phone));
		$validator->field('phone')->rule('phone')->error('Contact phone must be 10 digits');
		$validator->field('email')->rule('unique', $this)->error(sprintf('Contact with email %s already exists', $this->email));
		$validator->field('email')->rule('callback', function ($val, $validator, $field) {
			$rq = $validator->pixie->orm->get('vendor')
				->where('contact_email', $val)
				->where('or', array('email', $val))
				->where('id', '!=', $this->vendor_id);
			$obj = $rq->find();

			return !($obj->loaded() && $obj->id() != $this->id());
		})->error(sprintf('Contact email %s already exists in other vendor', $this->email));

		return $validator;
	}

	public function toArray()
	{
		return [
			'id' => (int)$this->id,
			'name' => $this->name,
			'phone' => $this->phone,
			'email' => $this->email,
		];
	}

}

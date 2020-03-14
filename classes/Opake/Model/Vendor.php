<?php

namespace Opake\Model;

use Opake\Model\AbstractModel;
use Opake\Helper\Config;

class Vendor extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'vendor';
	protected $_row = array(
		'id' => null,
		'mmis_id' => null,
		'organization_id' => null,
		'name' => '',
		'acc_number' => '',
		'time_create' => null,
		'is_dist' => '',
		'is_manf' => '',
		'website' => '',
		'address' => '',
		'country_id' => null,
		'phone' => '',
		'email' => '',
		'logo_id' => '',
		'contact_name' => '',
		'contact_phone' => '',
		'contact_email' => '',
	);

	protected $belongs_to = array(
		'country' => [
			'model' => 'Geo_Country',
			'key' => 'country_id'
		],
		'logo' => [
			'model' => 'UploadedFile_Image',
			'key' => 'logo_id'
		]
	);

	const TYPE_MANF = 'manf';
	const TYPE_DIST = 'dist';

	protected $has_many = array(
		'inventory' => [
			'model' => 'inventory',
			'through' => 'inventory_supply',
			'key' => 'vendor_id',
			'foreign_key' => 'inventory_id'
		],
		'contacts' => [
			'model' => 'Vendor_Contact',
			'key' => 'vendor_id',
			'cascade_delete' => true
		]
	);

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('name')->rule('filled')->rule('min_length', 2)->error('Invalid Vendor Name');
		$validator->field('name')->rule('unique', $this)->error(sprintf('Vendor with name %s already exists', $this->name));
		$validator->field('acc_number')->rule('alpha_numeric')->error('Account Number must be alphanumeric');
		$validator->field('phone')->rule('phone')->error('Invalid Phone');
		$validator->field('phone')->rule('unique', $this)->error(sprintf('Vendor with phone %s already exists', $this->phone));
		$validator->field('email')->rule('email')->error('Invalid Email');
		$validator->field('email')->rule('unique', $this)->error(sprintf('Vendor with email %s already exists', $this->email));
		$validator->field('website')->rule('url')->error('Invalid Web Site');
		$validator->field('contact_phone')->rule('phone')->error('Invalid Primary Contact Phone');
		$validator->field('contact_phone')->rule('unique', $this)->error(sprintf('Vendor with primary contact phone %s already exists', $this->contact_phone));
		$validator->field('contact_email')->rule('email')->error('Invalid Primary Contact Email');
		$validator->field('contact_email')->rule('unique', $this)->error(sprintf('Vendor with primary contact email %s already exists', $this->contact_email));

		$vendorEmailValidation = function ($val, $validator, $field) {
			$rq = $validator->pixie->orm->get('vendor_contact')->where('email', $val)->where('vendor_id', '!=', $this->id);
			$obj = $rq->find();

			return !($obj->loaded() && $obj->id() != $this->id());
		};

		$validator->field('email')
			->rule('callback', $vendorEmailValidation)
			->error(sprintf('Email %s already exists in other vendor contact', $this->email));
		$validator->field('contact_email')
			->rule('callback', $vendorEmailValidation)
			->error(sprintf('Primary contact email %s already exists in other vendor contact', $this->contact_email));

		return $validator;
	}

	public function getLogo($size = NULL)
	{
		if ($logo = $this->getLogoModel()) {
			return $logo->getThumbnailWebPath($size);
		}

		if ($size) {
			return '/i/default-logo_' . $size . '.png';
		}

		return '/i/default-logo.png';
	}


	public function save()
	{
		if ($this->time_create == NULL) {
			$this->time_create = strftime('%Y-%m-%d %H:%M:%S');
		}

		parent::save();
	}

	public function getContacts()
	{
		return $this->contacts->find_all()->as_array();
	}

	public function toArray()
	{
		$contacts = [];

		foreach ($this->getContacts() as $contact) {
			$contacts[] = $contact->toArray();
		}

		return [
			'id' => (int)$this->id,
			'mmis_id' => $this->mmis_id,
			'organization_id' => $this->organization_id,
			'name' => $this->name,
			'acc_number' => $this->acc_number,
			'time_create' => $this->time_create,
			'is_dist' => $this->is_dist,
			'is_manf' => $this->is_manf,
			'website' => $this->website,
			'address' => $this->address,
			'country' => $this->country->toArray(),
			'country_id' => $this->country_id,
			'phone' => $this->phone,
			'email' => $this->email,
			'logo_id' => $this->logo_id,
			'logo' => $this->getLogo('default'),
			'contact_name' => $this->contact_name,
			'contact_phone' => $this->contact_phone,
			'contact_email' => $this->contact_email,
			'contacts' => $contacts,
		];
	}

	public function toShortArray()
	{
		return [
			'id' => (int)$this->id,
			'name' => $this->name,
			'email' => $this->email,
			'image' => $this->getLogo('tiny'),
			'is_dist' => (int)$this->is_dist,
			'is_manf' => (int)$this->is_manf,
			'organization_id' => $this->organization_id
		];
	}

	/**
	 * @return \Opake\Model\UploadedFile\Image
	 */
	protected function getLogoModel()
	{
		if ($this->logo_id) {
			if ($this->logo->loaded()) {
				return $this->logo;
			}
			if (!$this->logo->loaded() && $this->logo_id) {
				$model = $this->pixie->orm->get('UploadedFile_Image', $this->logo_id);
				if ($model->loaded()) {
					return $model;
				}
			}
		}

		return null;
	}
}

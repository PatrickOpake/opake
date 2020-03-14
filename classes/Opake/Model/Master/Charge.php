<?php

namespace Opake\Model\Master;

use Opake\Model\AbstractModel;

class Charge extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'master_charge';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'site_id' => null,
		'cdm' => '',
		'desc' => '',
		'amount' => null,
		'revenue_code' => '',
		'department' => null,
		'cpt' => '',
		'cpt_modifier1' => '',
		'cpt_modifier2' => '',
		'unit_price' => null,
		'ndc' => '',
		'active' => '',
		'general_ledger' => '',
		'notes' => '',
		'last_edited_date' => null,
		'historical_price' => '',
		'order' => 0,
	    'last_update' => 0,
	    'archived' => 0
	];

	protected $formatters = [
		'ListOption' => [
			'class' => '\Opake\Formatter\Master\Charge\ListOptionFormatter'
		]
	];

	public function getModifiersTitle()
	{
		$modifiers = [];
		if ($this->cpt_modifier1) {
			$modifiers[] = $this->cpt_modifier1;
		}
		if ($this->cpt_modifier2) {
			$modifiers[] = $this->cpt_modifier2;
		}

		return implode(', ', $modifiers);
	}

	public function getFeeScheduleEntry()
	{
		$entry = $this->pixie->orm->get('Billing_FeeSchedule_Record')
			->where('hcpcs', $this->cpt)
			->where('site_id', $this->site_id)
			->find();
		if ($entry->loaded()) {
			return $entry;
		}

		return null;
	}

	public function fromArray($data)
	{
		if (isset($data->last_edited_date) && $data->last_edited_date) {
			$data->last_edited_date = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime($data->last_edited_date));
		}

		return $data;
	}

	public function toArray()
	{
		$data = parent::toArray();

		$data['amount'] = number_format($this->amount, 2, '.', '');
		$data['unit_price'] = number_format($this->unit_price, 2, '.', '');
		$data['last_edited_date'] = $this->last_edited_date ? date('D M d Y H:i:s O', strtotime($this->last_edited_date)) : null;

		return $data;
	}

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('cdm')->rule('filled')->error('You must specify Charge Code field');
		//$validator->field('cdm')->rule('unique_for_site', $this)->error(sprintf('Item with Charge Code %s already exists', $this->cdm));
		$validator->field('desc')->rule('filled')->error('You must specify Description field');
		$validator->field('amount')->rule('filled')->error('You must specify Charge Amount field');
		$validator->field('amount')->rule('decimal')->error('Wrong format of Charge Amount field');
		$validator->field('revenue_code')->rule('filled')->error('You must specify Revenue code field');
		$validator->field('revenue_code')->rule('numeric')->error('The Revenue Code must be numeric');
		$validator->field('revenue_code')->rule('max_length', 4)->error('The Revenue Code must be less than or equal to 4 characters');
		$validator->field('department')->rule('filled')->error('You must specify Department No. field');
		$validator->field('cpt')->rule('filled')->error('You must specify CPT/HCPCS field');
		$validator->field('cpt')->rule('max_length', 10)->error('The CPT must be less than or equal to 10 characters');
		$validator->field('cpt_modifier1')->rule('max_length', 2)->error('The CPT Modifier 1 must be less than or equal to 2 characters');
		$validator->field('cpt_modifier2')->rule('max_length', 2)->error('The CPT Modifier 2 must be less than or equal to 2 characters');
		$validator->field('unit_price')->rule('decimal')->error('Wrong format of Unit Cost field');
		$validator->field('active')->rule('filled')->error('You must specify Active (Y/N) field');
		$validator->field('active')->rule('in', ['Y', 'N'])->error('The Active (Y/N) field must be equal to 1 character - "Y" or "N".');

		return $validator;
	}

	public function isAllFieldsIsEmpty()
	{
		$fieldsForCheck = [
			'cdm',
			'desc',
			'amount',
			'revenue_code',
			'department',
			'cpt',
			'cpt_modifier1',
			'cpt_modifier2',
			'unit_price',
			'ndc',
			'active',
			'general_ledger',
			'notes',
			'historical_price'
		];

		foreach ($fieldsForCheck as $field) {
			if(!empty($this->{$field})) {
				return false;
			}
		}

		return true;
	}
}

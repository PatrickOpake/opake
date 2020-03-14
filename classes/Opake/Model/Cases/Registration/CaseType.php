<?php

namespace Opake\Model\Cases\Registration;

use Opake\Model\AbstractModel;


/**
 * Class CaseType
 * @package Opake\Model\Cases\Registration
 */
class CaseType extends AbstractModel {

	public $id_field = 'id';
	public $table = 'case_registration_case_type';
	protected $_row = [
		'id' => null,
		'reg_id' => null,
		'case_type_id' => null,
		'is_pre_authorization' => 0,
		'pre_authorization' => '',
		'is_case_procedure' => null,
	];

	protected $belongs_to = [
		'case_type' => [
			'model' => 'Cases_Type',
			'key' => 'case_type_id'
		],
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	public function fromArray($data)
	{
		if (isset($data->cpt) && $data->cpt) {
			$data->case_type_id = $data->cpt->id;
		}

	}

	public function toArray()
	{
		$data = parent::toArray();
		$data['is_pre_authorization'] = (int) $this->is_pre_authorization;
		$data['is_case_procedure'] = (int) $this->is_case_procedure;
		return $data;
	}

}

<?php

namespace Opake\Model\Cases\Registration\Insurance;

use Opake\Model\AbstractModel;


/**
 * Class CaseType
 * @package Opake\Model\Cases\Registration
 */
class CaseType extends AbstractModel {

	public $id_field = 'id';
	public $table = 'case_registration_insurance_case_type';
	protected $_row = [
		'id' => null,
		'verification_id' => null,
		'case_type_id' => null,
		'is_pre_authorization' => 0,
		'pre_authorization' => '',
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


	public function toArray()
	{
		$data = parent::toArray();
		$data['is_pre_authorization'] = (int) $this->is_pre_authorization;
		return $data;
	}

}

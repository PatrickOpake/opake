<?php

namespace Opake\Model\Cases\Registration\Document;

use Opake\Model\AbstractModel;

class Type extends AbstractModel
{

	const DOC_TYPE_ASSIGNMENT_OF_BENEFITS = 1;
	const DOC_TYPE_ADVANCED_BENEFICIARY_NOTICE = 2;
	const DOC_TYPE_CONSENT_FOR_TREATMENT = 3;
	const DOC_TYPE_SMOKING_STATUS = 4;
	const DOC_TYPE_HIPAA_ACKNOWLEDGEMENT = 5;

	public $id_field = 'id';
	public $table = 'case_registration_document_types';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'name' => null,
		'is_required' => null,
	];

	protected $has_one = [
		'form_document' => [
			'model' => 'Forms_Document',
			'key' => 'doc_type_id',
		],
	];

	/**
	 * @return mixed
	 */
	public function getOrgTypes($org_id)
	{
		return $this->where(['organization_id', $org_id])
			->order_by('is_required', 'desc')
			->find_all();
	}

	public function getRequiredTypes()
	{
		return $this->where('is_required', 1)
			->find_all();
	}

}
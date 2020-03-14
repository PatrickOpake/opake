<?php

namespace Opake\Model\Billing\Navicure;

use Opake\Model\AbstractModel;

/**
 *
 * @property-read \Opake\Model\Cases\Item $case
 * @package Opake\Model\Billing\Navicure
 */
class Claim extends AbstractModel
{
	const STATUS_NEW = 0;
	const STATUS_SENT = 1;
	const STATUS_REJECTED_BY_PROVIDER = 2;
	const STATUS_ACCEPTED_BY_PROVIDER = 3;
	const STATUS_REJECTED_BY_PAYER = 4;
	const STATUS_ACCEPTED_BY_PAYER = 5;
	const STATUS_PASSED_PROVIDER_VALIDATION = 6;
	const STATUS_PASSED_PAYOR_VALIDATION = 7;
	const STATUS_PAYMENT_DENIED = 8;
	const STATUS_PAYMENT_PROCESSED = 9;
	const STATUS_UNKNOWN = 20;

	const ADD_STATUS_ACCEPTED_WITH_ERRORS = 1;
	const ADD_STATUS_REJECTED_MAC_FAILED = 2;
	const ADD_STATUS_REJECTED_ASSURANCE_FAILED = 3;
	const ADD_STATUS_REJECTED_CONTENT_COULDNT_BE_ANALYZED = 4;

	const TYPE_UB04 = 1;
	const TYPE_1500 = 2;
	const TYPE_ELECTRONIC_UB04_CLAIM = 3;
	const TYPE_ELECTRONIC_1500_CLAIM = 4;


	public $id_field = 'id';
	public $table = 'billing_navicure_claim';
	protected $_row = [
		'id' => null,
		'case_id' => null,
		'active' => 1,
		'status' => self::STATUS_NEW,
		'first_name' => null,
		'last_name' => null,
		'mrn' => null,
		'dos' => null,
		'insurance_payer_id' => null,
		'additional_status' => null,
		'error' => null,
		'last_update' => null,
	    'last_transaction_date' => null,
	    'sending_date' => null,
	    'primary_insurance_id' => null,
		'type' => null,
	];

	protected $has_many = [
		'status_acknowledgments' => [
			'model' => 'Billing_Navicure_Claim_StatusAcknowledgment',
		    'key' => 'claim_id',
		    'cascade_delete' => true
		],
		'status_acknowledgments_service' => [
			'model' => 'Billing_Navicure_Claim_StatusAcknowledgmentService',
			'key' => 'claim_id',
			'cascade_delete' => true
		],
	];

	protected $belongs_to = [
		'case' => [
			'model' => 'Cases_Item',
		    'key' => 'case_id'
		],
	    'payer' => [
		    'model' => 'Insurance_Payor',
	        'key' => 'insurance_payer_id'
	    ],
	    'primary_insurance' => [
		    'model' => 'Billing_Navicure_Claim_Insurance',
	        'key' => 'primary_insurance_id'
	    ]
	];

	protected $formatters = [
		'Coding' => [
			'class' => 'Opake\Formatter\Billing\Navicure\Claim\CodingFormatter'
		],
	    'ListEntry' => [
		    'class' => 'Opake\Formatter\Billing\Navicure\Claim\ListEntryFormatter'
	    ],
		'PaperListEntry' => [
			'class' => 'Opake\Formatter\Billing\PaperClaimsListFormatter'
		],
	];

	public function getPrimaryInsurance()
	{
		if ($this->primary_insurance_id && $this->primary_insurance->loaded()) {
			return $this->primary_insurance;
		}

		$primaryInsurance = $this->case->registration->getPrimaryInsurance();
		if (!$primaryInsurance) {
			throw new \Exception('Claim ' . $this->id() . ' has no the primary insurance');
		}

		return $primaryInsurance;
	}

	public function copyPrimaryInsurance()
	{
		if ($this->case->coding->insurance_order) {
			$codingInsurance = $this->case->coding->getAssignedInsurance();
		} else {
			$codingInsurance = $this->case->coding->getPrimaryInsurance();
		}

		if (!$codingInsurance || !$codingInsurance->getCaseInsurance()) {
			throw new \Exception('Cannot find the primary insurance for this case');
		}

		$primaryInsurance = $codingInsurance->getCaseInsurance();

		$claimInsurance = $this->pixie->orm->get('Billing_Navicure_Claim_Insurance');
		$claimInsurance->type = $primaryInsurance->type;
		$claimInsurance->order = $primaryInsurance->order;
		$claimInsurance->case_registration_insurance_id = $primaryInsurance->id();

		$dataModel = $primaryInsurance->getInsuranceDataModel();
		$newDataModel = $this->pixie->orm->get($dataModel->model_name);
		$newDataModel->values($dataModel->as_array(), false);
		$newDataModel->id = null;
		$newDataModel->save();

		$claimInsurance->insurance_data_id = $newDataModel->id();
		$claimInsurance->save();

		$this->primary_insurance_id = $claimInsurance->id();
	}

	public static function getListOfStatusDescription()
	{
		return [
			static::STATUS_NEW => 'New',
			static::STATUS_SENT => 'Sent',
			static::STATUS_REJECTED_BY_PROVIDER => 'Rejected by provider',
			static::STATUS_ACCEPTED_BY_PROVIDER => 'Accepted by provider',
			static::STATUS_REJECTED_BY_PAYER => 'Rejected by payer',
			static::STATUS_ACCEPTED_BY_PAYER => 'Accepted by payer',
		    static::STATUS_PASSED_PROVIDER_VALIDATION => 'Passed provider validation',
		    static::STATUS_PASSED_PAYOR_VALIDATION => 'Passed payor validation',
		    static::STATUS_PAYMENT_DENIED => 'Payment denied',
		    static::STATUS_PAYMENT_PROCESSED => 'Payment processed'
		];
	}

	public static function getListOfAdditionalStatusDescription()
	{
		return [
			static::ADD_STATUS_ACCEPTED_WITH_ERRORS => 'Accepted with errors',
		    static::ADD_STATUS_REJECTED_MAC_FAILED => 'Message Authentication Code (MAC) Failed',
		    static::ADD_STATUS_REJECTED_ASSURANCE_FAILED => 'Assurance Failed Validity Tests',
		    static::ADD_STATUS_REJECTED_CONTENT_COULDNT_BE_ANALYZED => 'Content After Decryption Could Not Be Analyzed'
		];
	}

	public static function getListOfClaimType()
	{
		return [
			static::TYPE_UB04 => 'Paper UB04',
			static::TYPE_1500 => 'Paper 1500',
			static::TYPE_ELECTRONIC_UB04_CLAIM => 'Electronic UB04',
			static::TYPE_ELECTRONIC_1500_CLAIM => 'Electronic 1500',
		];
	}

	public static function getListOfElectronicClaimType()
	{
		return [
			static::TYPE_ELECTRONIC_UB04_CLAIM => 'Electronic UB04',
		    static::TYPE_ELECTRONIC_1500_CLAIM => 'Electronic 1500'
		];
	}


	public function getTitle()
	{
		$types = self::getListOfClaimType();
		return sprintf('#%d %s', $this->id, $types[$this->type] ?? 'Unknown');
	}
}
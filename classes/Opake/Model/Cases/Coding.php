<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;
use Opake\Model\Insurance\AbstractType;

/**
 *
 * @property-read \Opake\Model\Cases\Item $case
 * @package Opake\Model\Cases
 */
class Coding extends AbstractModel
{
	const BILL_TYPE_NON_PAYMENT_ZERO_CLAIM = 0;
	const BILL_TYPE_ADMIT_THROUGH_DISCHARGE_CODE = 1;
	const BILL_TYPE_FIRST_INTERIM_CLAIM = 2;
	const BILL_TYPE_COUNTING_INTERIM_CLAIM = 3;
	const BILL_TYPE_LAST_INTERIM_CLAIM = 4;
	const BILL_TYPE_LATE_CHARGES_OF_PRIOR_CLAIM = 5;
	const BILL_TYPE_REPLACEMENT_OF_PRIOR_CLAIM = 6;
	const BILL_TYPE_CANCEL_OF_PRIOR_CLAIM = 7;

	public $id_field = 'id';
	public $table = 'case_coding';
	protected $_row = [
		'id' => null,
		'case_id' => null,
		'authorization_release_information_payment' => null,
		'discharge_code_id' => null,
		'bill_type' => null,
		'reference_number' => '',
		'has_lab_services_outside' => null,
		'lab_services_outside_amount' => null,
		'amount_paid' => 0,
		'addition_claim_information' => '',
		'remarks' => '',
		'insurance_order' => null,
		'amount_paid_by_other_insurance' => null,
		'is_ready_professional_claim' => 0,
		'is_ready_institutional_claim' => 0,
		'original_claim_id' => null,
	];

	protected $belongs_to = [
		'case' => [
			'model' => 'Cases_Item',
			'key' => 'case_id'
		],
		'discharge_code' => [
			'model' => 'DischargeStatusCode',
			'key' => 'discharge_code_id'
		],
		'original_claim' => [
			'model' => 'Billing_Navicure_Claim',
			'key' => 'original_claim_id'
		],
	];
	protected $has_many = [
		'diagnoses' => [
			'model' => 'Cases_Coding_Diagnosis',
			'key' => 'coding_id',
			'cascade_delete' => true
		],
		'bills' => [
			'model' => 'Cases_Coding_Bill',
			'key' => 'coding_id',
			'cascade_delete' => true
		],
		'occurrences' => [
			'model' => 'Cases_Coding_Occurrence',
			'key' => 'coding_id',
			'cascade_delete' => true
		],
		'values' => [
			'model' => 'Cases_Coding_Value',
			'key' => 'coding_id',
			'cascade_delete' => true
		],
		'condition_codes' => [
			'model' => 'ConditionCode',
			'through' => 'coding_condition_code',
			'key' => 'coding_id',
			'foreign_key' => 'code_id',
		]
	];

	protected $baseFormatter = [
		'class' => '\Opake\Formatter\Cases\Coding\BaseCodingFormatter'
	];

	public function getCase()
	{
		if ($this->loaded()) {
			return $this->case;
		} elseif ($this->case_id){
			return $this->pixie->orm->get('Cases_Item', $this->case_id);
		}
		return null;
	}

	/**
	 * @return \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance
	 */
	public function getPrimaryInsurance()
	{
		$primaryInsurance = $this->case->registration->insurances
			->where('deleted', 0)
			->where('order', 1)
			->find();

		if (!$primaryInsurance->loaded()) {
			return null;
		}

		return $this->wrapBillingInsurance($primaryInsurance);
	}

	/**
	 * @return \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance
	 */
	public function getSecondaryInsurance()
	{
		$secondaryInsurance = $this->case->registration->insurances
			->where('deleted', 0)
			->where('order', 2)
			->find();

		if (!$secondaryInsurance->loaded()) {
			return null;
		}

		return $this->wrapBillingInsurance($secondaryInsurance);
	}

	/**
	 * @return \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance
	 */
	public function getTertiaryInsurance()
	{
		$tertiaryInsurance = $this->case->registration->insurances
			->where('deleted', 0)
			->where('order', 3)
			->find();

		if (!$tertiaryInsurance->loaded()) {
			return null;
		}

		return $this->wrapBillingInsurance($tertiaryInsurance);
	}

	/**
	 * @return \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance
	 */
	public function getAssignedInsurance()
	{
		$order = 1;
		if ($this->insurance_order) {
			$order = $this->insurance_order;
		}
		$insurance = $this->case->registration->insurances
			->where('deleted', 0)
			->where('order', $order)
			->find();

		if (!$insurance->loaded()) {
			return null;
		}

		return $this->wrapBillingInsurance($insurance);
	}

	/**
	 * @deprecated
	 * @return \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance
	 */
	public function getInsuranceByOrder()
	{
		return $this->getAssignedInsurance();
	}

	public function isPrimaryInsuranceAssigned()
	{
		return ($this->insurance_order && $this->insurance_order == 1);
	}

	public function getDiagnoses()
	{
		if ($this->loaded()) {
			return $this->diagnoses->find_all()->as_array();
		} elseif ($this->case_id) {
			$diagnoses = [];
			$registration = $this->pixie->orm->get('Cases_Registration')
				->where('case_id', $this->case_id)
				->find();
			$icds = array_merge(
				$registration->admitting_diagnosis->find_all()->as_array(),
				$registration->secondary_diagnosis->find_all()->as_array()
			);
			$i = 1;
			foreach ($icds as $icd) {
				$diagnosis = $this->pixie->orm->get('Cases_Coding_Diagnosis');
				$diagnosis->coding_id = $this->id;
				$diagnosis->icd_id = $icd->id;
				$diagnosis->row = $i++;
				$diagnoses[] = $diagnosis;
			}
			return $diagnoses;
		}
		return [];
	}

	public function getInsurances()
	{
		return [];
	}

	protected function hasInsuranceWithOrder($insurancesArray, $order)
	{
		foreach ($insurancesArray as $insurance) {
			if ($insurance->order_number == $order) {
				return true;
			}
		}

		return false;
	}

	public function getBills()
	{
		if ($this->loaded()) {
			return $this->bills->find_all()->as_array();
		} elseif ($case = $this->getCase()) {

			$bills = [];
			$cpts = $case->getAdditionalCpts();

			$siteId = $case->location->site_id;

			foreach ($cpts as $cpt) {

				$chargeMasterEntry = $this->pixie->orm->get('Master_Charge')
					->where('site_id', $siteId)
					->where('cpt', $cpt->code)
					->find();

				if ($chargeMasterEntry->loaded()) {
					$bill = $this->pixie->orm->get('Cases_Coding_Bill');
					$bill->coding_id = $this->id;
					$bill->charge_master_entry_id = $chargeMasterEntry->id();
					$bill->quantity = 1;
					$bill->revenue_code = $chargeMasterEntry->revenue_code;
					if ($chargeMasterEntry->amount) {
						$bill->charge = (float) $chargeMasterEntry->amount;
						$bill->amount = ((float) $chargeMasterEntry->amount * $bill->quantity);
					}
					$bills[] = $bill;
				}
			}
			return $bills;
		}

		return [];
	}

	/**
	 * @return int
	 */
	public function getTotalAmount()
	{
		$bills = $this->bills->find_all();
		$total = 0;
		foreach ($bills as $bill) {
			if ($bill->amount) {
				$total += $bill->amount;
			}
		}

		return $total;
	}

	public function fromArray($data)
	{
		if (!empty($data->discharge_code)) {
			$data->discharge_code_id = $data->discharge_code->id;
		}

		if (isset($data->condition_codes) && $data->condition_codes) {
			$condition_codes = [];
			foreach ($data->condition_codes as $code) {
				$condition_codes[] = $code->id;
			}
			$data->condition_codes = $condition_codes;
		}

		return $data;
	}

	/**
	 * @param AbstractType $insurance
	 * @return \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance
	 */
	protected function wrapBillingInsurance($insurance)
	{
		return \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance::wrapCaseInsurance($insurance);
	}
}
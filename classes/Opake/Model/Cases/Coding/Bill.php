<?php

namespace Opake\Model\Cases\Coding;

use Opake\Model\AbstractModel;
use Opake\Model\Billing\Ledger\AppliedPayment;
use Opake\Model\Billing\Ledger\PaymentActivity;
use Opake\Model\Billing\Ledger\PaymentInfo;

class Bill extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'case_coding_bill';
	protected $_row = [
		'id' => null,
		'coding_id' => null,
		'case_type_id' => null,
		'charge_master_entry_id' => null,
		'quantity' => null,
		'revenue_code' => null,
		'fee_id' => null,
		'diagnosis_row' => null,
		'charge' => 0,
		'amount' => 0,
		'sort' => null,
		'custom_modifier' => null
	];

	protected $belongs_to = [
		'coding' => [
			'model' => 'Cases_Coding',
		    'key' => 'coding_id'
		],
		'case_type' => [
			'model' => 'CPT',
			'key' => 'case_type_id'
		],
		'charge_master_entry' => [
			'model' => 'Master_Charge',
			'key' => 'charge_master_entry_id'
		],
		'fee' => [
			'model' => 'Billing_FeeSchedule_Record',
			'key' => 'fee_id'
		]
	];

	protected $has_many = [
		'applied_payments' => [
			'model' => 'Billing_Ledger_AppliedPayment',
			'key' => 'coding_bill_id'
		],
	];

	protected $baseFormatter = [
		'class' => '\Opake\Formatter\Cases\Coding\BillFormatter'
	];

	protected $formatters = [
		'PaymentPosting' => [
			'class' => '\Opake\Formatter\Billing\PaymentPosting\BillFormatter'
		],
		'LedgerListEntry' => [
			'class' => '\Opake\Formatter\Billing\Ledger\BillFormatter'
		],
		'Collection' => [
			'class' => '\Opake\Formatter\Billing\Collection\BillFormatter'
		],
	];

	public function find_all()
	{
		$this->order_by('sort', 'asc');
		return parent::find_all();
	}

	public function get($property)
	{
		if (!$this->loaded()) {
			if ($property === 'case_type' && $this->case_type_id) {
				return $this->pixie->orm->get('CPT', $this->case_type_id);
			}
			if ($property === 'charge_master_entry' && $this->charge_master_entry_id) {
				return $this->pixie->orm->get('Master_Charge', $this->charge_master_entry_id);
			}
			if ($property === 'fee' && $this->fee_id) {
				return $this->pixie->orm->get('Billing_FeeSchedule_Record', $this->fee_id);
			}
			if ($property === 'coding' && $this->coding_id) {
				return $this->pixie->orm->get('Cases_Coding', $this->coding_id);
			}
		}

		return null;
	}

	/**
	 * The method returns charge master entry including backward compatibility
	 *
	 * @return \Opake\Model\Master\Charge | null
	 */
	public function getChargeMasterEntry()
	{
		if ($this->charge_master_entry->loaded()) {
			return $this->charge_master_entry;
		}

		$caseType = $this->case_type;
		$coding = $this->coding;

		if ($caseType->loaded() && $coding->loaded()) {
			$siteId = $coding->case->location->site_id;

			$model = $this->pixie->orm->get('Master_Charge')
				->where('cpt', $caseType->code)
				->where('site_id', $siteId)
				->find();

			if ($model->loaded()) {
				return $model;
			}
		}

		return null;
	}

	public function getModifiersArray()
	{
		if ($this->custom_modifier) {
			$parts = explode(',', $this->custom_modifier);
			$parts = array_map('trim', $parts);
			return $parts;
		}

		if ($chargeMasterEntry = $this->getChargeMasterEntry()) {
			$modifiers = [];
			if ($chargeMasterEntry->cpt_modifier1) {
				$modifiers[] = $chargeMasterEntry->cpt_modifier1;
			}
			if ($chargeMasterEntry->cpt_modifier2) {
				$modifiers[] = $chargeMasterEntry->cpt_modifier2;
			}
			return $modifiers;
		}

		return [];
	}

	/**
	 * @return null
	 * @deprecated
	 */
	public function getDiagnosis()
	{
		$diagnosisRow = $this->diagnosis_row;
		if ($diagnosisRow) {
			$coding = $this->coding;
			$diagnosis = $coding->diagnoses->find_all()->as_array();
			$index = ($diagnosisRow - 1);
			if (isset($diagnosis[$index])) {
				return $diagnosis[$index];
			}
		}

		return null;
	}

	public function getDiagnosesLetters()
	{
		$numbers = $this->getDiagnosesNumbers();
		$letters = static::getDiagnosisRowsList();
		$result = [];

		foreach ($numbers as $number) {
			if (isset($letters[$number])) {
				$result[] = $letters[$number];
			}
		}

		return $result;
	}

	public function getDiagnoses()
	{
		$result = [];
		$coding = $this->coding;
		$diagnoses = [];
		foreach ($coding->diagnoses->find_all() as $diagnosis) {
			$diagnoses[$diagnosis->row] = $diagnosis;
		}

		$numbers = $this->getDiagnosesNumbers();

		foreach ($numbers as $number) {
			if (isset($diagnoses[$number])) {
				$result[] = $diagnoses[$number];
			}
		}

		return $result;
	}

	public function getDiagnosesNumbers()
	{
		$numbers = [];

		if ($this->loaded()) {
			$query = $this->pixie->db->query('select')
				->fields('diagnosis_number')
				->table('case_coding_bill_diagnosis')
				->where('bill_id', $this->id())
				->order_by('order')
				->execute();

			foreach ($query as $row) {
				$numbers[] = $row->diagnosis_number;
			}
		}

		return $numbers;
	}

	public function getRemainder()
	{
		$amount = (float) $this->amount;
		$activityEntries = $this->applied_payments->find_all();
		foreach ($activityEntries as $entry) {
			$entryAmount = (float) $entry->amount;
			$amount -= $entryAmount;
		}
		if ($amount < 0) {
			$amount = 0;
		}

		return $amount;
	}

	public function getPayment()
	{
		$sumAmount = 0;
		$activityEntries = $this->applied_payments
			->find_all();

		/** @var AppliedPayment $entry */
		foreach ($activityEntries as $entry) {
			$paymentInfo = $entry->payment_info;
			if (!in_array($paymentInfo->payment_source,
				[PaymentInfo::PAYMENT_SOURCE_ADJUSTMENT, PaymentInfo::PAYMENT_SOURCE_WRITE_OFF]))
			{
				$sumAmount += $entry->amount;
			}
		}

		return $sumAmount;
	}

	public function getWriteOff()
	{
		$sumAmount = 0;

		$appliedPayments = $this->pixie->orm->get('Billing_Ledger_AppliedPayment');
		$paymentsQuery = $appliedPayments->query;
		$paymentsQuery->fields('billing_ledger_applied_payment.*');

		$paymentsQuery->join('billing_ledger_payment_info', ['billing_ledger_payment_info.id', 'billing_ledger_applied_payment.payment_info_id'], 'inner')
			->where('billing_ledger_payment_info.payment_source', PaymentInfo::PAYMENT_SOURCE_WRITE_OFF);

		$appliedPayments->where('coding_bill_id', $this->id);

		foreach ($appliedPayments->find_all() as $entry) {
			$sumAmount += $entry->amount;
		}

		return $sumAmount;
	}

	public function getAdjustment()
	{
		$sum = 0;
		$activityEntries = $this->applied_payments
			->find_all();

		foreach ($activityEntries as $entry) {
			if ($entry->payment_info->payment_source == PaymentInfo::PAYMENT_SOURCE_ADJUSTMENT) {
				$sum += $entry->amount;
			}
		}

		return $sum;
	}

	public function updateDiagnosesNumbers($data)
	{
		$this->pixie->db->begin_transaction();
		try {
			if ($this->loaded()) {
				$this->pixie->db->query('delete')
					->table('case_coding_bill_diagnosis')
					->where('bill_id', $this->id())
					->execute();

				foreach ($data as $index => $diagNumber) {
					$this->pixie->db->query('insert')
						->table('case_coding_bill_diagnosis')
						->data([
							'bill_id' => $this->id(),
						    'diagnosis_number' => $diagNumber,
						    'order' => $index
						])
						->execute();
				}
			}

			$this->pixie->db->commit();

		} catch (\Exception $e) {
			$this->pixie->db->rollback();
		}
	}

	public static function getDiagnosisRowsList()
	{
		return [
			1 => 'A',
			2 => 'B',
			3 => 'C',
			4 => 'D',
			5 => 'E',
			6 => 'F',
			7 => 'G',
			9 => 'H',
			10 => 'I',
			11 => 'J',
			12 => 'K',
			13 => 'L'
		];
	}
}

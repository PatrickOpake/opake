<?php

namespace OpakeAdmin\Model\Search\Billing;

use Opake\Model\Billing\Ledger\PaymentInfo;
use Opake\Model\Billing\Navicure\Payment;
use Opake\Model\Search\AbstractSearch;

class LedgerPaymentActivity extends AbstractSearch
{
	const FILTER_PAYMENT_SOURCE_INSURANCE = 1;
	const FILTER_PAYMENT_SOURCE_PATIENT = 2;
	const FILTER_PAYMENT_SOURCE_ADJUSTMENT = 3;
	const FILTER_PAYMENT_SOURCE_WRITE_OFF = 4;

	protected $organizationId;

	/**
	 * @return mixed
	 */
	public function getOrganizationId()
	{
		return $this->organizationId;
	}

	/**
	 * @param mixed $organizationId
	 */
	public function setOrganizationId($organizationId)
	{
		$this->organizationId = $organizationId;
	}

	public function search($model, $request)
	{

		$model = parent::prepare($model, $request);

		$this->_params = [
			'first_name' => trim($request->get('first_name')),
			'last_name' => trim($request->get('last_name')),
			'date_from' => trim($request->get('date_from')),
			'date_to' => trim($request->get('date_to')),
			'payment_source' => trim($request->get('payment_source')),
			'payment_method' => trim($request->get('payment_method'))
		];

		$sort = $request->get('sort_by', 'id');
		$order = $request->get('sort_order', 'DESC');

		$query = $model->query;
		$query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));
		$query->join('case_coding_bill', ['billing_ledger_applied_payment.coding_bill_id', 'case_coding_bill.id'], 'inner');
		$query->join('case_coding', ['case_coding_bill.coding_id', 'case_coding.id'], 'inner');
		$query->join('case', ['case_coding.case_id', 'case.id'], 'inner');
		$query->join('case_registration', ['case_registration.case_id', 'case.id'], 'inner');
		$query->join('patient', ['case_registration.patient_id', 'patient.id'], 'inner');
		$query->join('billing_ledger_payment_info', ['billing_ledger_payment_info.id', 'billing_ledger_applied_payment.payment_info_id'], 'inner');

		if ($this->organizationId) {
			$query->where('patient.organization_id', $this->organizationId);
		}

		if ($this->_params['first_name'] !== '') {
			$query->where('patient.first_name', 'like', '%' . $this->_params['first_name'] . '%');
		}

		if ($this->_params['last_name'] !== '') {
			$query->where('patient.last_name', 'like', '%' . $this->_params['last_name'] . '%');
		}

		if ($this->_params['last_name'] !== '') {
			$query->where('patient.last_name', 'like', '%' . $this->_params['last_name'] . '%');
		}

		if (!empty($this->_params['date_from'])) {
			$query->where('billing_ledger_payment_info.date_of_payment', '>=', \Opake\Helper\TimeFormat::formatToDB($this->_params['date_from']));
		}

		if (!empty($this->_params['date_to'])) {
			$query->where('billing_ledger_payment_info.date_of_payment', '<=', \Opake\Helper\TimeFormat::formatToDB($this->_params['date_to']));
		}

		if (!empty($this->_params['payment_source'])) {
			$sourceTypes = [];
			if ($this->_params['payment_source'] == static::FILTER_PAYMENT_SOURCE_INSURANCE) {
				$sourceTypes = [
					PaymentInfo::PAYMENT_SOURCE_INSURANCE
				];
			} else if ($this->_params['payment_source'] == static::FILTER_PAYMENT_SOURCE_PATIENT) {
				$sourceTypes = [
					PaymentInfo::PAYMENT_SOURCE_PATIENT_CO_INSURANCE,
					PaymentInfo::PAYMENT_SOURCE_PATIENT_CO_PAY,
					PaymentInfo::PAYMENT_SOURCE_PATIENT_DEDUCTIBLE,
					PaymentInfo::PAYMENT_SOURCE_PATIENT_OOP
				];
			} else if ($this->_params['payment_source'] == static::FILTER_PAYMENT_SOURCE_ADJUSTMENT) {
				$sourceTypes = [
					PaymentInfo::PAYMENT_SOURCE_ADJUSTMENT
				];
			} else if ($this->_params['payment_source'] == static::FILTER_PAYMENT_SOURCE_WRITE_OFF) {
				$sourceTypes = [
					PaymentInfo::PAYMENT_SOURCE_WRITE_OFF,
					PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_CO_INSURANCE,
					PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_CO_PAY,
					PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_DEDUCTIBLE,
					PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_OOP,
				];
			}

			if ($sourceTypes) {
				$query->where('billing_ledger_payment_info.payment_source', 'IN', $this->pixie->db->arr($sourceTypes));
			}
		}

		if (!empty($this->_params['payment_method'])) {
			$query->where('billing_ledger_payment_info.payment_method', $this->_params['payment_method']);
		}

		if ($this->_pagination) {
			$model->pagination($this->_pagination);
		}

		switch ($sort) {
			case 'id':
				$query->order_by('billing_ledger_applied_payment.id', $order);
				break;
			case 'date_of_payment':
				$query->order_by('billing_ledger_payment_info.date_of_payment', $order);
				break;
			case 'patient_last_name':
				$query->order_by('patient.last_name', $order);
				break;
			case 'patient_first_name':
				$query->order_by('patient.first_name', $order);
				break;
			case 'payment_source':
				$query->order_by($this->pixie->db->expr($this->getPaymentSourceCase()), $order);
				break;
			case 'payment_method':
				$query->order_by($this->pixie->db->expr($this->getPaymentMethodSource()), $order);
				break;
			case 'payment_amount':
				$query->order_by('billing_ledger_applied_payment.amount', $order);
				break;
		}

		$results = $model->find_all()
			->as_array();

		$count = $this->pixie->db
			->query('select')
			->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()
			->get('count');

		if ($this->_pagination) {
			$this->_pagination->setCount($count);
		}

		return $results;
	}

	protected function getPaymentSourceCase()
	{
		$sql = 'CASE `billing_ledger_payment_info`.`payment_source` ';
		$statuses = PaymentInfo::getPaymentSourcesList();
		foreach ($statuses as $id => $desc) {
			$sql .= 'WHEN ' . $id . ' THEN \'' . $desc . '\' ';
		}
		$sql .= 'ELSE 1 END';

		return $sql;
	}

	protected function getPaymentMethodSource()
	{
		$sql = 'CASE `billing_ledger_payment_info`.`payment_method` ';
		$statuses = PaymentInfo::getPaymentMethodsList();
		foreach ($statuses as $id => $desc) {
			$sql .= 'WHEN ' . $id . ' THEN \'' . $desc . '\' ';
		}
		$sql .= 'ELSE 1 END';

		return $sql;
	}

}

<?php

namespace OpakeAdmin\Helper\Analytics\Reports\CasesReport;

use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Navicure\Claim;
use Opake\Model\Profession;
use OpakeAdmin\Helper\Analytics\Reports\CasesReport\RowSource\BaseCaseRowSource;
use OpakeAdmin\Helper\Analytics\Reports\CasesReport\RowSource\CaseCancellationRowSource;
use OpakeAdmin\Helper\Analytics\Reports\CasesReport\RowSource\InventoryItemRowSource;
use OpakeAdmin\Helper\Analytics\Reports\CasesReport\RowSource\ProcedureRowSource;

class Formatter
{
	const NOT_FOUND = '{val_not_found}';

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var array
	 */
	protected $claims = [];

	public function __construct()
	{
		$this->pixie = \Opake\Application::get();
	}

	/**
	 * @param $columnName
	 * @param BaseCaseRowSource $rowSource
	 * @return string
	 */
	public function formatColumn($columnName, $rowSource)
	{
		if ($rowSource instanceof InventoryItemRowSource) {
			$value = $this->formatInventoryItemColumn($columnName, $rowSource);
			if ($value !== static::NOT_FOUND) {
				return $value;
			}
		}

		if ($rowSource instanceof CaseCancellationRowSource) {
			$value = $this->formatCancellationColumn($columnName, $rowSource);
			if ($value !== static::NOT_FOUND) {
				return $value;
			}
		}

		if ($rowSource instanceof ProcedureRowSource) {
			$value = $this->formatProcedureColumn($columnName, $rowSource);
			if ($value !== static::NOT_FOUND) {
				return $value;
			}
		}

		return $this->formatCaseColumn($columnName, $rowSource);
	}

	/**
	 * @param string $columnName
	 * @param InventoryItemRowSource $rowSource
	 * @return string
	 */
	protected function formatInventoryItemColumn($columnName, $rowSource)
	{
		switch ($columnName) {
			case 'inventory_item_name':
				return $rowSource->getInventoryItem()->name;

			case 'inventory_item_number':
				return $rowSource->getInventoryItem()->item_number;

			case 'inventory_item_description':
				return $rowSource->getInventoryItem()->desc;

			case 'inventory_qty_requested':
				return $rowSource->getInventoryDefaultQuantity();

			case 'inventory_qty_used':
				return $rowSource->getInventoryActualUse();

			case 'inventory_unit_price':
				$unitPrice = $rowSource->getInventoryItem()->unit_price;
				return number_format($unitPrice, 2, '.', '');

			case 'inventory_charge_amount':
				$chargeAmount = $rowSource->getInventoryItem()->getChargeAmount();
				return number_format($chargeAmount, 2, '.', '');

			case 'inventory_manufacturer':
				return $rowSource->getInventoryItem()->manufacturer->name;
		}

		return static::NOT_FOUND;
	}

	/**
	 * @param string $columnName
	 * @param CaseCancellationRowSource $rowSource
	 * @return string
	 */
	protected function formatCancellationColumn($columnName, $rowSource)
	{
		switch ($columnName) {
			case 'case_canceled_within_one_day':
				return $this->formatCaseCanceledWithinOneDay($rowSource);

			case 'case_rescheduled':
				return $this->formatCaseRescheduled($rowSource);
		}

		return static::NOT_FOUND;
	}

	/**
	 * @param string $columnName
	 * @param ProcedureRowSource $rowSource
	 * @return string
	 */
	protected function formatProcedureColumn($columnName, $rowSource)
	{

		if($columnName === 'coded_procedures_hcpcs') {
		}

		switch ($columnName) {
			case 'bill_procedure_cpt':
				$chargeMasterEntry = $rowSource->getBill()->getChargeMasterEntry();
				return ($chargeMasterEntry) ? $chargeMasterEntry->cpt : '';

			case 'bill_procedure_description':
				$chargeMasterEntry = $rowSource->getBill()->getChargeMasterEntry();
				return ($chargeMasterEntry) ? $chargeMasterEntry->desc : '';

			case 'bill_procedure_charge_amount':
				return $this->_formatToMoney($chargeMasterEntry = $rowSource->getBill()->amount);
			case 'coded_procedures_hcpcs':
				$chargeMasterEntry = $rowSource->getBill()->getChargeMasterEntry();
				return ($chargeMasterEntry) ? $chargeMasterEntry->cpt : '';
			case 'coded_procedures_desc':
				$chargeMasterEntry = $rowSource->getBill()->getChargeMasterEntry();
				return ($chargeMasterEntry) ? $chargeMasterEntry->desc : '';
			case 'coded_procedures_amount':
				return $this->_formatToMoney($rowSource->getBill()->amount);

		}

		return static::NOT_FOUND;
	}

	/**
	 * @param string $columnName
	 * @param BaseCaseRowSource $rowSource
	 * @return string
	 */
	protected function formatCaseColumn($columnName, $rowSource)
	{
		$case = $rowSource->getCase();

		switch ($columnName) {
			case 'case_start_time':
				return $this->formatTime($case->time_start);

			case 'case_end_time':
				return $this->formatTime($case->time_end);

			case 'case_actual_start_time':
				return $this->formatCaseActualStartTime($case);

			case 'case_actual_end_time':
				return $this->formatCaseActualEndTime($case);

			case 'case_physician':
				return $this->formatUsers($case);

			case 'case_duration':
				return $this->formatDuration($case);

			case 'case_procedure1_hcpcs':
				return $this->formatProcedure($case, 1, 'hcpcs');

			case 'case_procedure1_desc':
				return $this->formatProcedure($case, 1, 'desc');

			case 'case_procedure1_amount':
				return $this->formatProcedure($case, 1, 'amount');

			case 'case_procedure2_hcpcs':
				return $this->formatProcedure($case, 2, 'hcpcs');

			case 'case_procedure2_desc':
				return $this->formatProcedure($case, 2, 'desc');

			case 'case_procedure2_amount':
				return $this->formatProcedure($case, 2, 'amount');

			case 'case_procedure3_hcpcs':
				return $this->formatProcedure($case, 3, 'hcpcs');

			case 'case_procedure3_desc':
				return $this->formatProcedure($case, 3, 'desc');

			case 'case_procedure3_amount':
				return $this->formatProcedure($case, 3, 'amount');

			case 'case_procedure4_hcpcs':
				return $this->formatProcedure($case, 4, 'hcpcs');

			case 'case_procedure4_desc':
				return $this->formatProcedure($case, 4, 'desc');

			case 'case_procedure4_amount':
				return $this->formatProcedure($case, 4, 'amount');

			case 'case_description':
				return $case->description;

			case 'case_date_of_service':
				return $this->formatDateTimeToDate($case->time_start);

			case 'case_doctor':
				return $this->formatUsers($case);

			case 'case_id':
				return $case->id();

			case 'patient_id':
				return $case->registration->patient_id;

			case 'patient_last_name':
				return $case->registration->last_name;

			case 'patient_first_name':
				return $case->registration->first_name;

			case 'patient_street_address':
				return $case->registration->home_address;

			case 'patient_street_address_2':
				return $case->registration->home_apt_number;

			case 'patient_country':
				return $this->formatCountry($case);

			case 'patient_city':
				return $this->formatCity($case);

			case 'patient_state':
				return $this->formatState($case);

			case 'patient_zip':
				return $case->registration->home_zip_code;

			case 'patient_phone_number':
				return $case->registration->home_phone;

			case 'patient_date_of_birth':
				return $this->formatDate($case->registration->dob);

			case 'patient_mrn':
				return $case->registration->patient->getFullMrn();

			case 'insurance_company':
				return $this->formatInsuranceCompany($case);

			case 'primary_insurance_type':
				return $this->formatPrimaryInsuranceType($case);

			case 'patient_insurance_id':
				return $this->formatPatientInsuranceId($case);

			case 'total_scheduled_charges':
				return $this->formatTotalScheduledCharges($case);

			case 'total_coded_charges':
				return $this->formatTotalCodedCharges($case);

			case 'insurance_phone':
				return $this->formatInsurancePhone($case);

			case 'case_anesthesiologist':
				return $this->formatAnesthesiologist($case);

			case 'case_anesthesia_type':
				return $this->formatAnesthesiaType($case);

			case 'case_special_equipment':
				return $case->special_equipment_implants;

			case 'case_special_equipment_flag':
				return $this->getFlagValue($case->special_equipment_implants);

			case 'case_implants':
				return $case->implants;

			case 'case_implants_flag':
				return $this->getFlagValue($case->implants);

			case 'case_actual_duration':
				return $this->formatCaseActualDuration($case);

			case 'clinical_notes':
				return $this->formatClinicalNotes($case);

			case 'billing_notes':
				return $this->formatBillingNotes($case);

			case 'type_of_claim':
				return $this->formatTypeOfClaim($case);

			case 'date_of_submission_claim':
				return $this->formatDateOfSubmissionClaim($case);

			case 'primary_insurance':
				return $this->formatInsurance($case, 1);

			case 'secondary_insurance':
				return $this->formatInsurance($case, 2);

			case 'tertiary_insurance':
				return $this->formatInsurance($case, 3);

			case 'quaternary_insurance':
				return $this->formatInsurance($case, 4);

			case 'other_insurance':
				return $this->formatInsurance($case, 5);

			case 'or':
				return $this->formatLocation($case);

			case 'payments_amount':
				return $this->formatPaymentsAmount($case, $rowSource);

			case 'adjustments_amount':
				return $this->formatAdjustmentsAmount($case, $rowSource);

			case 'write_offs_amount':
				return $this->formatWriteOffsAmount($case, $rowSource);

			case 'outstanding_balance':
				return $this->formatOutstandingBalance($case, $rowSource);

			case 'ar_billing_status':
				return $this->formatBillingStatus($case);

			case 'insurance_type_acronym':
				return $this->formatInsuranceTypeAcronym($case);

			case 'deductible':
				return $this->formatDeductibles($case, $rowSource);

			case 'co_pay':
				return $this->formatCoPay($case, $rowSource);

			case 'co_insurance':
				return $this->formatCoInsurance($case, $rowSource);

			case 'oop':
				return $this->formatOOP($case, $rowSource);

			case 'var_cost':
				return $this->formatVarCost($case, $rowSource);
		}

		return '';
	}

	protected function getFlagValue($value)
	{
		if (!empty($value)) {
			return 'Yes';
		}

		return 'No';
	}

	protected function getCaseActualStartTime($case)
	{
		$timeLogEnterOr = false;
		$timeLogEnterOrQuery = $this->pixie->db->query('select')
			->table('case_time_log')
			->fields('time')
			->where(['case_id', $case->id], ['stage', 'enter_or'])
			->execute()
			->current();
		if ($timeLogEnterOrQuery) {
			$timeLogEnterOr = $timeLogEnterOrQuery->time;
		}

		return $timeLogEnterOr;
	}

	protected function getCaseActualEndTime($case)
	{
		$timeLogExitOr = false;
		$timeLogExitOrQuery = $this->pixie->db->query('select')
			->table('case_time_log')
			->fields('time')
			->where(['case_id', $case->id], ['stage', 'operation_room_exit'])
			->execute()
			->current();
		if ($timeLogExitOrQuery) {
			$timeLogExitOr = $timeLogExitOrQuery->time;
		}

		return $timeLogExitOr;
	}

	protected function getClaims($case)
	{
		if (!empty($this->claims[$case->id])) {
			return $this->claims;
		}

		$this->claims = [];

		$claims = $this->pixie->db->query('select')
			->table('billing_navicure_claim')
			->fields('type', 'last_transaction_date')
			->where('case_id', $case->id)
			->order_by('last_transaction_date', 'ASC')
			->execute();
		foreach ($claims as $claim) {
			$this->claims[$case->id]['type'][] = $claim->type;
			$this->claims[$case->id]['last_transaction_date'][] = $claim->last_transaction_date;
		}

		return $this->claims;

	}

	protected function formatCaseActualStartTime($case)
	{
		$timeLogStartOr = $this->getCaseActualStartTime($case);
		if ($timeLogStartOr) {
			$timeStart = TimeFormat::fromDBTime($timeLogStartOr);
			return TimeFormat::getTime($timeStart);
		}

		return '';
	}

	protected function formatCaseActualEndTime($case)
	{
		$timeLogStartOr = $this->getCaseActualEndTime($case);
		if ($timeLogStartOr) {
			$timeStart = TimeFormat::fromDBTime($timeLogStartOr);
			return TimeFormat::getTime($timeStart);
		}

		return '';
	}

	protected function formatCaseActualDuration($case)
	{

		$timeLogEnterOr = $this->getCaseActualStartTime($case);
		$timeLogExitOr = $this->getCaseActualEndTime($case);

		if ($timeLogEnterOr && $timeLogExitOr) {
			$timeStart = TimeFormat::fromDBTime($timeLogEnterOr);
			$timeEnd = TimeFormat::fromDBTime($timeLogExitOr);

			if ($timeStart && $timeEnd) {
				$secDiff = abs($timeEnd->getTimestamp() - $timeStart->getTimestamp());
				return (ceil($secDiff / 60));
			}
		}

		return '';
	}

	protected function formatClinicalNotes($case)
	{
		$notes = [];
		foreach ($case->getNotes() as $item) {
			$date = TimeFormat::fromDBDatetime($item->time_add);
			$notes[] = $item->user->getFullName() . ' (' . TimeFormat::getDate($date) . ' ' . TimeFormat::getTime($date) . '): ' . $item->text;
		}

		$result = implode("\n", $notes);

		return $result;
	}

	protected function formatBillingNotes($case)
	{
		$notes = [];
		foreach ($case->getBillingNotes() as $item) {
			$date = TimeFormat::fromDBDatetime($item->time_add);
			$notes[] = $item->user->getFullName() . ' (' . TimeFormat::getDate($date) . ' ' . TimeFormat::getTime($date) . '): ' . $item->text;
		}

		$result = implode("\n", $notes);

		return $result;
	}

	protected function formatTypeOfClaim($case)
	{
		$claimTypes = [];
		$claims = $this->getClaims($case);
		$types = Claim::getListOfClaimType();
		if (!empty($claims[$case->id])) {
			foreach ($claims[$case->id]['type'] as $type) {
				if (isset($types[$type])) {
					$claimTypes[] = $types[$type];
				}
			}
		}

		return implode("\n", $claimTypes);
	}

	protected function formatDateOfSubmissionClaim($case)
	{
		$dateOfSubmission = [];
		$claims = $this->getClaims($case);
		if (!empty($claims[$case->id])) {
			foreach ($claims[$case->id]['last_transaction_date'] as $value) {
				$date = TimeFormat::fromDBDatetime($value);
				if ($date) {
					$dateOfSubmission[] = TimeFormat::getDate($value);
				}
			}
		}

		return implode("\n", $dateOfSubmission);
	}

	protected function formatInsurance($case, $order)
	{
		$labels = [];

		foreach ($insurances = $case->registration->getSelectedInsurances() as $insurance) {
			if ($insurance->order == $order) {
				$labels[] = $insurance->getInsuranceName();

			}
		}

		return implode("\n", $labels);
	}

	protected function formatDuration($case)
	{
		if ($case->time_start && $case->time_end) {
			$timeStart = TimeFormat::fromDBDatetime($case->time_start);
			$timeEnd = TimeFormat::fromDBDatetime($case->time_end);

			if ($timeStart && $timeEnd) {
				$secDiff = abs($timeEnd->getTimestamp() - $timeStart->getTimestamp());
				return (ceil($secDiff / 60));
			}
		}

		return '';
	}

	protected function formatUsers($case)
	{
		$names = [];

		foreach ($case->users->find_all() as $user) {
			$names[] = $user->getFullName();
		}

		return implode(', ', $names);
	}

	protected function formatDateTime($date)
	{
		if (!$date) {
			return '';
		}

		$dateTime = TimeFormat::fromDBDatetime($date);
		return TimeFormat::getDateTime($dateTime);
	}

	protected function formatTime($date)
	{
		if (!$date) {
			return '';
		}

		$dateTime = TimeFormat::fromDBDatetime($date);
		return TimeFormat::getTime($dateTime);
	}

	protected function formatDate($date)
	{
		if (!$date) {
			return '';
		}

		$dateTime = TimeFormat::fromDBDate($date);
		return TimeFormat::getDate($dateTime);
	}

	protected function formatDateTimeToDate($date)
	{
		if (!$date) {
			return '';
		}

		$dateTime = TimeFormat::fromDBDatetime($date);
		return TimeFormat::getDate($dateTime);
	}

	protected function formatProcedure($case, $num, $type)
	{

		if ($num === 1) {
			if ($case->type->isHistorical()) {
				switch ($type) {
					case 'hcpcs':
						return $case->type->code;
						break;
					case 'desc':
						return $case->description;
						break;
					case 'amount':
						return $case->type->getCharge()->amount;
						break;
				}

			}
		}

		$procedures = [];
		switch ($type) {
			case 'hcpcs':
				$procedures[] = $case->type->code;
				break;
			case 'desc':
				$procedures[] = $case->type->name;
				break;
			case 'amount':
				$procedures[] = $case->type->getCharge()->amount;
				break;
		}

		$additionalCpts = $case->additional_cpts->find_all();
		foreach ($additionalCpts as $caseType) {
			if ($caseType->id() != $case->type->id()) {
				switch ($type) {
					case 'hcpcs':
						$procedures[] = $case->type->code;
						break;
					case 'desc':
						$procedures[] = $case->type->name;
						break;
					case 'amount':
						$procedures[] = $case->type->getCharge()->amount;
						break;
				}
			}
		}

		return (isset($procedures[$num - 1])) ? $procedures[$num - 1] : '';

	}

	protected function formatCountry($case)
	{
		if ($case->registration->home_country && $case->registration->home_country->loaded()) {
			return $case->registration->home_country->name;
		}

		return '';
	}

	protected function formatFlag($flag)
	{
		if ($flag == 1) {
			return 'Yes';
		} elseif ($flag == 0) {
			return 'No';
		}

		return '';
	}

	protected function formatCity($case)
	{
		if ($case->registration->custom_home_city) {
			return $case->registration->custom_home_city;
		}

		if ($case->registration->home_city && $case->registration->home_city->loaded()) {
			return $case->registration->home_city->name;
		}

		return '';
	}

	protected function formatState($case)
	{
		if ($case->registration->custom_home_state) {
			return $case->registration->custom_home_state;
		}

		if ($case->registration->home_state && $case->registration->home_state->loaded()) {
			return $case->registration->home_state->name;
		}

		return '';
	}

	protected function formatInsuranceCompany($case)
	{
		$labels = [];

		foreach ($insurances = $case->registration->getSelectedInsurances() as $insurance) {
			$labels[] = $insurance->getInsuranceName();
		}

		return implode(', ', $labels);
	}

	protected function formatPrimaryInsuranceType($case)
	{
		$types = \Opake\Model\Insurance\AbstractType::getInsuranceTypesList();
		$labels = [];
		$primaryInsurance =  $case->registration->getPrimaryInsurance();
		if (isset($types[$primaryInsurance->type])) {
			$labels[] = $types[$primaryInsurance->type];
		}

		return implode(', ', $labels);
	}

	protected function formatInsuranceTypeAcronym($case)
	{
		$types = \Opake\Model\Insurance\AbstractType::getInsuranceTypesListAcronym();
		$labels = [];

		foreach ($case->registration->getSelectedInsurances() as $insurance) {
			if (isset($types[$insurance->type])) {
				$labels[] = $types[$insurance->type];
			}
		}

		return implode(', ', $labels);
	}

	protected function formatPatientInsuranceId($case)
	{
		$labels = [];

		foreach ($insurances = $case->registration->getSelectedInsurances() as $insurance) {
			$insuranceData = $insurance->getInsuranceDataModel();
			if (isset($insuranceData->policy_number) && $insuranceData->policy_number) {
				$labels[] = $insuranceData->policy_number;
			}
		}

		return implode(', ', $labels);
	}

	protected function formatInsurancePhone($case)
	{
		$labels = [];

		foreach ($insurances = $case->registration->getSelectedInsurances() as $insurance) {
			if ($insurance->isAutoAccidentInsurance() || $insurance->isWorkersCompanyInsurance()) {
				$phone = $insurance->getInsuranceDataModel()->adjuster_phone;
			} else {
				$phone = $insurance->getInsuranceDataModel()->provider_phone;
			}
			if ($phone) {
				$labels[] = $phone;
			}
		}

		return implode(', ', $labels);
	}

	protected function formatTotalScheduledCharges($case)
	{
		$codingBillsChargesSum = 0;
		if ($case->coding->loaded()) {
			foreach ($case->coding->bills->find_all() as $bill) {
				$codingBillsChargesSum += $bill->charge;
			}
		}
		return '$' . number_format((float)$codingBillsChargesSum, 2, '.', ',');
	}

	protected function formatTotalCodedCharges($case)
	{
		$codingBillsChargesSum = 0;
		if ($case->coding->loaded()) {
			foreach ($case->coding->bills->find_all() as $bill) {
				$codingBillsChargesSum += $bill->charge * $bill->quantity;
			}
		}
		return '$' . number_format((float)$codingBillsChargesSum, 2, '.', ',');
	}

	protected function formatAnesthesiologist($case)
	{
		$names = [];

		foreach ($case->other_staff->find_all() as $user) {
			if ($user->profession_id == Profession::ANESTHESIOLOGIST) {
				$names[] = $user->getFullName();
			}
		}

		return implode(', ', $names);
	}

	protected function formatAnesthesiaType($case)
	{
		$types = \Opake\Model\Cases\Item::getAnesthesiaTypeList();
		return (isset($types[$case->anesthesia_type])) ? $types[$case->anesthesia_type] : '';
	}


	protected function formatCaseCanceledWithinOneDay($rowSource)
	{
		$caseCancellation = $rowSource->getCaseCancellation();
		if ($caseCancellation) {
			$dateOfService = $caseCancellation->dos;
			$dateOfCancellation = $caseCancellation->cancel_time;
			if ($dateOfService && $dateOfCancellation) {
				$dateOfService = TimeFormat::fromDBDatetime($dateOfService);
				$dateOfCancellation = TimeFormat::fromDBDatetime($dateOfCancellation);
				if ($dateOfService && $dateOfCancellation) {
					$diff = $dateOfService->getTimestamp() - $dateOfCancellation->getTimestamp();
					if (($diff > 0) && ($diff <= (3600 * 24))) {
						return 'Yes';
					}
				}
			}
		}

		return '';
	}

	protected function formatCaseRescheduled($rowSource)
	{
		$caseCancellation = $rowSource->getCaseCancellation();
		if ($caseCancellation) {
			$dateOfReschedule = $caseCancellation->rescheduled_date;
			if ($dateOfReschedule) {
				$dateOfReschedule = TimeFormat::fromDBDatetime($dateOfReschedule);
				if ($dateOfReschedule) {
					return 'Yes, ' . TimeFormat::getDateTime($dateOfReschedule);
				}
			}
		}
		return '';
	}

	protected function formatLocation($case)
	{
		return $case->location->name;
	}

	protected function formatPaymentsAmount($case, $rowSource)
	{
		$totals = $rowSource->getCaseTotals();
		return $this->_formatToMoney($totals['insurancePayments'] + $totals['patientPayments']);
	}

	protected function formatAdjustmentsAmount($case, $rowSource)
	{
		$totals = $rowSource->getCaseTotals();
		return $this->_formatToMoney($totals['adjustments']);
	}

	protected function formatWriteOffsAmount($case, $rowSource)
	{
		$totals = $rowSource->getCaseTotals();
		return $this->_formatToMoney($totals['writeOffs']);
	}

	protected function formatOutstandingBalance($case, $rowSource)
	{
		$totals = $rowSource->getCaseTotals();
		return $this->_formatToMoney($totals['outstandingBalance']);
	}

	protected function formatCoPay($case, $rowSource)
	{
		$details = $rowSource->	getCasePatientResponsibility();
		return $this->_formatToMoney($details['coPay']);
	}

	protected function formatDeductibles($case, $rowSource)
	{
		$details = $rowSource->getCasePatientResponsibility();
		return $this->_formatToMoney($details['deductible']);
	}

	protected function formatCoInsurance($case, $rowSource)
	{
		$details = $rowSource->getCasePatientResponsibility();
		return $this->_formatToMoney($details['coIns']);
	}

	protected function formatOOP($case, $rowSource)
	{
		$details = $rowSource->getCasePatientResponsibility();
		return $this->_formatToMoney($details['oop']);
	}


	protected function formatBillingStatus($case)
	{
		$list = \Opake\Model\Cases\Item::getManualBillingStatusesListDesc();
		return (isset($list[$case->billing_status])) ? $list[$case->billing_status] : '';
	}

	protected function formatVarCost($case, $rowSource)
	{
		$card = $case->getCard();
		if ($card->loaded()) {
			return $this->_formatToMoney($card->var_cost);
		}
		return '';
	}

	protected function _formatToMoney($amount)
	{
		return '$' . number_format($amount, 2, '.', ',');
	}
}
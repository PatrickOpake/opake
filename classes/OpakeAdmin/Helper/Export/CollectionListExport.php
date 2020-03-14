<?php

namespace OpakeAdmin\Helper\Export;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;
use Opake\Model\Insurance\AbstractType;
use PHPExcel_Style_Alignment;

class CollectionListExport
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var AbstractModel[]
	 */
	protected $models;


	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}


	/**
	 * @return \Opake\Model\AbstractModel[]
	 */
	public function getModels()
	{
		return $this->models;
	}

	/**
	 * @param \Opake\Model\AbstractModel[] $models
	 */
	public function setModels($models)
	{
		$this->models = $models;
	}

	/**
	 * @throws PHPExcel_Exception
	 */
	public function exportToExcel()
	{
		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', 600);

		$excel = new \PHPExcel();
		$excel->getProperties()
			->setCreator('Opake')
			->setLastModifiedBy('Opake')
			->setTitle('Collections')
			->setSubject('Collections');

		$sheet = $excel->getSheet(0);
		$sheet->setTitle('Collections');

		foreach($excel->getActiveSheet()->getRowDimensions() as $rd) {
			$rd->setRowHeight(-1);
		}


		$columns = $this->getColumns();

		foreach ($columns as $index => $name) {
			$sheet->setCellValueByColumnAndRow($index, 1, $name);
		}

		$rowNum = 2;
		foreach ($this->models as $model) {
			$interestPayments = $model->ledger_interest_payments->order_by('id', 'desc')->find_all()->as_array();
			$bills = $model->coding->getBills();

			$formattedRow = $this->formatCaseRow($model, $bills, $interestPayments);
			foreach ($formattedRow as $index => $value) {
				$sheet->setCellValueByColumnAndRow($index, $rowNum, $value);
				$sheet->getStyleByColumnAndRow($index, $rowNum)->getAlignment()
					->setWrapText(true)
					->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			}
			$sheet->getRowDimension($rowNum)->setRowHeight(-1);

			$rowNum++;

			foreach ($interestPayments as $interestPayment) {
				$formattedPayment = $this->formatInterestPaymentRow($interestPayment);
				foreach ($formattedPayment as $index => $value) {
					$sheet->setCellValueByColumnAndRow($index, $rowNum, $value);
					$sheet->getStyleByColumnAndRow($index, $rowNum)->getAlignment()
						->setWrapText(true)
						->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sheet->getDefaultColumnDimension()->setAutoSize(true);
				}

				$rowNum++;
			}

			foreach ($bills as $bill) {
				$formattedBillRow = $this->formatBillRow($bill);
				foreach ($formattedBillRow as $index => $value) {
					$sheet->setCellValueByColumnAndRow($index, $rowNum, $value);
					$sheet->getStyleByColumnAndRow($index, $rowNum)->getAlignment()
						->setWrapText(true)
						->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sheet->getDefaultColumnDimension()->setAutoSize(true);
				}

				$rowNum++;
			}
		}

		$highestColumn = $sheet->getHighestColumn();
		for ($col = ord('a'); $col <= ord(strtolower($highestColumn)); $col++) {
			$sheet->getColumnDimension(chr($col))->setAutoSize(true);
		}


		$writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$tmpPath = tempnam(sys_get_temp_dir(), 'opk');
		$writer->save($tmpPath);

		if (is_file($tmpPath)) {
			/** @var \Opake\Model\UploadedFile $uploadedFile */
			$uploadedFile = $this->pixie->orm->get('UploadedFile');
			$uploadedFile->storeContent($this->getFileName(), file_get_contents($tmpPath), [
				'is_protected' => true,
				'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
			]);
			$uploadedFile->save();

			unlink($tmpPath);

			return $uploadedFile;
		}

		return null;
	}

	/**
	 * @return array
	 */
	protected function formatCaseRow($model, $bills, $interestPayments)
	{
		$patient = $model->registration->patient;
		$data = [
			$patient->getFullMrn(),
			$patient->last_name,
			$patient->first_name,
			$model->id(),
			TimeFormat::getDate($model->time_start),
			$this->formatPrimaryPayerType($model),
			$this->formatPrimaryPayerName($model),
			$this->formatPrimaryPayerPhone($model),
			$this->formatPrimaryPolicyNumber($model),
			$this->formatSecondaryPayerType($model),
			$this->formatSecondaryPayerName($model),
			$this->formatSecondaryPayerPhone($model),
			$this->formatSecondaryPolicyNumber($model),
			'',
			$this->formatSurgeon($model),
			$this->formatBalance($bills),
			$this->formatCharges($bills, $interestPayments),
			$this->formatPayments($bills, $interestPayments),
			$this->formatAdjustment($bills),
			$this->formatNotes($model),

		];
		return $data;
	}

	/**
	 * @param $payment
	 * @return array
	 */
	protected function formatInterestPaymentRow($payment)
	{
		$data = [
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'INT',
			'',
			'',
			$this->_formatFloatToMoney($payment->amount),
			$this->_formatFloatToMoney($payment->amount),
			'',
			''
		];
		return $data;
	}

	/**
	 * @return array
	 */
	protected function formatBillRow($bill)
	{
		$data = [
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			$this->formatServiceCode($bill),
			'',
			$this->formatBillRemainder($bill),
			$this->_formatFloatToMoney($bill->charge),
			$this->formatBillPayment($bill),
			$this->formatBillAdjustments($bill),
			''
		];
		return $data;
	}

	protected function getColumns()
	{
		$columns = [];
		$columns[] = 'MRN';
		$columns[] = 'Last Name';
		$columns[] = 'First Name';
		$columns[] = 'Case #';
		$columns[] = 'DOS';
		$columns[] = 'Primary Payer Type';
		$columns[] = 'Primary Payer Name';
		$columns[] = 'Primary Phone Number';
		$columns[] = 'Primary Policy or ID number';
		$columns[] = 'Secondary Payer Type';
		$columns[] = 'Secondary Payer Name';
		$columns[] = 'Secondary Phone Number';
		$columns[] = 'Secondary Policy or ID number';
		$columns[] = 'HCPCS/CPT';
		$columns[] = 'Provider';
		$columns[] = 'Balance';
		$columns[] = 'Charges';
		$columns[] = 'Payment';
		$columns[] = 'Adjustments';
		$columns[] = 'Notes';

		return $columns;
	}

	protected function getFileName()
	{
		return 'Collections_export.xlsx';
	}

	protected function formatPrimaryPayerType($model)
	{
		$insurance = $model->registration->getPrimaryInsurance();
		if($insurance) {
			$insuranceTypes = AbstractType::getInsuranceTypesList();
			return (isset($insuranceTypes[$insurance->type])) ? $insuranceTypes[$insurance->type] : '';
		}
		return '';
	}

	protected function formatPrimaryPayerName($model)
	{
		$primaryInsurance = $model->registration->getPrimaryInsurance();
		if(!$primaryInsurance) {
			return null;
		}

		$insuranceDataModel = $primaryInsurance->getInsuranceDataModel();
		if ($primaryInsurance->isRegularInsurance()) {
			if(empty($insuranceDataModel->insurance->name)) {
				return $this->formatPrimaryPayerType($model);
			}
			return $insuranceDataModel->insurance->name;
		} else if($primaryInsurance->isAutoAccidentInsurance() || $primaryInsurance->isWorkersCompanyInsurance()) {
			return $insuranceDataModel->insurance_company->name;
		}

		return 	$this->formatPrimaryPayerType($model);
	}

	protected function formatPrimaryPayerPhone($model)
	{
		$primaryInsurance = $model->registration->getPrimaryInsurance();
		if ($primaryInsurance && $primaryInsurance->isRegularInsurance()) {
			return $primaryInsurance->getInsuranceDataModel()->phone;
		} else if($primaryInsurance && ($primaryInsurance->isAutoAccidentInsurance() || $primaryInsurance->isWorkersCompanyInsurance())) {
			return $primaryInsurance->getInsuranceDataModel()->insurance_company_phone;
		}

		return null;
	}

	protected function formatPrimaryPolicyNumber($model)
	{
		$primaryInsurance = $model->registration->getPrimaryInsurance();
		if ($primaryInsurance && $primaryInsurance->isRegularInsurance()) {
			return $primaryInsurance->getInsuranceDataModel()->policy_number;
		}

		return null;
	}

	protected function formatSecondaryPayerType($model)
	{
		$insurance = $model->registration->getSecondaryInsurance();
		if($insurance) {
			$insuranceTypes = AbstractType::getInsuranceTypesList();
			return (isset($insuranceTypes[$insurance->type])) ? $insuranceTypes[$insurance->type] : '';
		}
		return '';
	}

	protected function formatSecondaryPayerName($model)
	{
		$insurance = $model->registration->getSecondaryInsurance();
		if(!$insurance) {
			return null;
		}

		$insuranceDataModel = $insurance->getInsuranceDataModel();
		if ($insurance->isRegularInsurance()) {
			if(empty($insuranceDataModel->insurance->name)) {
				return $this->formatSecondaryPayerType($model);
			}
			return $insuranceDataModel->insurance->name;
		} else if($insurance->isAutoAccidentInsurance() || $insurance->isWorkersCompanyInsurance()) {
			return $insuranceDataModel->insurance_company->name;
		}

		return $this->formatSecondaryPayerType($model);
	}

	protected function formatSecondaryPayerPhone($model)
	{
		$insurance = $model->registration->getSecondaryInsurance();
		if ($insurance && $insurance->isRegularInsurance()) {
			return $insurance->getInsuranceDataModel()->phone;
		} else if($insurance && ($insurance->isAutoAccidentInsurance() || $insurance->isWorkersCompanyInsurance())) {
			return $insurance->getInsuranceDataModel()->insurance_company_phone;
		}

		return null;
	}

	protected function formatSecondaryPolicyNumber($model)
	{
		$insurance = $model->registration->getSecondaryInsurance();
		if ($insurance && $insurance->isRegularInsurance()) {
			return $insurance->getInsuranceDataModel()->policy_number;
		}

		return null;
	}

	protected function formatSurgeon($model)
	{
		if($firstSurgeon = $model->getFirstSurgeon()) {
			return $firstSurgeon->getFullName();
		}
	}

	protected function formatCharges($bills, $interestPayments)
	{
		$result = 0;
		foreach ($bills as $bill)
		{
			$result += $bill->charge;
		}
		foreach ($interestPayments as $interestPayment) {
			$result += $interestPayment->amount;
		}
		return $this->_formatFloatToMoney($result);
	}

	protected function formatPayments($bills, $interestPayments)
	{
		$result = 0;
		foreach ($bills as $bill)
		{
			$result += $bill->getPayment();
		}
		foreach ($interestPayments as $interestPayment) {
			$result += $interestPayment->amount;
		}
		return $this->_formatFloatToMoney($result);
	}

	protected function formatAdjustment($bills)
	{
		$result = 0;
		foreach ($bills as $bill)
		{
			$result += $bill->getAdjustment() + $bill->getWriteOff();
		}
		return $this->_formatFloatToMoney($result);
	}

	protected function formatBalance($bills)
	{
		$result = 0;
		foreach ($bills as $bill)
		{
			$result += $bill->getRemainder();
		}
		return $this->_formatFloatToMoney($result);
	}

	protected function formatWriteOff($bills)
	{
		$result = 0;
		foreach ($bills as $bill)
		{
			$result += $bill->getWriteOff();
		}
		return $this->_formatFloatToMoney($result);
	}

	protected function formatServiceCode($model)
	{
		$chargeMasterRecord = $model->getChargeMasterEntry();
		if ($chargeMasterRecord) {
			return $chargeMasterRecord->cpt;
		}

		return '';
	}

	protected function formatBillPayment($model)
	{
		$sumAmount = $model->getPayment();
		return $this->_formatFloatToMoney($sumAmount);
	}

	protected function formatBillAdjustments($model)
	{
		$adjustment = $model->getAdjustment();
		$amount = $model->getWriteOff();
		return $this->_formatFloatToMoney($adjustment + $amount);
	}

	protected function formatBillRemainder($model)
	{
		$amount = $model->getRemainder();
		return $this->_formatFloatToMoney($amount);
	}

	protected function formatBillWriteOff($model)
	{
		$amount = $model->getWriteOff();
		return $this->_formatFloatToMoney($amount);
	}

	protected function formatNotes($model)
	{
		$notes = [];
		foreach ($model->getBillingNotes() as $item) {
			$date = TimeFormat::fromDBDatetime($item->time_add);
			$notes[] = $item->user->getFullName() . ' (' . TimeFormat::getDate($date) . ' ' . TimeFormat::getTime($date) . '): ' . $item->text;
		}

		$result = implode("\n", $notes);

		return $result;
	}

	protected function _formatFloatToMoney($float)
	{
		return '$' . number_format((float) $float, 2, '.', ',');
	}
}
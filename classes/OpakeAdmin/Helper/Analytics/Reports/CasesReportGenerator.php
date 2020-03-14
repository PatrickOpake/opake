<?php

namespace OpakeAdmin\Helper\Analytics\Reports;

use Opake\Helper\TimeFormat;
use Opake\Model\Analytics\Reports\CustomReport;
use Opake\Model\Billing\Navicure\Claim;
use Opake\Model\Insurance\AbstractType;
use OpakeAdmin\Helper\Analytics\Reports\CasesReport\Formatter;
use OpakeAdmin\Helper\Analytics\Reports\CasesReport\RowSource\BaseCaseRowSource;
use OpakeAdmin\Helper\Analytics\Reports\CasesReport\RowSource\CaseCancellationRowSource;
use OpakeAdmin\Helper\Analytics\Reports\CasesReport\RowSource\InventoryItemRowSource;
use OpakeAdmin\Helper\Analytics\Reports\CasesReport\RowSource\ProcedureRowSource;

class CasesReportGenerator
{

	const TYPE_CANCELED_CASES = 7;
	const TYPE_PROCEDURES_REPORT = 8;
	const TYPE_CASES_REPORT = 9;

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var int
	 */
	protected $organizationId;

	/**
	 * @var \DateTime
	 */
	protected $dateFrom;

	/**
	 * @var \DateTime
	 */
	protected $dateTo;

	/**
	 * @var array
	 */
	protected $columns;

	/**
	 * @var int
	 */
	protected $reportType;

	/**
	 * @var CustomReport
	 */
	protected $customReport;

	/**
	 * @var int
	 */
	protected $customReportType;

	/**
	 * @var array
	 */
	protected $practiceGroups;

	/**
	 * @var array
	 */
	protected $surgeons;

	/**
	 * @var array
	 */
	protected $insurances;

	/**
	 * @var array
	 */
	protected $procedures;

	/**
	 * @var array
	 */
	protected $inventoryItems;

	/**
	 * @var array
	 */
	protected $insuranceTypes;

	/**
	 * @var array
	 */
	protected $manufacturers;

	/**
	 * @var array
	 */
	protected $inventoryItemTypes;

	/**
	 * @var array
	 */
	protected $billingStatuses;

	/**
	 * @var string
	 */
	protected $delimiter = ',';

	/**
	 * @var array
	 */
	protected static $procedureColumns = [
		'coded_procedures',
		'case_procedure1',
		'case_procedure2',
		'case_procedure3',
		'case_procedure4'
	];

	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}

	/**
	 * @return int
	 */
	public function getOrganizationId()
	{
		return $this->organizationId;
	}

	/**
	 * @param int $organizationId
	 */
	public function setOrganizationId($organizationId)
	{
		$this->organizationId = $organizationId;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateFrom()
	{
		return $this->dateFrom;
	}

	/**
	 * @param \DateTime $dateFrom
	 */
	public function setDateFrom($dateFrom)
	{
		$this->dateFrom = $dateFrom;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateTo()
	{
		return $this->dateTo;
	}

	/**
	 * @param \DateTime $dateTo
	 */
	public function setDateTo($dateTo)
	{
		$this->dateTo = $dateTo;
	}

	/**
	 * @return array
	 */
	public function getColumns()
	{
		return $this->columns;
	}

	/**
	 * @param array $columns
	 */
	public function setColumns($columns)
	{
		$this->columns = $columns;
	}

	/**
	 * @return int
	 */
	public function getReportType()
	{
		return $this->reportType;
	}

	/**
	 * @param int $reportType
	 */
	public function setReportType($reportType)
	{
		$this->reportType = $reportType;
	}

	/**
	 * @return CustomReport
	 */
	public function getCustomReport()
	{
		return $this->customReport;
	}

	/**
	 * @return int
	 */
	public function getCustomReportType()
	{
		return $this->customReportType;
	}

	/**
	 * @param CustomReport|null $customReport
	 */
	public function setCustomReport($customReport)
	{
		$this->customReport = $customReport && $customReport->loaded() ? $customReport : null;
		$this->customReportType = $this->customReport ? (int)$this->customReport->parent_id : null;
	}

	/**
	 * @return array
	 */
	public function getPracticeGroups()
	{
		return $this->practiceGroups;
	}

	/**
	 * @param array $practiceGroups
	 */
	public function setPracticeGroups($practiceGroups)
	{
		$this->practiceGroups = $practiceGroups;
	}

	/**
	 * @return array
	 */
	public function getInsurances()
	{
		return $this->insurances;
	}

	/**
	 * @param array $insurances
	 */
	public function setInsurances($insurances)
	{
		$this->insurances = $insurances;
	}

	/**
	 * @return array
	 */
	public function getSurgeons()
	{
		return $this->surgeons;
	}

	/**
	 * @param array $surgeons
	 */
	public function setSurgeons($surgeons)
	{
		$this->surgeons = $surgeons;
	}

	/**
	 * @return array
	 */
	public function getProcedures()
	{
		return $this->procedures;
	}

	/**
	 * @param array $procedures
	 */
	public function setProcedures($procedures)
	{
		$this->procedures = $procedures;
	}

	/**
	 * @return array
	 */
	public function getInventoryItems()
	{
		return $this->inventoryItems;
	}

	/**
	 * @param array $inventoryItems
	 */
	public function setInventoryItems($inventoryItems)
	{
		$this->inventoryItems = $inventoryItems;
	}

	/**
	 * @return array
	 */
	public function getManufacturers()
	{
		return $this->manufacturers;
	}

	/**
	 * @param array $manufacturers
	 */
	public function setManufacturers($manufacturers)
	{
		$this->manufacturers = $manufacturers;
	}

	/**
	 * @return array
	 */
	public function getInventoryItemTypes()
	{
		return $this->inventoryItemTypes;
	}

	/**
	 * @param array $inventoryItemTypes
	 */
	public function setInventoryItemTypes($inventoryItemTypes)
	{
		$this->inventoryItemTypes = $inventoryItemTypes;
	}

	/**
	 * @return array
	 */
	public function getBillingStatuses()
	{
		return $this->inventoryItemTypes;
	}

	/**
	 * @param array $billingStatuses
	 */
	public function setBillingStatuses($billingStatuses)
	{
		$this->billingStatuses = $billingStatuses;
	}

	/**
	 * @param array $insuranceType
	 */
	public function setInsuranceTypes($insuranceTypes)
	{
		$this->insuranceTypes = $insuranceTypes;
	}

	public function generate()
	{
		$excel = new \PHPExcel();
		$excel->getProperties()
			->setCreator('Opake')
			->setLastModifiedBy('Opake')
			->setTitle('Analytics_Report')
			->setSubject('Analytics_Report');

		$sheet = $excel->getSheet(0);
		$sheet->setTitle('Analytics Report');

		$columnLabels = static::getColumnLabels();

		$orderedColumnsList = [];

		foreach ($columnLabels as $name => $label) {
			if (in_array($name, $this->columns)) {
				if (in_array($name, static::$procedureColumns)) {
					$orderedColumnsList[] = $name . '_hcpcs';
					$orderedColumnsList[] = $name . '_desc';
					$orderedColumnsList[] = $name . '_amount';
				} else {
					$orderedColumnsList[] = $name;
				}
			}
		}

		foreach ($orderedColumnsList as $index => $columnName) {
			$sheet->setCellValueByColumnAndRow($index, 1, (isset($columnLabels[$columnName])) ? $columnLabels[$columnName] : $columnName);
		}

		$highestColumn = $sheet->getHighestColumn();
		for ($col = ord('a'); $col <= ord(strtolower($highestColumn)); $col++) {
			$sheet->getColumnDimension(chr($col))->setAutoSize(true);
		}

		$formatter = new Formatter();
		$rowSources = $this->getRowSources();
		$rowIndex = 2;
		foreach ($rowSources as $rowSource) {

			try {
				foreach ($orderedColumnsList as $index => $column) {
					$sheet->setCellValueByColumnAndRow($index, $rowIndex, $formatter->formatColumn($column, $rowSource));
				}
				$rowIndex++;
			} catch (\Exception $e) {
				$this->pixie->logger->exception($e);
			}
		}

		$highestRow = $sheet->getHighestRow();
		$sheet->getStyle('A1:'.$highestColumn . $highestRow)->getAlignment()->setWrapText(true);


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

			$model = $this->pixie->orm->get('Analytics_Reports_GeneratedReport');
			$model->file_id = $uploadedFile->id();
			$model->generateAccessKey();

			$model->save();

			return $model;
		}

		return null;

	}

	protected function getRowSources()
	{
		$model = $this->pixie->orm->get('Cases_Item');

		$query = $model->query;
		$query->fields('case.*');
		$query->where('organization_id', $this->organizationId);
		$query->group_by('case.id');

		if ($this->dateFrom) {
			$query->where($this->pixie->db->expr('DATE(case.time_start)'), '>=', TimeFormat::formatToDB($this->dateFrom) . ' 00:00:00');
		}

		if ($this->dateTo) {
			$query->where($this->pixie->db->expr('DATE(case.time_start)'), '<=', TimeFormat::formatToDB($this->dateTo) . ' 23:59:59');
		}

		if ($this->practiceGroups || $this->surgeons) {
			$query->join('case_user', ['case_user.case_id', 'case.id'], 'inner');
		}

		if ($this->practiceGroups) {
			$query->join('user_practice_groups', ['case_user.user_id', 'user_practice_groups.user_id'], 'inner');
			$query->where('user_practice_groups.practice_group_id' ,'IN', $this->pixie->db->arr($this->practiceGroups));
		}

		if ($this->surgeons) {
			$query->where('case_user.user_id', 'IN', $this->pixie->db->arr($this->surgeons));
		}

		if($this->billingStatuses) {
			$query->where($model->table . '.billing_status', 'IN', $this->pixie->db->arr($this->billingStatuses));
		}

		if ($this->insurances || $this->insuranceTypes) {
			$query->join('case_registration', ['case.id', 'case_registration.case_id'], 'inner');
			$query->join('case_registration_insurance_types', ['case_registration_insurance_types.registration_id', 'case_registration.id'], 'inner');
			$query->join('insurance_data_auto_accident', [
				'and', [
					['case_registration_insurance_types.insurance_data_id', 'insurance_data_auto_accident.id'],
					['and', ['case_registration_insurance_types.type', $this->pixie->db->expr(AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT)]]
				]
			], 'left');
			$query->join('insurance_data_workers_comp', [
				'and', [
					['case_registration_insurance_types.insurance_data_id', 'insurance_data_workers_comp.id'],
					['and', ['case_registration_insurance_types.type', $this->pixie->db->expr(AbstractType::INSURANCE_TYPE_WORKERS_COMP)]]
				]
			], 'left');
			$query->join('insurance_data_regular', [
				'and', [
					['case_registration_insurance_types.insurance_data_id', 'insurance_data_regular.id'],
					['and', ['case_registration_insurance_types.type', 'IN', $this->pixie->db->arr(AbstractType::getRegularInsuranceTypeIds())]]
				]
			], 'left');


			$query->where('case_registration_insurance_types.deleted', 0);
			$query->where('case_registration_insurance_types.order', 'IS NOT NULL', $this->pixie->db->expr(''));

			if ($this->insurances) {
				$insuranceCompanies = $this->pixie->db->arr($this->insurances);
				$query->where('and', [
					['insurance_data_regular.insurance_id', 'IN', $insuranceCompanies],
					['or', ['insurance_data_auto_accident.insurance_company_id', 'IN', $insuranceCompanies]],
					['or', ['insurance_data_workers_comp.insurance_company_id', 'IN', $insuranceCompanies]],
				]);
			}

			if ($this->insuranceTypes) {
				$query->where('case_registration_insurance_types.order', 1);

				if (in_array('self_funded', $this->insuranceTypes)) {
					$query->where('and', [
						['insurance_data_regular.is_self_funded', 1],
						['or', ['case_registration_insurance_types.type', 'IN', $this->pixie->db->arr($this->insuranceTypes)]],
					]);
				} else {
					$query->where('case_registration_insurance_types.type', 'IN', $this->pixie->db->arr($this->insuranceTypes));
				}
			}

		}

		if ($this->procedures) {
			$query->join('case_additional_type', ['case.id', 'case_additional_type.case_id'], 'inner');
			$query->where('and', [
				['or', ['case_additional_type.type_id', 'IN', $this->pixie->db->arr($this->procedures)]],
				['or', ['case.type_id', 'IN', $this->pixie->db->arr($this->procedures)]]
			]);
		}

		if ($this->reportType === CasesReportGenerator::TYPE_CANCELED_CASES
			|| $this->customReportType === CasesReportGenerator::TYPE_CANCELED_CASES) {
			$query->join('case_cancellation', ['case_cancellation.case_id', 'case.id'], 'inner');
		} else {
			$model->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED);
		}

		$model->order_by('case.time_start', 'DESC');

		$models = $model->find_all();

		$cases = [];
		foreach ($models as $case) {
			if ($case->registration->loaded()) {
				$cases[] = $case;
			}
		}

		$hasInventoryColumns = $this->isColumnsContainsInventoryItems();
		if ($this->inventoryItems || $this->manufacturers || $this->inventoryItemTypes || $hasInventoryColumns) {
			$sources = [];
			foreach ($cases as $case) {
				$cancellation = null;
				if ($this->reportType === CasesReportGenerator::TYPE_CANCELED_CASES
					|| $this->customReportType === CasesReportGenerator::TYPE_CANCELED_CASES) {
					$cancellation = $this->pixie->orm->get('Cases_Cancellation')
						->where('case_id', $case->id())
						->order_by('id', 'DESC')
						->limit(1)
						->find();
					if (!$cancellation->loaded()) {
						$cancellation = null;
					}
				}

				$caseCard = $case->getCard();

				/** @var InventoryItemRowSource[] $caseRowSources */
				$caseRowSources = [];
				if ($caseCard->loaded()) {
					foreach ($caseCard->items->find_all() as $item) {
						if ($item->inventory->loaded()) {
							$caseRowSources[] = new InventoryItemRowSource($case, $cancellation, $item);
						}
					}
				}

				if ($caseRowSources) {
					if ($this->inventoryItems) {
						foreach ($caseRowSources as $index => $item) {
							if (!in_array($item->getInventoryItem()->id(), $this->inventoryItems)) {
								unset($caseRowSources[$index]);
							}
						}
					}

					if ($this->manufacturers) {
						foreach ($caseRowSources as $index => $item) {
							if (!in_array($item->getInventoryItem()->manf_id, $this->manufacturers)) {
								unset($caseRowSources[$index]);
							}
						}
					}

					if ($this->inventoryItemTypes) {
						foreach ($caseRowSources as $index => $item) {
							if (!in_array($item->getInventoryItem()->type, $this->inventoryItemTypes)) {
								unset($caseRowSources[$index]);
							}
						}
					}

					$sources = array_merge($sources, $caseRowSources);
				}

			}


			return $sources;
		}

		if ($this->reportType === CasesReportGenerator::TYPE_PROCEDURES_REPORT
			|| $this->customReportType === CasesReportGenerator::TYPE_PROCEDURES_REPORT) {
			$sources = [];
			foreach ($cases as $case) {
				$caseCoding = $case->coding;
				if ($caseCoding->loaded()) {
					foreach ($caseCoding->bills->find_all() as $bill) {
						$sources[] = new ProcedureRowSource($case, $bill);
					}
				}
			}

			return $sources;
		}

		if ($this->reportType === CasesReportGenerator::TYPE_CANCELED_CASES
			|| $this->customReportType === CasesReportGenerator::TYPE_CANCELED_CASES) {
			$sources = [];
			foreach ($cases as $case) {
				$cancellation = null;
				$cancellation = $this->pixie->orm->get('Cases_Cancellation')
					->where('case_id', $case->id())
					->order_by('id', 'DESC')
					->limit(1)
					->find();
				if (!$cancellation->loaded()) {
					$cancellation = null;
				}
				$sources[] = new CaseCancellationRowSource($case, $cancellation);
			}

			return $sources;
		}

		if(in_array('coded_procedures', $this->columns)) {
			$sources = [];
			foreach ($cases as $case) {
				$caseCoding = $case->coding;
				if ($caseCoding->loaded()) {
					foreach ($caseCoding->bills->find_all() as $bill) {
						$sources[] = new ProcedureRowSource($case, $bill);
					}
				}
			}

			return $sources;
		}

		$sources = [];
		foreach ($cases as $case) {
			$sources[] = new BaseCaseRowSource($case);
		}

		return $sources;
	}

	protected function getFileName()
	{
		$nameParts = [];
		if ($this->customReport) {
			$nameParts[] = $this->customReport->name;
		}
		else {
			$labels = static::getReportTypeLabels();
			$nameParts[] = (isset($labels[$this->reportType])) ? $labels[$this->reportType] : 'Report-' . $this->reportType;
		}

		if ($this->dateFrom) {
			$nameParts[] = $this->dateFrom->format('MY');
		}

		if ($this->dateTo) {
			$nameParts[] = $this->dateTo->format('MY');
		}

		return implode('_', $nameParts) . '.xlsx';
	}

	protected function isColumnsContainsInventoryItems()
	{
		$inventoryColumns = static::getInventoryColumns();
		foreach ($this->columns as $columnName) {
			if (in_array($columnName, $inventoryColumns)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return array
	 */
	public static function getReportTypeLabels()
	{
		return [
			1 => 'Billing',
			2 => 'Patients',
			3 => 'Surgeon',
			4 => 'Inventory',
			7 => 'Canceled Cases',
			8 => 'Procedures Report',
			9 => 'Cases Report',
			10 => 'Other'
		];
	}

	public static function getInventoryColumns()
	{
		return [
			'inventory_item_name',
			'inventory_item_number',
			'inventory_item_description',
			'inventory_qty_requested',
			'inventory_qty_used',
			'inventory_unit_price',
			'inventory_charge_amount',
			'inventory_manufacturer'
		];
	}

	/**
	 * @return array
	 */
	public static function getColumnLabels()
	{
		return [
			'case_id' => 'Case Number',
			'case_start_time' => 'Scheduled Start Time',
			'case_end_time' => 'Scheduled End Time',
			'case_actual_start_time' => 'Actual Start Time',
			'case_actual_end_time' => 'Actual End Time',
			'case_physician' => 'Physician',
			'case_special_equipment_flag' => 'Special Equipment (yes/no)',
			'case_special_equipment' => 'Special Equipment',
			'case_implants_flag' => 'Implants (yes/no)',
			'case_implants' => 'Implants',
			'case_actual_duration' => 'Actual Duration',
			'case_duration' => 'Scheduled Duration',
			'case_procedure1' => 'Procedure 1',
			'case_procedure1_hcpcs' => 'Procedure 1 - HCPCS/CPT',
			'case_procedure1_desc' => 'Procedure 1 - Description',
			'case_procedure1_amount' => 'Procedure 1 - Charge amount',
			'case_procedure2' => 'Procedure 2',
			'case_procedure2_hcpcs' => 'Procedure 2 - HCPCS/CPT',
			'case_procedure2_desc' => 'Procedure 2 - Description',
			'case_procedure2_amount' => 'Procedure 2 - Charge amount',
			'case_procedure3' => 'Procedure 3',
			'case_procedure3_hcpcs' => 'Procedure 3 - HCPCS/CPT',
			'case_procedure3_desc' => 'Procedure 3 - Description',
			'case_procedure3_amount' => 'Procedure 3 - Charge amount',
			'case_procedure4' => 'Procedure 4',
			'case_procedure4_hcpcs' => 'Procedure 4 - HCPCS/CPT',
			'case_procedure4_desc' => 'Procedure 4 - Description',
			'case_procedure4_amount' => 'Procedure 4 - Charge amount',
			'coded_procedures' => 'Coded Procedures',
			'coded_procedures_hcpcs' => 'Coded Procedures - HCPCS/CPT',
			'coded_procedures_desc' => 'Coded Procedures - Description',
			'coded_procedures_amount' => 'Coded Procedures - Charge amount',
			'case_description' => 'Description',
			'case_date_of_service' => 'Date of Service',
			'case_doctor' => 'Doctor',
			'case_anesthesiologist' => 'Anesthesiologist',
			'case_anesthesia_type' => 'Anesthesia Type',
			'patient_id' => 'Patient ID',
			'patient_mrn' => 'MRN',
			'patient_last_name' => 'Patient Last Name',
			'patient_first_name' => 'Patient First Name',
			'patient_date_of_birth' => 'Date of Birth',
			'patient_country' => 'Country',
			'patient_city' => 'City',
			'patient_state' => 'State',
			'patient_street_address' => 'Address 1',
			'patient_street_address_2' => 'Address 2',
			'patient_zip' => 'Zip',
			'patient_phone_number' => 'Phone Number',
			'insurance_company' => 'Insurance Company',
			'primary_insurance_type' => 'Primary Insurance Type',
			'insurance_phone' => 'Insurance Phone #',
			'patient_insurance_id' => 'Patient\'s Insurance ID',
			'total_scheduled_charges' => 'Total Scheduled Charges',
			'total_coded_charges' => 'Total Coded Charges',
			'inventory_item_name' => 'Item Name',
			'inventory_item_number' => 'Item Number',
			'inventory_item_description' => 'Item Description',
			'inventory_qty_requested' => 'Qty Requested',
			'inventory_qty_used' => 'Qty Used',
			'inventory_unit_price' => 'Unit Price',
			'inventory_charge_amount' => 'Charge Amount',
			'inventory_manufacturer' => 'Manufacturer',
		    'case_canceled_within_one_day' => 'Canceled within 1 day',
		    'case_rescheduled' => 'Rescheduled',
			'clinical_notes' => 'Clinical Notes',
			'billing_notes' => 'Billing Notes',
			'type_of_claim' => 'Type of Claim(s)',
			'date_of_submission_claim' => 'Date of Submission',
			'primary_insurance' => 'Primary Insurance',
			'secondary_insurance' => 'Secondary Insurance',
			'tertiary_insurance' => 'Tertiary Insurance',
			'quaternary_insurance' => 'Quaternary Insurance',
			'other_insurance' => 'Other Insurance',
		    'or' => 'OR',
		    'payments_amount' => 'Payments',
		    'adjustments_amount' => 'Adjustments',
		    'write_offs_amount' => 'Write-Offs',
		    'outstanding_balance' => 'Balance',
		    'ar_billing_status' => 'Billing Status',
		    'insurance_type_acronym' => 'Insurance Type',
		    'co_pay' => 'Co-Pay',
		    'co_insurance' => 'Co-Insurance',
		    'deductible' => 'Deductible',
		    'oop' => 'OOP',
		    'bill_procedure_cpt' => 'Coded Procedures - HCPCS/CPT',
		    'bill_procedure_description' => 'Coded Procedures - Description',
		    'bill_procedure_charge_amount' => 'Coded Procedures - Charge amount',
		    'var_cost' => 'Variable Cost'
		];
	}
}
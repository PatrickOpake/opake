<?php

namespace OpakeAdmin\Helper\Printing\Document\Booking;

use Opake\Model\Insurance\AbstractType;
use OpakeAdmin\Helper\Printing\Document\PDFCompileDocument;

class BookingForm extends PDFCompileDocument
{
	/**
	 * @var mixed
	 */
	protected $bookingData;

	/**
	 * @param mixed $bookingData
	 */
	public function __construct($bookingData)
	{
		$this->bookingData = $bookingData;
	}

	/**
	 * @return string
	 */
	public function getFileName()
	{
		return 'BookingForm.pdf';
	}

	/**
	 * @return \Opake\View\View
	 * @throws \Exception
	 */
	protected function generateView()
	{
		$app = \Opake\Application::get();

		$bookingSheet = $this->bookingData;

		$view = $app->view('booking/export/sheet');

		$patient = $bookingSheet->patient;
		if(isset($bookingSheet->patient->first_name) && isset($bookingSheet->patient->last_name)) {
			$patient->fullName = $bookingSheet->patient->first_name . ' ' . $bookingSheet->patient->last_name;
		}
		if(isset($bookingSheet->patient->mrn) && isset($bookingSheet->patient->mrn_year)) {
			$patient->fullMrn = $bookingSheet->patient->mrn . '-' . $bookingSheet->patient->mrn_year;
		}
		$view->booking = $bookingSheet;
		$view->patient = $patient;
		$view->surgeons = $bookingSheet->users;
		$view->assistants = [];
		if(isset($bookingSheet->assistant)) {
			$view->assistants = $bookingSheet->assistant;
		}

		$additional_cpts = [];
		foreach ($bookingSheet->additional_cpts as $item) {
			$fullName = '';
			if ($item->code) {
				$fullName .= $item->code . ' - ';
			}
			$fullName .= $item->name;

			$item->fullname = $fullName;
			$additional_cpts[] = $item;
		}

		$insurances = [];
		$insuranceTypes = AbstractType::getInsuranceTypesList();
		foreach ($bookingSheet->insurances as $insurance) {
			if (isset($insurance->type)) {
				$insurance->isAutoAccidentInsurance = $insurance->type == AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT;
				$insurance->isWorkersCompanyInsurance = $insurance->type == AbstractType::INSURANCE_TYPE_WORKERS_COMP;
				$insurance->isDescriptionInsurance = $insurance->type == AbstractType::INSURANCE_TYPE_LOP || $insurance->type == AbstractType::INSURANCE_TYPE_SELF_PAY;
				$insurance->isRegularInsurance = !$insurance->isAutoAccidentInsurance && !$insurance->isWorkersCompanyInsurance && !$insurance->isDescriptionInsurance;
				$insurance->isInsuranceCompanyEqualsType = ($insurance->type == AbstractType::INSURANCE_TYPE_MEDICARE || $insurance->type == AbstractType::INSURANCE_TYPE_TRICARE ||
					$insurance->type == AbstractType::INSURANCE_TYPE_CHAMPVA || $insurance->type == AbstractType::INSURANCE_TYPE_FECA_BLACK_LUNG);
				$insurance->insuranceTypeTitle = (isset($insuranceTypes[$insurance->type]) ? $insuranceTypes[$insurance->type] : '');
				$insurances[] = $insurance;
			}
		}

		$view->additional_cpts = $additional_cpts;
		$view->admitting_diagnosis = $bookingSheet->admitting_diagnosis;
		$view->secondary_diagnosis = [];
		if(isset($bookingSheet->secondary_diagnosis)) {
			$view->secondary_diagnosis = $bookingSheet->secondary_diagnosis;
		}
		$view->pre_op_required_data = $bookingSheet->pre_op_required_data;
		$view->studies_ordered = $bookingSheet->studies_ordered;
		$view->insurances = $insurances;

		return $view;
	}

}
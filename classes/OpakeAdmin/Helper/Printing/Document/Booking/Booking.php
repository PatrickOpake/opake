<?php

namespace OpakeAdmin\Helper\Printing\Document\Booking;

use OpakeAdmin\Helper\Printing\Document\PDFCompileDocument;

class Booking extends PDFCompileDocument
{
	/**
	 * @var \Opake\Model\Booking
	 */
	protected $booking;

	/**
	 * @param \Opake\Model\Booking $booking
	 */
	public function __construct($booking)
	{
		$this->booking = $booking;
	}

	/**
	 * @return string
	 */
	public function getFileName()
	{
		return 'Booking_' . $this->booking->id() . '.pdf';
	}

	/**
	 * @return \Opake\View\View
	 * @throws \Exception
	 */
	protected function generateView()
	{
		$app = \Opake\Application::get();

		$bookingSheet = $this->booking;
		if (!$bookingSheet->loaded()) {
			throw new \Exception('Booking sheet is not loaded');
		}

		$view = $app->view('booking/export/sheet');
		if(!$bookingSheet->patient->loaded() && $bookingSheet->booking_patient->loaded()) {
			$patient = $bookingSheet->booking_patient;
			$patient->fullName = $bookingSheet->booking_patient->getFullName();
		} else {
			$patient = $bookingSheet->patient;
			$patient->fullName = $bookingSheet->patient->getFullName();
			$patient->fullMrn = $bookingSheet->patient->getFullMrn();
		}

		$surgeons = [];
		foreach ($bookingSheet->getUsers() as $user) {
			$user->fullname = $user->getFullName();
			$surgeons[] = $user;
		}

		$assistants = [];
		foreach ($bookingSheet->assistant->find_all() as $user) {
			$user->fullname = $user->getFullName();
			$assistants[] = $user;
		}


		$additional_cpts = [];
		foreach ($bookingSheet->additional_cpts->find_all() as $item) {
			$item->fullname = $item->getFullName();
			$additional_cpts[] = $item;
		}

		$admitting_diagnosis = [];
		foreach ($bookingSheet->admitting_diagnosis->find_all() as $item) {
			$admitting_diagnosis[] = $item;
		}

		$secondary_diagnosis = [];
		foreach ($bookingSheet->secondary_diagnosis->find_all() as $item) {
			$secondary_diagnosis[] = $item;
		}

		$insurances = [];
		foreach ($bookingSheet->insurances->find_all() as $item) {
			$item->data = $item->getInsuranceDataModel();
			$item->title = $item->getTitle();
			$item->isRegularInsurance = $item->isRegularInsurance();
			$item->isAutoAccidentInsurance = $item->isAutoAccidentInsurance();
			$item->isWorkersCompanyInsurance = $item->isWorkersCompanyInsurance();
			$item->isDescriptionInsurance = $item->isDescriptionInsurance();
			$item->isInsuranceCompanyEqualsType = $item->isInsuranceCompanyEqualsType();
			$item->insuranceTypeTitle = $item->getInsuranceTypeTitle();
			$insurances[] = $item;
		}

		$view->booking = $bookingSheet;
		$view->patient = $patient;
		$view->surgeons = $surgeons;
		$view->assistants = $assistants;
		$view->additional_cpts = $additional_cpts;
		$view->admitting_diagnosis = $admitting_diagnosis;
		$view->secondary_diagnosis = $secondary_diagnosis;
		$view->pre_op_required_data = $bookingSheet->getPreOpRequiredData();
		$view->studies_ordered = $bookingSheet->getStudiesOrdered();
		$view->insurances = $insurances;

		return $view;
	}

}
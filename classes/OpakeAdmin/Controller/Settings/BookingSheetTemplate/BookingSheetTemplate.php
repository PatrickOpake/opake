<?php

namespace OpakeAdmin\Controller\Settings\BookingSheetTemplate;

class BookingSheetTemplate extends \OpakeAdmin\Controller\AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/settings/booking-sheet-templates/' . $this->org->id() => 'Booking Sheet']);
		$this->view->setActiveMenu('settings.templates.booking-sheet');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('booking_sheet_template', 'view');
		$this->view->subview = 'settings/booking-sheet-template/index';
	}

	public function actionCreate()
	{
		$this->checkAccess('booking_sheet_template', 'create');
		$this->view->id = null;
		$this->view->subview = 'settings/booking-sheet-template/view';
	}

	public function actionEdit()
	{
		$this->checkAccess('booking_sheet_template', 'edit');
		$this->view->id = $this->request->param('subid');
		$this->view->subview = 'settings/booking-sheet-template/view';
	}
}
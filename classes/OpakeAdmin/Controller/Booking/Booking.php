<?php

namespace OpakeAdmin\Controller\Booking;

class Booking extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/booking/' . $this->org->id => 'Booking Queue']);
		$this->view->setActiveMenu('schedule.booking');
		$this->view->set_template('inner');
		$this->view->showSideCalendar = true;
	}

	public function actionIndex()
	{
		$this->checkAccess('booking', 'index');
		$this->view->subview = 'booking/index';
	}

	public function actionView()
	{
		$booking = $this->orm->get('Booking', $this->request->param('subid'));

		$this->checkAccess('booking', 'edit', $booking);

		if (!$booking->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}

		$this->view->id = $booking->id;
		$this->view->addBreadCrumbs(['' => 'Booking Sheet']);
		$this->view->subview = 'booking/view';
	}

	public function actionCreate()
	{
		$this->checkAccess('booking', 'create');

		$this->view->addBreadCrumbs(['' => 'Create Booking Sheet']);
		$this->view->subview = 'booking/view';
		$this->view->id = null;
	}
}

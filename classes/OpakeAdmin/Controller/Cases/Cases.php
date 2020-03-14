<?php

namespace OpakeAdmin\Controller\Cases;

use Opake\Exception\BadRequest;
use Opake\Exception\Forbidden;

class Cases extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/cases/' . $this->org->id => 'Case Management']);
		$this->view->setActiveMenu('schedule.cases');
		$this->view->set_template('inner');
		$this->view->showSideCalendar = true;
	}

	public function actionIndex()
	{
		$this->iniCalendar();
		$this->view->subview = 'cases/index';
		$this->view->wrapContent = false;
		$this->view->showScheduleLegend = true;
	}

	public function actionCanceled()
	{
		$this->checkAccess('cancellation', 'index');
		$this->view->setActiveMenu('schedule.canceled-cases');
		$this->view->subview = 'cases/canceled_list';
	}

	public function actionCards()
	{
		$this->view->subview = 'cases/cards';
		$this->view->setActiveMenu('inventory.pref-cards');
	}

	public function actionClaim()
	{
		$service = $this->services->get('cases');
		$case = $service->getItem($this->request->param('subid'));

		if (!$case->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}

		$charge_masters = [];
		foreach ($case->coding->procedures->find_all() as $procedure) {
			if ($procedure->cpt_id) {
				$charge_master = $this->orm->get('Master_Charge')->where('cpt', $procedure->cpt_id)->find();
				if ($charge_master->loaded()) {
					$charge_masters[] = $charge_master;
				}
			}
		}

		$condition_codes = [];
		foreach ($case->coding->occurences->limit(11)->find_all() as $occurrence) {
			$condition_codes[] = $occurrence->cond_code->code;
		}

		$occurrence_codes = [];
		foreach ($case->coding->occurences->find_all() as $occurrence) {
			if ($occurrence->occ_code->code) {
				$occurrence_codes[] = ['code' => $occurrence->occ_code->code, 'date' => \Opake\Helper\TimeFormat::getDate($occurrence->occurence_date)];
			}
		}

		$icds = [];
		foreach ($case->coding->final_diagnosis->find_all() as $icd) {
			$icds[] = $icd;
		}

		$this->view->case = $case;
		$this->view->claim = $case->claim;
		$this->view->registration = $case->registration;
		$this->view->coding = $case->coding;
		$this->view->patient = $case->registration->patient;
		$this->view->charge_masters = $charge_masters;
		$this->view->condition_codes = $condition_codes;
		$this->view->occurrence_codes = $occurrence_codes;
		$this->view->icds = $icds;
		$this->view->addBreadCrumbs(['' => 'Claim']);
		$this->view->subview = 'cases/claim';
	}

	public function actionCm()
	{
		$this->view->addCss('/vendors/vis/vis.min.css');
		$this->view->addJS('/vendors/vis/vis.min.js');

		$this->view->setActiveMenu('schedule');
		$service = $this->services->get('cases');
		$case = $service->getItem($this->request->param('subid'));

		$this->view->cmActiveTab = null;
		if ($from = $this->request->get('from')) {
			if ($from === 'reschedule') {
				$this->view->cmActiveTab = 'intake';
			}
		}

		if (!$case->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}

		$this->checkAccess('case_management', 'view', $case);
		$this->checkAccess('cases', 'view', $case);

		if ($case->organization_id != $this->org->id()) {
			throw new Forbidden();
		}

		$this->view->case = $case;
		$this->view->caseObj = $case->toArray();
		$this->view->addBreadCrumbs(['' => 'Case View']);
		$this->iniDictation();
		$this->view->subview = 'cases/cm';
	}

	protected function iniCalendar()
	{
		//$this->view->addCss('/assets/vendors/fullcalendar/fullcalendar.min.css');
		$this->view->addJS('/vendors/fullcalendar/lib/jquery-ui.custom.min.js');
		$this->view->addJS('/vendors/fullcalendar/fullcalendar.js');
		$this->view->addJS('/js/calendar-extension.js');
		$this->view->addJS('/vendors/fullcalendar/gcal.js');
	}

	public function actionImportFromExcel()
	{
		$this->checkAccess('cases', 'import_from_excel');

		if ($this->request->method === "POST") {
			try {
				$this->importCasesFromExcel();
				$this->view->setMessage('Cases database has been imported');
			} catch (\Exception $e) {
				$this->logSystemError($e);
				$this->view->errors = [$e->getMessage()];
			}
		}

		$this->view->subview = 'cases/import_from_excel';
	}

	protected function importCasesFromExcel()
	{
		$files = $this->request->getFiles();
		$typeId = $this->request->post('type_id');
		$roomId = $this->request->post('location_id');

		if (empty($files['file_import'])) {
			throw new BadRequest('Empty file');
		}
		$file = $files['file_import'];
		if ($file->isEmpty()) {
			throw new BadRequest('Empty file');
		}
		if ($file->hasErrors()) {
			throw new \Exception('An error occurred while file loading');
		}

		$tmpFile = new \Opake\Helper\File\TemporaryFile($file);
		$tmpFile->create();

		$type = $this->pixie->orm->get('Cases_Type')->where(['organization_id', $this->org->id], ['id', $typeId])->find();
		if (!$type->loaded()) {
			throw new BadRequest('Unkonw procedure');
		}

		$location = $this->pixie->orm->get('Location')->where('id', $roomId)->find();
		if (!$location->loaded()) {
			throw new BadRequest('Unkonw room');
		}

		$importer = new \OpakeAdmin\Helper\Import\Cases($this->pixie);
		$importer->load($tmpFile->getFilePath(), $this->org->id, $type, $location);

		$tmpFile->cleanup();
	}

}

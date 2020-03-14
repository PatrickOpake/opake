<?php

namespace OpakeAdmin\Controller\Cases;

use Opake\Exception\BadRequest;
use Opake\Exception\InvalidMethod;
use Opake\Helper\TimeFormat;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Cases\OperativeReport as OpReport;
use OpakeAdmin\Helper\Export\CaseCancellationExport;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionSearch()
	{
		$colorType = $this->request->get('color_type', 'doctor');

		$service = $this->services->get('cases');
		$model = $service->getItem()->where('organization_id', $this->org->id);
			//->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED);

		$search = new \OpakeAdmin\Model\Search\Cases($this->pixie, false);
		$results = $search->search($model, $this->request);

		$this->result = [];
		foreach ($results as $result) {
			$this->result[] = $result->toCalendarArray($colorType);
		}
	}

	public function actionSearchInService()
	{
		$model = $this->orm->get('Cases_InService')->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Cases\InService($this->pixie);
		$results = $search->search($model, $this->request);

		$this->result = [];
		foreach ($results as $result) {
			$this->result[] = $result->toCalendarArray();
		}
	}

	public function actionDeleteInService()
	{
		$case = $this->loadModel('Cases_InService', 'subid');
		$case->delete();

		$this->result = 'ok';
	}

	public function actionGetAllSurgeons()
	{
		$service = $this->services->get('cases');
		$model = $service->getItem()->where('organization_id', $this->org->id)
			->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED);

		$search = new \OpakeAdmin\Model\Search\Cases($this->pixie, false);
		$results = $search->search($model, $this->request);

		$this->result = [];
		foreach ($results as $result) {
			$surgeon = $result->getFirstSurgeon();
			if ($surgeon) {
				$this->result[] = $surgeon->toScheduleLegendArray();
			}
		}
	}

	public function actionSearchCanceledCases()
	{
		$model = $this->orm->get('Cases_Cancellation');

		$search = new \OpakeAdmin\Model\Search\Cases\Cancellation($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionSearchMonthAlerts()
	{
		$service = $this->services->get('cases');
		$model = $service->getItem()->where('organization_id', $this->org->id)
			->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED);

		$search = new \OpakeAdmin\Model\Search\Cases($this->pixie, false);
		$results = $search->search($model, $this->request);

		$this->result = [];
		foreach ($results as $result) {
			$alerts = $result->getAlerts();
			if(!empty($alerts)) {
				$this->result[] = $result->getFormatter('DatePickerAlertsFormatter')->toArray();
			}
		}
	}

	public function actionExportCancellations()
	{
		$model = $this->orm->get('Cases_Cancellation');

		$search = new \OpakeAdmin\Model\Search\Cases\Cancellation($this->pixie, false);
		$cancellations = $search->search($model, $this->request);
		$export = new CaseCancellationExport($this->pixie);
		$csv = $export->generateCsv($cancellations);

		$this->result = [
			'success' => true,
			'url' => $csv->getWebPath()
		];
	}

	public function actionCase()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		if ($this->request->get('isOperativeReport')) {
			$this->result = $case->toOpReportArray();
		} else {
			$this->result = $case->toArray();
		}

	}

	public function actionHasTodayCaseForPatient()
	{
		$patientId = $this->request->get('patient_id');
		$timeStart = $this->request->get('time_start');

		$cases = $this->orm->get('Cases_Item');
		$caseQuery = $cases->query;
		$caseQuery->fields('case.*');

		$caseQuery->where('organization_id', $this->org->id)
			->join('case_registration', ['case_registration.case_id', 'case.id'], 'inner')
			->where('case_registration.patient_id', $patientId);

		$cases->where($this->pixie->db->expr('DATE(case.time_start)'), \Opake\Helper\TimeFormat::formatToDB($timeStart))
			->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED);

		if ($cases->count_all()) {
			$isCasesExists = true;
		} else {
			$isCasesExists = false;
		}

		$this->result = [
			'is_cases_exists' => $isCasesExists
		];
	}

	public function actionUpdateByCalendar()
	{
		$case = $this->loadModel('Cases_Item', 'subid');

		$form = new \OpakeAdmin\Form\Cases\ScheduleForm($this->pixie, $case);
		$form->load($this->getData(true));

		/** @var \Opake\ActivityLogger $logger */
		$logger = $this->pixie->activityLogger;
		$queue = $logger->newModelActionQueue($case);
		$queue->addAction(ActivityRecord::ACTION_EDIT_CASE);
		$queue->assign();

		if ($form->isValid()) {
			$form->save();

			$queue->registerActions();

			$this->result = [
				'success' => true
			];
		} else {
			$this->result = [
				'success' => false,
				'errors' => $form->getCommonErrorList()
			];
		}
	}

	public function actionSetting()
	{
		$setting = $this->orm->get('Cases_Setting')->where('organization_id', $this->org->id)->find();
		$data = $setting->toArray();

		$rooms = [];
		foreach ($this->org->getLocations() as $location) {
			$rooms[] = $location->getFormatter('CalendarSettings')->toArray();
		}
		$data['rooms'] = $rooms;

		$practiceGroups = [];
		$results = $this->org->practice_groups->where('active', 1)->order_by('name', 'asc')
			->find_all();
		foreach ($results as $group) {
			$practiceGroups[] = $group->toExpandedArray($this->org->id());
		}
		$data['practices'] = $practiceGroups;

		$this->result = $data;
	}

	public function actionExportReport()
	{
		$toDownload = true;

		if ($this->request->get('to_download') === 'false') {
			$toDownload = false;
		}

		$opReport = $this->loadModel('Cases_OperativeReport', 'subid');

		$document = new \OpakeAdmin\Helper\Printing\Document\Cases\OperativeReport($opReport);
		$document->runCompile();
		$pdf = $document->getContent();

		$filename = 'case_report_' . $opReport->id() . '.pdf';

		$this->response->file('application/pdf', $filename, $pdf, $toDownload);
		$this->execute = false;
	}

	public function actionExportOverview()
	{

		$service = $this->services->get('Cases');
		$viewState = $this->logged()->getViewState();
		$groupType = isset($viewState['dashboard_group']) ? $viewState['dashboard_group'] : 'surgeon';

		$groupedCases = [];

		$model = $service->getItem()
			->where('organization_id', $this->org->id)
			->where('stage', '!=', \Opake\Model\Cases\Item::STAGE_BILLING)
			->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED);

		$search = new \OpakeAdmin\Model\Search\Cases($this->pixie, false);
		$results = $search->search($model, $this->request);

		if($groupType == 'room') {
			foreach ($results as $result) {
				$groupedCases[$result->location_id]['cases'][] = $result;
				$groupedCases[$result->location_id]['room'] = $result->location;
				$groupedCases[$result->location_id]['header'] = $result->location->name;
				$groupedCases[$result->location_id]['position'] = $result->location->display_settings->overview_position;
			}
			foreach ($this->getInServices() as $service) {
				$groupedCases[$service->location_id]['cases'][] = $service;
				$groupedCases[$service->location_id]['room'] = $service->location;
				$groupedCases[$service->location_id]['header'] = $service->location->name;
				$groupedCases[$service->location_id]['position'] = $service->location->display_settings->overview_position;
			}
		}

		if($groupType == 'surgeon') {
			foreach ($results as $result) {
				foreach ($result->users->find_all() as $user) {
					if(empty($this->request->get('doctor')) ||  $this->request->get('doctor') == $user->id()) {
						$groupedCases[$user->id]['cases'][$result->id] = $result;
						$groupedCases[$user->id]['surgeon'] = $user;
						$groupedCases[$user->id]['header'] = $user->getFullName();
						$groupedCases[$user->id]['position'] = $user->display_settings->overview_position;
					}
				}
				foreach ($result->other_staff->find_all() as $user) {
					if(empty($this->request->get('doctor')) ||  $this->request->get('doctor') == $user->id()) {
						$groupedCases[$user->id]['cases'][$result->id] = $result;
						$groupedCases[$user->id]['surgeon'] = $user;
						$groupedCases[$user->id]['header'] = $user->getFullName();
						$groupedCases[$user->id]['position'] = $user->display_settings->overview_position;
					}
				}
				foreach ($result->assistant->find_all() as $user) {
					if(empty($this->request->get('doctor')) ||  $this->request->get('doctor') == $user->id()) {
						$groupedCases[$user->id]['cases'][$result->id] = $result;
						$groupedCases[$user->id]['surgeon'] = $user;
						$groupedCases[$user->id]['header'] = $user->getFullName();
						$groupedCases[$user->id]['position'] = $user->display_settings->overview_position;
					}
				}
			}

			foreach ($this->getInServices() as $service) {
				$groupedCases['in_service']['cases'][] = $service;
				$groupedCases['in_service']['header'] = 'In Service';
				$groupedCases['in_service']['position'] = 0;
			}
		}

		$displayTimestamp = $this->orm->get('Cases_Setting')->where('organization_id', $this->org->id)->find()->display_timestamp_on_printout;

		$view = $this->pixie->view('cases/export/overview');
		$view->request = $this->request->get();
		$view->display_timestamp = (bool) $displayTimestamp;
		$view->org = $this->org;
		$view->groupType = $groupType;
		$view->groupedCases = $groupedCases;


		$this->response->body = $view->render();
		$this->execute = false;
	}

	protected function getInServices()
	{
		$model = $this->orm->get('Cases_InService')->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Cases\InService($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $result) {
			$items[] = $result;
		}

		return $items;
	}

	public function actionExportCase()
	{
		$toDownload = $printPatientDetails = $printInsurances = true;

		if ($this->request->get('to_download') === 'false') {
			$toDownload = false;
		}

		if ($this->request->get('print_part') === 'patient_details') {
			$printInsurances = false;
		} else if ($this->request->get('print_part') === 'insurances') {
			$printPatientDetails = false;
		}

		$case = $this->loadModel('Cases_Item', 'subid');

		$validationData = json_decode(json_encode($case->registration->toArray()));
		$patientsService = $this->services->get('patients');
		$validationErrors = $patientsService->validate('Cases_Registration', 'Cases_Registration_Insurance', $validationData);

		$filename = 'case_info_' . $case->id . '.pdf';
		$view = $this->pixie->view('cases/export/case');
		$view->case = $case;
		$view->registration = $case->registration;
		$view->organization = $this->org;
		$view->printPatientDetails = $printPatientDetails;
		$view->printInsurances = $printInsurances;
		$view->validationErrors = $validationErrors;

		list($pdf, $errors) = \Opake\Helper\Export::pdf($view->render());
		if ($errors) {
			throw new \Opake\Exception\Ajax('PDF generation failed: ' . $errors);
		} else {
			$this->response->file('application/pdf', $filename, $pdf, $toDownload);
			$this->execute = false;
		}
	}

	public function actionCompileCaseWithForms()
	{
		try {

			$case = $this->loadModel('Cases_Item', 'subid');

			$documentsToPrint = [];
			$documentsToPrint[] = new \OpakeAdmin\Helper\Printing\Document\Cases\FacesheetDocument($case);
			foreach ($case->registration->documents->find_all() as $document) {
				$documentsToPrint[] = new \OpakeAdmin\Helper\Printing\Document\Cases\AdditionalChart\ChartFile($document);
			}

			$operativeReport = $this->pixie->orm->get('Cases_OperativeReport')
				->where('case_id', $case->id())
				->find();

			if ($operativeReport->loaded()) {
				$documentsToPrint[] = new \OpakeAdmin\Helper\Printing\Document\Cases\OperativeReport($case->getOpReport());
			}

			$helper = new \OpakeAdmin\Helper\Printing\PrintCompiler();
			$printResult = $helper->compile($documentsToPrint);

			/** @var \Opake\Model\UploadedFile $file */
			$file = $printResult->file;

			$file->original_filename = 'case-' . $case->id() . '.pdf';
			$file->save();

			$this->result = [
				'success' => true,
				'url' => $file->getWebPath()
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	public function actionExportCard()
	{
		$type = $this->request->get('type');
		$case = $this->loadModel('Cases_Item', 'subid');

		if ($type === 'staff') {
			$view = $this->pixie->view('cases/export/card');
			$cards = $case->getStaffCards();

			$notes = [];
			$itemGroups = [];

			foreach ($cards as $card) {
				$notes = array_merge($notes, $card->notes->find_all()->as_array());

				foreach ($card->items->with('inventory')->find_all() as $item) {
					if (!isset($itemGroups[$item->inventory->type])) {
						$itemGroups[$item->inventory->type] = [];
					}
					$itemGroups[$item->inventory->type][] = $item;
				}
			}

			$view->title = 'Operation';
			$view->notes = $notes;
			$view->item_groups = $itemGroups;
		} elseif ($type === 'location') {
			$view = $this->pixie->view('cases/export/card');
			$cards = $case->getLocationCards();
			$subtype = $this->request->get('subtype');
			if (isset($cards[$subtype])) {
				$card = $cards[$subtype];
				$view->title = $subtype ? 'Post-OP' : 'Pre-OP';
				$view->notes = $card->notes->find_all()->as_array();
				$view->items = $card->items->with('inventory')->find_all()->as_array();
			} else {
				throw new \Opake\Exception\Ajax('Wrong subtype');
			}
		} else {
			throw new \Opake\Exception\Ajax('Unknown type');
		}

		$filename = 'card_' . $type . '_' . $case->id . '.pdf';

		list($pdf, $errors) = \Opake\Helper\Export::pdf($view->render());

		$this->response->file('application/pdf', $filename, $pdf);
		$this->execute = false;
	}

	public function actionPrintClaim()
	{

		if ($this->request->param('subid')) {
			$case = $this->loadModel('Cases_Item', 'subid');
		} elseif ($this->request->post('ids')) {
			$ids = $this->request->post('ids');
			foreach ($ids as $id) {
				$case = $this->orm->get('Cases_Item', $id);
			}
		}

		return;
		//$doc = \ZendPdf\PdfDocument::load($this->pixie->root_dir . '/docs/UB04_CMS-1450.pdf');
		//var_dump($doc->pages[0]->getPageDictionary()->Resources->ProcSet);
		//$doc->save($this->pixie->root_dir . '/_tmp/111.pdf');
		//die;
		$template = $this->pixie->root_dir . '/_tmp/111.pdf';
		copy($this->pixie->root_dir . '/docs/UB04_CMS-1450.pdf', $template);
		$pdf = new \PDFlib();
		//var_dump($pdf); die;
		$doc = $pdf->open_pdi_document($template, "");
		$page = $pdf->open_pdi_page($doc, 1, '');
		//PDF_set_value($pdf, 'TextField1[0]', '1');
		$pdf->rotate(-90);
		$pdf->end_page_ext("1");
		$pdf->end_document('');

		$buf = $pdf->get_buffer();
		$len = strlen($buf);

		header("Content-type: application/pdf");
		header("Content-Length: $len");
		header("Content-Disposition: inline; filename=hello.pdf");
		print $buf;

		/*if (file_exists($template)) {
			$this->response->file('application/pdf', $template, file_get_contents($template));
			$this->execute = false;
		} else {
			throw new \Opake\Exception\FileNotFound();
		}*/
	}

	public function actionStartCase()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$case->start();

		$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_CLINICAL_START_CASE)
			->setModel($case)
			->register();

		$this->result = 'ok';
	}

	public function actionEndCase()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$case->end();

		$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_CLINICAL_END_CASE)
			->setModel($case)
			->register();

		$this->result = 'ok';
	}

	public function actionUpdateState()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$data = (array)$this->getData();
		$oldState = $case->getState();
		if (!$oldState || !$oldState['operation']) {
			$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_CLINICAL_CONFIRM_AUDIT)
				->setModel($case)
				->register();
		}

		$case->updateState($data);

		$this->result = [
			'stage' => $case->stage,
			'phase' => $case->phase
		];
	}

	public function actionDelete()
	{
		$this->checkAccess('cases', 'delete');
		$case = $this->loadModel('Cases_Item', 'subid');

		$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_DELETE_CASE)
			->setModel($case)
			->register();

		$case->delete();

		$this->result = 'ok';
	}

	public function actionPhases()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$this->result = $case->getPhases();
	}

	public function actionReport()
	{
		$service = $this->services->get('cases_operativeReports');
		$service_future = $this->services->get('cases_operativeReports_Future');
		/** @var OpReport $opReport */
		$opReport = $this->loadModel('Cases_OperativeReport', 'subid');
		if (!$opReport->loaded()) {
			throw new \Opake\Exception\Ajax('Operative Report doesn\'t exist');
		}
		$case = $opReport->getCase();
		$this->result = [
			'report' => $opReport->toArray(),
			'template' => $service->getFieldsTemplate($this->org->id, $opReport->id()),
			'site_template' => $service->getTemplate($this->org->id),
			'future_reports' => $service_future->findFutureReports($case, $opReport->surgeon_id),
		];
	}

	public function actionChangeAppointmentStatus()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$newStatus = $this->request->post('newStatus');
		$isRemainedInBilling = (bool)$this->request->post('isRemainedInBilling');

		try {
			if ($newStatus == \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED) {
				$this->cancelCase($case);
			} else if (($newStatus == \Opake\Model\Cases\Item::APPOINTMENT_STATUS_COMPLETED) 
				|| ($newStatus == \Opake\Model\Cases\Item::APPOINTMENT_STATUS_NEW)) {
				$this->checkInCase($case);
			}
			$case->appointment_status = $newStatus;
			$case->is_remained_in_billing = $isRemainedInBilling;
			$case->save();
		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$this->result = 'ok';
	}

	protected function cancelCase($case)
	{
		$data = $this->getData();
		if ($data) {
			if (isset($data->id)) {
				$caseCancellation = $this->orm->get('Cases_Cancellation', $data->id);
			} else {
				$caseCancellation = $this->orm->get('Cases_Cancellation');
				$caseCancellation->cancel_time = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB);
				$caseCancellation->canceled_user_id = $this->logged()->id;
				$caseCancellation->case_id = $case->id;
				$caseCancellation->dos = $case->time_start;
			}
			$caseCancellation->fill($data);
			$caseCancellation->save();

			if (isset($data->cancel_status) && ($data->cancel_status == \Opake\Model\Cases\Cancellation::CANCELLED_STATUS_NO_SHOW)) {
				$caseCancellation->cancel_attempts->delete_all();
				if (!empty($data->cancel_attempts)) {
					foreach ($data->cancel_attempts as $attemptData) {
						$attemptModel = $this->orm->get('Cases_CancelAttempt', isset($attemptData->id) ? $attemptData->id : null);
						$attemptData->case_cancellation_id = $caseCancellation->id;
						$this->updateModel($attemptModel, $attemptData);
					}
				}
			}
		}

		$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_CANCEL_CASE)
			->setModel($case)
			->register();
	}

	protected function checkInCase($case)
	{
		$data = $this->getData();
		if ($data) {
			$case->fill($data);
		}
			$case->time_check_in = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB);

		$action = $this->pixie->activityLogger->newAction(ActivityRecord::ACTION_CASE_CHECK_IN);
		$action->setModel($case);
		$action->register();
	}

	public function actionIsAllTabsComplete()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$registration = $case->registration;

		if ($registration->isAllSectionsValid()) {
			$this->result = true;
		} else {
			$this->result = false;
		}
	}

	public function actionLogPrintSchedule()
	{
		$data = $this->getData();
		$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_PRINT_SCHEDULE)
			->setAdditionalInfo('viewParams', $data)
			->register();

		$this->result = 'ok';
	}

	public function actionCards()
	{
		$service = $this->services->get('Cases');

		$model = $service->getItem()
			->where('organization_id', $this->org->id())
			->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED);

		$search = new \OpakeAdmin\Model\Search\Cases($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->getFormatter('CardListFormatter')->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionCard()
	{
		$card = [];
		$templates = [];
		$case = $this->loadModel('Cases_Item', 'subid');
		$cardModel = $case->getCard();
		if ($cardModel->loaded()) {
			$card = $cardModel->toArray();
		}
		$prefCardStaff = $this->orm->get('PrefCard_Staff');
		foreach ($prefCardStaff->getByOrganization($this->org->id)->find_all() as $prefCard) {
			$templates[] = $prefCard->toArray();
		}
		$this->result = [
			'card' => $card,
			'templates' => $templates,
		];
	}

	public function actionAddInventoryItem()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$data = $this->getData();

		if ($data) {
			$model = $this->orm->get('Cases_InventoryItem');
			$model->case_id = $case->id;

			$this->updateModel($model, $data);
			$this->result = (int)$model->id;
		}
	}

	public function actionRemoveInventoryItem()
	{
		$item = $this->loadModel('Cases_InventoryItem', 'subid');
		$item->delete();
	}

	public function actionTimeLog()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$result = [];

		foreach ($case->time_logs->find_all()->as_array() as $log) {
			$result[] = $log->toArray();
		}

		$this->result = $result;
	}

	public function actionChartsList()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$caseCharts = []; ;
		foreach ($case->getCharts()->find_all() as $chart) {
			$caseCharts[] = $chart->toArray();
		}

		$this->result = [
			'charts' => $caseCharts
		];
	}

	public function actionFinancialDocList()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$caseFinancialDocs = []; ;
		foreach ($case->getFinancialDocuments()->find_all() as $doc) {
			$caseFinancialDocs[] = $doc->toArray();
		}

		$this->result = [
			'charts' => $caseFinancialDocs
		];
	}

	public function actionUploadDoc()
	{
		try {
			$case = $this->loadModel('Cases_Item', 'subid');

			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}
			$docType = $this->request->post('doc_type');
			$docId = $this->request->post('id');
			$updateDocId = $this->request->post('doc_id');

			if ($docId && !$updateDocId) {
				if($docType === 'financial_document') {
					$doc = $this->orm->get('Cases_FinancialDocument', $docId);
				} else {
					$doc = $this->orm->get('Cases_Chart', $docId);
				}
				$doc->name = $this->request->post('doc_name');
				$doc->save();
			} else {
				/** @var \Opake\Request $req */
				$req = $this->request;

				$files = $req->getFiles();
				if (empty($files['file'])) {
					throw new BadRequest('Empty file');
				}

				$upload = $files['file'];
				if (!$upload->isEmpty() && !$upload->hasErrors()) {
					$fileProtectedType = 'cases_chart';
					if($docType == 'financial_document') {
						$fileProtectedType = 'cases_financial_document';
					}
					/** @var \Opake\Model\UploadedFile $uploadedFile */
					$uploadedFile = $this->pixie->orm->get('UploadedFile');
					$uploadedFile->storeFile($upload, [
						'is_protected' => true,
						'protected_type' => $fileProtectedType
					]);
					$uploadedFile->save();

					$docId = $this->request->post('doc_id');
					if($docType === 'financial_document') {
						if ($docId) {
							$doc = $this->orm->get('Cases_FinancialDocument', $docId);
						} else {
							$doc = $this->orm->get('Cases_FinancialDocument');
							$doc->list_id = $case->getCaseBookingListId();
						}
					} else {
						if ($docId) {
							$doc = $this->orm->get('Cases_Chart', $docId);
						} else {
							$doc = $this->orm->get('Cases_Chart');
							$doc->list_id = $case->getCaseBookingListId();
						}
					}

					$doc->name = $this->request->post('doc_name') ? $this->request->post('doc_name') : $this->request->post('name');
					$doc->uploaded_file_id = $uploadedFile->id;
					$doc->uploaded_date = strftime(TimeFormat::DATE_FORMAT_DB, time());
					$doc->save();
				}
			}

			$this->result = 'ok';

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	public function actionRemoveDoc()
	{
		$docType = $this->request->post('doc_type');
		if($docType === 'financial_document') {
			$doc = $this->loadModel('Cases_FinancialDocument', 'subid');
		} else {
			$doc = $this->loadModel('Cases_Chart', 'subid');
		}
		if ($doc->loaded()) {
			$doc->delete();
			$this->result = 'ok';
		}
	}

	public function actionPointContactSMS()
	{
		try {
			$case = $this->loadModel('Cases_Item', 'subid');
			$notifier = new \OpakeAdmin\Helper\SMS\CaseNotifier($this->org->sms_template);
			$success = $notifier->notifyPointContact($case);

			$action = $this->pixie->activityLogger->newAction(ActivityRecord::ACTION_SEND_POINT_OF_CONTACT_SMS);
			$action->setModel($case);
			$action->register();

			$this->result = [
				'success' => $success
			];
		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	public function actionCardInventories()
	{
		$result = [];
		$IDs = $this->request->post('IDs');
		$caseId = (int)$this->request->param('subid');
		if($IDs) {
			$model = $this->orm->get('Inventory')
				->where('id', 'IN', $this->pixie->db->arr($IDs));

			$model->query
				->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'), $this->pixie->db->expr('card_staff_item.actual_use as actual_use'))
				->join('card_staff_item', ['card_staff_item.inventory_id',  $model->table. '.id'])
				->join('card_staff', ['card_staff.id', 'card_staff_item.card_id'])
				->where('card_staff.case_id', $caseId)
				->group_by('inventory.id');
			foreach ($model->find_all() as $inventory) {
				$IDs = array_diff($IDs, [$inventory->id()]);
				$result[] = $inventory->getFormatter('InventoryDynamicFieldsFormatter')->toArray();
			}

			if($IDs) {
				$inventories = $this->orm->get('Inventory')
					->where('id', 'IN', $this->pixie->db->arr($IDs));

				foreach ($inventories->find_all() as $inventory) {
					$result[] = $inventory->getFormatter('InventoryDynamicFieldsFormatter')->toArray();
				}
			}
		}

		$this->result = $result;
	}

}

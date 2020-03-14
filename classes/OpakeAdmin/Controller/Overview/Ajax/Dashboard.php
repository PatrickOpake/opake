<?php

namespace OpakeAdmin\Controller\Overview\Ajax;

use Opake\Exception\BadRequest;
use Opake\Exception\PageNotFound;
use Opake\Model\Cases\Item;
use Opake\Model\Cases\OperativeReport;
use Opake\Model\Profession;
use Opake\Service\Cases\Cases;
use OpakeAdmin\Helper\Printing\PrintCompiler;

class Dashboard extends \OpakeAdmin\Controller\Ajax {

	public function before() {
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$service = $this->services->get('Cases');

		$cases = [];
		$casesIds = [];
		$relations = [];

		$model = $service->getItem()
			->where('organization_id', $this->org->id)
			->where('stage', '!=', Item::STAGE_BILLING)
			->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED);

		$search = new \OpakeAdmin\Model\Search\Cases($this->pixie, false);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$casesIds[] = $result->id;
			$cases[] = $result->toShortArray();
		}

		if (count($casesIds)) {
			$relations = $service->getUsersRelations($casesIds);
		}

		$this->result = [
			'cases' => $cases,
			'relations' => $relations,
			'case_ids' => $casesIds,
			'in_services' => $this->getInServices()
		];
	}

	protected function getInServices()
	{
		$model = $this->orm->get('Cases_InService')->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Cases\InService($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->toDashboardArray();
		}

		return $items;
	}

	public function actionInitData()
	{
		$surgeons = [];
		if ($this->logged()->isDoctor()) {
			$loggedUser = $this->logged();
			$loggedUserPracticeGroupIds = $loggedUser->getPracticeGroupIds();
			$users = $this->orm->get('User');

			$users->query->where('user.organization_id', $this->org->id());
			if (count($loggedUserPracticeGroupIds)) {
				$users->query->join(['user_practice_groups', 'upg'], ['user.id', 'upg.user_id'], 'left');
				$users->query->where('upg.practice_group_id', 'IN', $this->pixie->db->arr($loggedUserPracticeGroupIds));
			}
			$users->query->group_by('user.id');
			$users->query->order_by('user.last_name', 'ASC');
		} else {
			$users = $this->orm->get('User')
				->where('organization_id', $this->org->id)
				->order_by('last_name', 'ASC');

			if ($this->request->get('doctor')) {
				$users->where('id', $this->request->get('doctor'));
			}
		}

		foreach ($users->find_all() as $surgeon) {
			$surgeons[] = $surgeon->toDashboardArray();
		}

		$rooms = [];
		foreach ($this->org->getLocations() as $location) {
			$rooms[] = $location->getFormatter('OverviewSettings')->toArray();
		}

		$displayTimestamp = $this->orm->get('Cases_Setting')->where('organization_id', $this->org->id)->find()->display_timestamp_on_printout;
		$smsTemplate = $this->orm->get('SmsTemplate')->where('organization_id', $this->org->id)->find();

		$this->result = [
			'rooms' => $rooms,
			'surgeons' => $surgeons,
			'display_timestamp' => (bool) $displayTimestamp,
			'display_point_of_contact' => (bool) $smsTemplate->poc_sms,
			'point_of_contact_msg' => $smsTemplate->poc_msg
		];
	}

	public function actionUpdateDisplaySettings()
	{
		$data = $this->getData();

		foreach ($data->surgeons as $surgeon) {
			if ($surgeon->overview_display_position) {
				$user = $this->orm->get('User', $surgeon->id);
				$user->updateOverviewPosition($surgeon->overview_display_position);
			}
		}

		foreach ($data->rooms as $room) {
			if ($room->overview_display_position) {
				$location = $this->orm->get('Location', $room->id);
				$location->updateOverviewPosition($room->overview_display_position);
			}
		}

		$this->updateDisplayTimestampSetting($data->display_timestamp);
	}

	protected function updateDisplayTimestampSetting($displayTimestamp) {
		$this->pixie->db->query('update')
			->table('case_setting')
			->data(['display_timestamp_on_printout' => $displayTimestamp])
			->where('organization_id', $this->org->id)
			->execute();
	}

	public function actionChartGroupOptions()
	{
		$caseId = $this->request->get('case');

		if (!$caseId) {
			throw new BadRequest('Case is required param');
		}

		$chartGroups = $this->orm->get('Forms_ChartGroup')
			->where('organization_id', $this->org->id())
			->order_by('name', 'ASC')
			->find_all();

		$chartGroupsResults = [];
		foreach ($chartGroups as $chartGroup) {
			$chartGroupsResults[] = $chartGroup->toArray();
		}

		$forms = $this->orm->get('Forms_Document')
			->where('organization_id', $this->org->id())
			->order_by('name', 'ASC')
			->find_all();

		$formsResults = [];

		$case = $this->orm->get('Cases_Item', $caseId);
		if (!$case->loaded()) {
			throw new PageNotFound();
		}

		$hasAccessToBilling = $this->pixie->permissions->checkAccess('billing', 'view_forms');
		foreach ($forms as $form) {
			if ($form->isAllowedCase($case) && ($form->segment !== \Opake\Model\Forms\Document::SEGMENT_BILLING || $hasAccessToBilling)) {
				$formsResults[] = [
					'id' => (int) $form->id(),
					'name' => $form->name
				];
			}
		}

		$model = $this->pixie->orm->get('PrefCard_Staff');
		$model->query->fields('pref_card_staff.*');
		$model->query->join('user', ['user.id', 'pref_card_staff.user_id']);
		$model->where('user.organization_id', $this->org->id);

		$prefCards = [];
		foreach ($model->find_all() as $prefCardModel) {
			$prefCards[] = $prefCardModel->getFormatter('DashboardPrint')->toArray();
		}

		$this->result = [
			'success' => true,
			'chart_groups' => $chartGroupsResults,
			'charts' => $formsResults,
		    'pref_cards' => $prefCards
		];
	}

	public function actionCompileChartGroupsPrint()
	{
		$registrationId = $this->request->param('subid');
		$charts = $this->request->post('charts');
		$chartGroup = $this->request->post('chart_group');
		$prefCards= $this->request->post('pref_cards');

		if (!$registrationId && (!$charts || !$chartGroup || !$prefCards)) {
			throw new BadRequest('Bad request');
		}

		$registration = $this->orm->get('Cases_Registration', $registrationId);
		$case = $registration->case;

		try {


			$usedFormIds = [];
			$documentsToPrint = [];

			if ($chartGroup) {
				$model = $this->orm->get('Forms_ChartGroup')
					->where('organization_id', $this->org->id())
					->where('id', $chartGroup)
					->find();

				foreach ($model->getDocuments() as $document) {
					$documentsToPrint[] = \OpakeAdmin\Helper\Printing\Document\Cases\Chart::createDocument($document, $case);
					$usedFormIds[] = $document->id();
				}
			}

			if ($charts) {
				$chartIds = [];
				foreach ($charts as $id) {
					if (!in_array($id, $usedFormIds)) {
						$chartIds[] = $id;
					}
				}

				if ($chartIds) {
					$model = $this->orm->get('Forms_Document')
						->where('organization_id', $this->org->id())
						->where('id', 'IN', $this->pixie->db->expr("(" . implode(',', $chartIds) . ")"));

					foreach ($model->find_all() as $document) {
						$documentsToPrint[] = \OpakeAdmin\Helper\Printing\Document\Cases\Chart::createDocument($document, $case);
					}
				}

			}

			if ($prefCards) {

				$model = $this->orm->get('PrefCard_Staff')
					->where('id', 'IN', $this->pixie->db->expr("(" . implode(',', $prefCards) . ")"));

				foreach ($model->find_all() as $prefCard) {
					$documentsToPrint[] = new \OpakeAdmin\Helper\Printing\Document\PrefCard\StaffPrefCard($prefCard, $case, $this->logged());
				}
			}

			$helper = new PrintCompiler();
			$printResult = $helper->compile($documentsToPrint);

			$this->result = [
				'success' => true,
				'url' => $printResult->getResultUrl(),
				'print' => $printResult->isReadyToPrint()
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	public function actionCompilePatientLabels()
	{
		$registrationId = $this->request->param('subid');
		if (!$registrationId) {
			throw new BadRequest('Bad Request');
		}

		$registration = $this->pixie->orm->get('Cases_Registration', $registrationId);
		if (!$registration->loaded()) {
			throw new PageNotFound('Unknown registration');
		}
		$case = $registration->case;

		try {

			$helper = new \OpakeAdmin\Helper\Printing\PrintCompiler();
			$printResult = $helper->compile([
				new \OpakeAdmin\Helper\Printing\Document\Cases\PatientLabels($case)
			]);

			$this->result = [
				'success' => true,
				'url' => $printResult->getResultUrl(),
				'print' => $printResult->isReadyToPrint()
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}
}

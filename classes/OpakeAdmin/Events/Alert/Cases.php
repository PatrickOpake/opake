<?php

namespace OpakeAdmin\Events\Alert;

use Opake\Model\Alert\Alert as OpakeAlert;
use Opake\Helper\TimeFormat;
use Opake\Model\Cases\Alert;

class Cases extends \Opake\Events\AbstractListener
{

	protected $_case;

	/**
	 * Cоздаёт алерты при создании или редактировании кейса
	 *
	 * @param \Opake\Model\Cases\Item $case
	 * @return array Список алертов
	 */
	public function dispatch($case)
	{

		if (!$case->loaded()) {
			return false;
		}
		$this->_case = $case;

		$this->checkIsReportCompleted();
		$this->checkAlerts();


		$alerts = $this->getAlerts();

		$time_start = strtotime($case->time_start);
		$time_end = strtotime($case->time_end);
		// До начала кейса
		if (time() < $time_start) {

			// За 2 дня до
			if (strtotime('+2 day') < $time_start) {
				//$this->checkCards($alerts, false);
				$this->updateStatus(\Opake\Model\Cases\Item::STATUS_BEFORE);
				// 2-0 дня до
			} else {
				//$this->checkCards($alerts);
				$this->updateStatus(\Opake\Model\Cases\Item::STATUS_PRIOR);
			}
			//$this->removeAlerts($alerts);

			// Во время кейса
		} elseif (time() < $time_end) {
			//$this->checkCards($alerts);
			//$this->removeAlerts($alerts);
			$this->updateStatus(\Opake\Model\Cases\Item::STATUS_DURING);
			// После кейса
		} else {
			$this->makeCaseAlert($alerts, OpakeAlert::TYPE_CASE_REVIEW);
//			if (!$case->report->loaded()) {
//				$this->makeCaseAlert($alerts, OpakeAlert::TYPE_CASE_REPORT);
//			}
			$this->updateStatus(\Opake\Model\Cases\Item::STATUS_AFTER);
		}
	}

	protected function checkCards(&$alerts, $picked_up_check = true)
	{
		$users = $this->getUsers();
		$cards = $this->_case->getStaffCards();

		$card_alerts = &$alerts[OpakeAlert::TYPE_PREFERENCE_CARD];

		foreach ($users as $user) {
			$empty_card = !isset($cards[$user->id]) || !$cards[$user->id]->items->count_all();

			if ($empty_card) {
				if (isset($card_alerts[$user->id])) {
					$alert = $card_alerts[$user->id];
					unset($card_alerts[$user->id]);
				} else {
					$alert = $this->orm->get('alert_alert');
					$alert->type = OpakeAlert::TYPE_PREFERENCE_CARD;
					$alert->object_id = $user->id;
					$this->fill($alert);
				}
				$alert->subtitle = $alert->statuses[OpakeAlert::TYPE_PREFERENCE_CARD];
				$alert->setObject([
					'date' => $this->_case->time_start,
					'enddate' => $this->_case->time_end,
					'locationid' => (int)$this->_case->location->id,
					'locationname' => $this->_case->location->name,
					'doctor' => $user->getFullName(),
					'caseid' => (int)$this->_case->id,
					'userid' => (int)$user->id,
					'provider' => $this->_case->getProvider(),
					'patient' => $this->_case->registration->getFullName()
				]);
				$alert->save();
			}
		}

		if ($picked_up_check) {
			foreach ($cards as $card) {
				foreach ($card->items->find_all() as $item) {
					if (!isset($item->status) || $item->status != \Opake\Model\Card\AbstractItem::STATUS_MOVED) {
						if (isset($alerts[OpakeAlert::TYPE_CASE_READY_TO_PICKED])) {
							$alert = $alerts[OpakeAlert::TYPE_CASE_READY_TO_PICKED];
							unset($alerts[OpakeAlert::TYPE_CASE_READY_TO_PICKED]);
						} else {
							$alert = $this->orm->get('alert_alert');
							$alert->type = OpakeAlert::TYPE_CASE_READY_TO_PICKED;
							$this->fill($alert);
						}
						$alert->object_id = (int)$this->_case->id;
						$alert->subtitle = $alert->statuses[OpakeAlert::TYPE_CASE_READY_TO_PICKED];
						$alert->setObject([
							'date' => $this->_case->time_start,
							'enddate' => $this->_case->time_end,
							'locationid' => (int)$this->_case->location->id,
							'locationname' => $this->_case->location->name,
							'caseid' => (int)$this->_case->id,
							'provider' => $this->_case->getProvider(),
							'patient' => $this->_case->registration->getFullName()
						]);
						$alert->save();
						break 2;
					}
				}
			}
		}
	}

	protected function makeCaseAlert(&$alerts, $type)
	{
		if (isset($alerts[$type])) {
			$alert = $alerts[$type];
			unset($alerts[$type]);
		} else {
			$alert = $this->orm->get('alert_alert');
			$alert->type = $type;
			$this->fill($alert);
		}
		$alert->object_id = (int)$this->_case->id;
		$alert->subtitle = $alert->statuses[$type];
		$alert->setObject([
			'date' => $this->_case->time_start,
			'enddate' => $this->_case->time_end,
			'locationid' => (int)$this->_case->location->id,
			'locationname' => $this->_case->location->name,
			'caseid' => (int)$this->_case->id,
			'provider' => $this->_case->getProvider(),
			'patient' => $this->_case->registration->getFullName()
		]);
		$alert->save();
	}

	protected function fill($alert)
	{
		$alert->date = strftime(TimeFormat::DATE_FORMAT_DB);
		$alert->organization_id = (int)$this->_case->organization_id;
		$alert->title = $this->_case->type->name;
		$alert->case_id = $this->_case->id;
	}

	protected function getUsers()
	{
		$result = [];
		foreach ($this->_case->users->find_all() as $user) {
			$result[$user->id] = $user;
		}
		return $result;
	}

	protected function getAlerts()
	{
		$result = [];
		$alerts = $this->orm->get('alert_alert')
			->where('case_id', $this->_case->id)
			->find_all();

		$result[OpakeAlert::TYPE_PREFERENCE_CARD] = [];
		foreach ($alerts as $alert) {
			if ($alert->type == OpakeAlert::TYPE_PREFERENCE_CARD) {
				$result[OpakeAlert::TYPE_PREFERENCE_CARD][$alert->object_id] = $alert;
			} else {
				$result[$alert->type] = $alert;
			}
		}
		return $result;
	}

	protected function checkAlerts()
	{
		$this->pixie->logger->notice("checkAlerts");
		foreach (Alert::$alerts as $codeAlert) {
			if($this->callCheckMethod($codeAlert)) {
				$registrationAlert = $this->orm->get('Cases_Alert')->where([
					['case_id', $this->_case->id()],
					['code', $codeAlert],
				])->find();
				if(!$registrationAlert->loaded()) {
					$registrationAlert = $this->orm->get('Cases_Alert');
					$registrationAlert->case_id = $this->_case->id();
					$registrationAlert->code = $codeAlert;
					$registrationAlert->type = Alert::$alertTypes[$codeAlert];
					$registrationAlert->save();
				}
			} else {
				$registrationAlert = $this->orm->get('Cases_Alert')->where([
					['case_id', $this->_case->id()],
					['code', $codeAlert],
				])->find();

				if($registrationAlert->loaded()) {
					$registrationAlert->delete();
				}
			}
		}
	}

	protected function callCheckMethod($type)
	{
		$methodName = Alert::$alertCheckingHandler[$type];
		$methodName = 'check' . ucfirst($methodName);

		if (!method_exists($this, $methodName)) {
			throw new \Exception('Unknown method "' . $methodName . '" for alert type "' . $type . '"');
		}

		return call_user_func([$this, $methodName]);
	}

	protected function removeAlerts($alerts)
	{
		foreach ($alerts as $alert) {  // Удаляем устаревшие алерты
			if (is_array($alert)) {
				foreach ($alert as $a) {
					$a->delete();
				}
			} else {
				$alert->delete();
			}
		}
	}

	protected function updateStatus($status)
	{
		$this->db->query('update')
			->table($this->_case->table)
			->data(['alert_status' => $status])
			->where('id', $this->_case->id)
			->execute();
	}

	protected function checkIsReportCompleted()
	{
		$openStatuses = [
			\Opake\Model\Cases\OperativeReport::STATUS_OPEN,
			\Opake\Model\Cases\OperativeReport::STATUS_DRAFT
		];

		$now = new \DateTime();

		$report = $this->_case->getOpReport();
		if($report->loaded()) {
			$dosAfter48 = new \DateTime($this->_case->time_start);
			$dosAfter48->add(new \DateInterval('PT48H'));
			if ($now > $dosAfter48 && in_array($report->status, $openStatuses)) {
				$alert = $this->orm->get('Cases_Alert')
					->where([
						['case_id', $this->_case->id()],
						['code', 'cases_report_completed_48hrs_case_end'],
					])->find();

				if(!$alert->loaded()) {
					$newAlert = $this->orm->get('Cases_Alert');
					$newAlert->case_id = $this->_case->id();
					$newAlert->code = 'cases_report_completed_48hrs_case_end';
					$newAlert->type = Alert::$alertTypes[$newAlert->code];
					$newAlert->save();
				}
			} else {
				$alert = $this->orm->get('Cases_Alert')
					->where([
						['case_id', $this->_case->id()],
						['code', 'cases_report_completed_48hrs_case_end'],
					])->find();

				if($alert->loaded()) {
					$alert->delete();
				}
			}
		}
	}

	protected function checkIsInsuranceNotVerified()
	{
		return !$this->_case->registration->isVerified();
	}

	protected function checkIsPreAuthorizationCompleted()
	{
		return !$this->_case->registration->is_pre_authorization_completed;
	}

	protected function checkIsPreCertificationObtained()
	{
		return !!$this->_case->registration->pre_certification_required && !$this->_case->registration->pre_certification_obtained;
	}

	protected function checkIsSelfFunded()
	{
		return !!$this->_case->registration->self_funded;
	}

	protected function checkIsHasOONBenefits()
	{
		return $this->_case->registration->is_oon_benefits_cap;
	}

	protected function checkIsASCBenefits()
	{
		return $this->_case->registration->is_asc_benefits_cap;
	}

	protected function checkIsHasClausesUnderPatient()
	{
		return !!$this->_case->registration->is_pre_existing_clauses;
	}

	protected function checkIsHasClausesUnderMedicare()
	{
		return !!$this->_case->registration->is_clauses_pertaining;
	}

	protected function checkIsRegistrationNotCompleted()
	{
		return false;
	}

}

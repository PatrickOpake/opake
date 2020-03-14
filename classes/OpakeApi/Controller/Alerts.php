<?php

namespace OpakeApi\Controller;

use Opake\Model\Alert\Alert as OpakeAlert;

class Alerts extends AbstractController
{

	/**
	 * Service for Alerts
	 * @var \Opake\Service\Alert\Alert
	 */
	protected $service;

	public function __construct($pixie)
	{
		parent::__construct($pixie);
		$this->service = $this->services->get('alert');
	}

	public function actionMyalerts()
	{
		$type = json_decode($this->request->get('type'));
		$alerts = $this->service->findAll(null, $type);
		$result = array();
		foreach ($alerts as $alert) {
			$result[] = $alert->toArray();
		}
		$this->result = ['alerts' => $result];
	}

	public function actionAlert()
	{
		$alert = $this->service->findOne($this->request->get('alertid'));
		if (!$alert->loaded()) {
			throw new \OpakeApi\Exception\PageNotFound();
		}
		$this->result = $alert->toArray();
	}

	public function actionView()
	{
		$alert = $this->service->findOne($this->request->get('alertid'));
		if (!$alert->loaded()) {
			throw new \OpakeApi\Exception\PageNotFound();
		}
		if ($this->service->setView($alert)) {
			$alert->view_date = true;
		}
		$this->result = $alert->toArray();
	}

	public function actionChangePhase()
	{
		$alert = $this->loadModel('Alert_Alert', 'alertid');
		if (!$alert->loaded() || $alert->organization_id != $this->logged()->organization_id) {
			throw new \OpakeApi\Exception\PageNotFound();
		}
		$phase = $this->request->get('alertPhase', -1);
		if ($phase < OpakeAlert::PHASE_REQUIRES_ACTION || $phase > OpakeAlert::PHASE_RESOLVED) {
			throw new \OpakeApi\Exception\BadRequest('Unknown phase');
		}
		$alert->phase = $phase;
		$alert->save();
		$this->result = 'ok';
	}

	public function actionDelete()
	{
		$alert = $this->loadModel('Alert_Alert', 'alertid');
		if (!$alert->loaded() || $alert->organization_id != $this->logged()->organization_id) {
			throw new \OpakeApi\Exception\PageNotFound();
		}
		$this->service->delete($alert);
		$this->result = 'ok';
	}
}

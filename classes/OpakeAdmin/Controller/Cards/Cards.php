<?php

namespace OpakeAdmin\Controller\Cards;

use Opake\Helper\Config;
use OpakeAdmin\Helper\Export\PrefCardStaff;

class Cards extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/cards/' . $this->org->id => 'Preference Cards']);
		$this->view->setActiveMenu('settings.templates.cards');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('card', 'index');
		$this->view->subview = 'cards/index';
	}

	public function actionCreate()
	{
		$this->checkAccess('card', 'create');
		$user_id = $this->request->param('subid');
		$user = $this->logged();

		if ($user_id) {
			$user = $this->loadModel('User', 'subid');
			if ($user->organization_id != $this->org->id) {
				throw new \Opake\Exception\Forbidden();
			}
		}

		$this->view->user = $user;
		$this->view->addBreadCrumbs(['' => 'Create Preference Card']);
		$this->view->subview = 'cards/create';
	}

	public function actionView()
	{
		$this->view->card = $this->loadModel('PrefCard_Staff', 'subid');
		$this->view->addBreadCrumbs(['' => 'View Preference Card']);
		$this->view->subview = 'cards/view';
	}

	public function actionDownloadTemplate()
	{
		$template = $this->pixie->root_dir . Config::get('app.templates.pref_card');
		if (file_exists($template)) {
			$this->response->file('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $template, file_get_contents($template));
			$this->execute = false;
		} else {
			throw new \Opake\Exception\FileNotFound();
		}
	}

	public function actionDownloadFilledTemplate()
	{
		$card = $this->loadModel('PrefCard_Staff', 'subid');
		$exporter = new PrefCardStaff($this->pixie);
		$content = $exporter->generate($card);

		$fileName = 'Preference_Card_' . $card->id . '.xlsx';
		$this->response->file('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $fileName, $content);
		$this->execute = false;
	}

}

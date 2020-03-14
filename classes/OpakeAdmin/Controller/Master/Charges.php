<?php

namespace OpakeAdmin\Controller\Master;

use Opake\Helper\Config;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Service\Master\Charges\ChargeMasterExporter;

class Charges extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/master/charges/' . $this->org->id => 'Charge Master']);
		$this->view->setActiveMenu('settings.databases.charges-master');
		$this->view->set_template('inner');
	}

	public function upload()
	{
		if (!isset($_FILES['file_import']) || $_FILES['file_import']['error']) {
			throw new \OpakeApi\Exception\BadRequest("File cant't upload");
		}
		$tmpFilename = $_FILES['file_import']['tmp_name'];
		$ext = pathinfo($_FILES['file_import']['name'], PATHINFO_EXTENSION);
		$fname = $this->pixie->app_dir . '/_tmp/' . md5(microtime()) . '.' . $ext;
		move_uploaded_file($tmpFilename, $fname);

		$service = $this->services->get('Master_Charges');
		try {
			$service->upload($this->org->id, $fname);
			unlink($fname);
			$this->redirect(sprintf('/master/charges/%d', $this->org->id));
		} catch (\Exception $e) {
			unlink($fname);
			$this->view->errors = [$e->getMessage()];
		}
	}

	public function actionView()
	{
		$this->checkAccess('sites', 'view');
		$siteId = $this->request->param('subid');
		\OpakeAdmin\Helper\Menu\SiteNavigationMenu::prepareMenu($this->view, $siteId);
		$this->view->setActiveMenu('settings.organization.sites.charges');
		$this->view->siteId = $siteId;
		$this->view->subview = 'master/charges';
	}

	public function actionDownload()
	{
		$template = $this->pixie->root_dir . Config::get('app.templates.master.charges');
		if (file_exists($template)) {
			$this->response->file('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $template, file_get_contents($template));
			$this->execute = false;
		} else {
			throw new \Opake\Exception\FileNotFound();
		}
	}

	public function actionDownloadChargeMaster()
	{
		$siteId = $this->request->param('subid');
		$model = $this->pixie->orm->get('Master_Charge');

		$search = new \OpakeAdmin\Model\Search\Master\Charge($this->pixie, false);
		$search->setOrganizationId($this->org->id());
		$search->setSiteId($siteId);
		$results = $search->search($model, $this->request);

		$exporter = new ChargeMasterExporter($this->pixie);
		$exporter->setModels($results);
		$exporter->setTemplate($this->pixie->root_dir . Config::get('app.templates.master.charges'));
		$exporter->exportToExcel();

		$this->logDownloadToAudit($exporter, $siteId);

		$this->response->file(
			$exporter->getMimeType(),
			$exporter->getFileName(),
			$exporter->getOutput()
		);

		$this->view = null;
	}

	protected function logDownloadToAudit($exporter, $siteId)
	{
		$uploadedFile = $this->pixie->orm->get('UploadedFile');
		$uploadedFile->storeContent($exporter->getFileName(), $exporter->getOutput(), [
			'mime_type' => $exporter->getMimeType(),
		]);
		$uploadedFile->save();

		$this->pixie->activityLogger
			->newModelActionQueue($uploadedFile)
			->addAction(ActivityRecord::ACTION_MASTER_CHARGE_DOWNLOAD)
			->setAdditionalInfo('site_id', $siteId)
			->assign()
			->registerActions();
	}

}

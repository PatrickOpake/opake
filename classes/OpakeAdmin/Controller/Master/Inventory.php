<?php

namespace OpakeAdmin\Controller\Master;

use Opake\Helper\Config;
use Opake\Service\Inventory\InventoryExporter;

class Inventory extends \OpakeAdmin\Controller\AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));
		$this->view->addBreadCrumbs(['/master/inventory/' . $this->org->id => 'Item Master']);
		$this->view->setActiveMenu('settings.databases.inventory');
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

		$service = $this->services->get('Master_Inventory');
		try {
			$service->upload($this->org->id, $fname);
			unlink($fname);
			$this->redirect(sprintf('/master/inventory/%d', $this->org->id));
		} catch (\Exception $e) {
			unlink($fname);
			$this->view->errors = [$e->getMessage()];
		}
	}

	public function actionIndex()
	{
		$this->checkAccess('databases', 'view');

		if ($this->request->method === "POST") {
			$this->upload();
		}

		$this->view->subview = 'master/inventory';
	}

	public function actionDownload()
	{
		$template = $this->pixie->root_dir . Config::get('app.templates.master.inventory');
		if (file_exists($template)) {
			$this->response->file('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $template, file_get_contents($template));
			$this->execute = false;
		} else {
			throw new \Opake\Exception\FileNotFound();
		}
	}

	public function actionDownloadItemMaster()
	{
		$service = $this->services->get('Master_Inventory');
		$model = $service->getItem()->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Inventory($this->pixie, false);
		$results = $search->search($model, $this->request);

		$exporter = new InventoryExporter($this->pixie);
		$exporter->setModels($results);
//		$exporter->setTemplate($this->pixie->root_dir . Config::get('app.templates.master.inventory'));
		$exporter->setTemplate($this->pixie->root_dir . '/apps/admin/docs/Item_Master_Template.xlsx');
		$exporter->exportToExcel();

		$now = new \DateTime();

		$this->response->file(
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'Item_Master_' . $now->format('Y-m-d_h-i-s') . '.xlsx',
			$exporter->getOutput()
		);

		$this->view = null;
	}

}

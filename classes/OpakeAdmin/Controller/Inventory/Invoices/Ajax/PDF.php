<?php

namespace OpakeAdmin\Controller\Inventory\Invoices\Ajax;

use Opake\Exception\BadRequest;
use Opake\Exception\FileNotFound;
use Opake\Exception\Forbidden;
use Opake\Exception\HttpException;
use OpakeAdmin\Helper\PDF\PreviewImageGenerator;

class PDF extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionGeneratePreviewImage()
	{
		try {
			$invoice = $this->loadModel('Inventory_Invoice', 'subid');
			if (!$invoice->loaded()) {
				throw new FileNotFound('Invoice is not found');
			}

			if ($invoice->organization_id != $this->org->id()) {
				throw new Forbidden();
			}

			$page = $this->request->get('page');
			if (!$page) {
				throw new BadRequest('Page is not defined');
			}

			$previewImageGenerator = new PreviewImageGenerator($invoice->file);
			$previewImageGenerator->setPage($page);
			$previewImageGenerator->generateImage();

			$this->result = [
				'success' => true,
				'url' => '/inventory/invoices/ajax/pdf/' .
					$this->org->id() . '/getPreviewImage/' . $invoice->id() .
					'?page=' . $page
			];
		} catch (HttpException $e) {
			throw $e;
		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Exception('Error while image converting');
		}
	}

	public function actionGetPreviewImage()
	{
		$invoice = $this->loadModel('Inventory_Invoice', 'subid');
		if (!$invoice->loaded()) {
			throw new FileNotFound('Chart is not found');
		}

		if ($invoice->organization_id != $this->org->id()) {
			throw new Forbidden();
		}

		$page = $this->request->get('page');
		if (!$page) {
			throw new BadRequest('Page is not defined');
		}

		$previewImageGenerator = new PreviewImageGenerator($invoice->file);
		$previewImageGenerator->setPage($page);
		$path = $previewImageGenerator->getImagePath();

		if (!file_exists($path)) {
			throw new FileNotFound();
		}

		$this->execute = false;
		$this->response->file('image/png', 'page.png', file_get_contents($path), false);
	}
}
<?php

namespace OpakeAdmin\Controller\Billings\EOB;

use Opake\Exception\BadRequest;
use Opake\Exception\InvalidMethod;
use Opake\Helper\TimeFormat;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$items = [];

		$model = $this->orm->get('Billing_EOB')
			->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Billing\EOB($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionUploadDoc()
	{
		try {
			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			$docId = $this->request->post('id');

			if ($docId) {
				$doc = $this->orm->get('Billing_EOB', $docId);
				$doc->organization_id = $this->org->id();
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
					/** @var \Opake\Model\UploadedFile $uploadedFile */
					$uploadedFile = $this->pixie->orm->get('UploadedFile');
					$uploadedFile->storeFile($upload, [
						'is_protected' => true,
						'protected_type' => 'billing_eob'
					]);
					$uploadedFile->save();

					$docId = $this->request->post('doc_id');
					if ($docId) {
						$doc = $this->orm->get('Billing_EOB', $docId);
					} else {
						$doc = $this->orm->get('Billing_EOB');
					}
					$doc->fill($this->request->post());
					$doc->organization_id = $this->org->id();
					$doc->uploaded_file_id = $uploadedFile->id;
					$doc->uploaded_date = strftime(TimeFormat::DATE_FORMAT_DB, time());
					$doc->save();
				}
			}

			$this->result = 'ok';

		} catch (\Exception $e) {
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	public function actionCompileDocs()
	{
		try {

			$documents = $this->request->post('documents');

			if (!$documents || !is_array($documents)) {
				throw new \Exception('Documents list is empty');
			}

			$documentsList = [];
			foreach ($documents as $document) {
				$documentObject = $this->pixie->orm->get('Billing_EOB', $document['id']);
				$documentsList[] = new \OpakeAdmin\Helper\Printing\Document\Billing\EOBDocument($documentObject);
			}

			$helper =  new \OpakeAdmin\Helper\Printing\PrintCompiler();
			$result = $helper->compile($documentsList);

			$this->result = [
				'success' => true,
				'id' => $result->id(),
				'url' => $result->getResultUrl(),
				'print' => $result->isReadyToPrint()
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

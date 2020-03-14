<?php

namespace OpakeAdmin\Controller\Cards;

use Opake\Exception\BadRequest;
use Opake\Exception\PageNotFound;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionSearch()
	{
		$result = [];

		$service = $this->services->get('inventory');
		$model = $service->getItem()->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Inventory($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $item) {
			$result[] = $item->toShortArray();
		}

		$this->result = $result;
	}

	public function actionStaff()
	{
		$model = $this->pixie->orm->get('User');
		$model->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Card\User($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $result) {
			$item = $result->toShortArray();
			$item['card_amount'] = $result->card_amount;
			$items[] = $item;
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionStaffCards()
	{
		$this->checkAccess('card', 'view');

		if ($this->getAccessLevel('card', 'view')->isSelfAllowed()) {
			$user = $this->logged();
		} else {
			$user = $this->loadModel('User', 'subid');
		}
		$model = $this->pixie->orm->get('PrefCard_Staff');
		$model->where('user_id', $user->id);

		$search = new \OpakeAdmin\Model\Search\Card\Staff($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->toShortArray();
		}
		$this->result = [
			'full_name' => $user->getFullName(),
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionCard()
	{
		$card = $this->loadModel('PrefCard_Staff', 'subid');
		$this->result = $card->toArray();
	}

	public function actionExportStaffPrefCard()
	{
		$prefCardIds = $this->request->post('cards');
		if (!$prefCardIds) {
			throw new BadRequest('Cards is required param');
		}

		try {

			$documentsToPrint = [];
			foreach ($prefCardIds as $prefCardId) {
				$prefCard = $this->pixie->orm->get('PrefCard_Staff', $prefCardId);
				if ($prefCard->loaded()) {
					$documentsToPrint[] = new \OpakeAdmin\Helper\Printing\Document\PrefCard\StaffPrefCard($prefCard);
				}
			}

			if (!$documentsToPrint) {
				throw new \Exception('Document for print list is empty');
			}

			$printHelper =  new \OpakeAdmin\Helper\Printing\PrintCompiler();
			$printResult = $printHelper->compile($documentsToPrint);

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

	public function actionExportCaseStaffPrefCard()
	{
		$case = $this->loadModel('Cases_Item', 'subid');

		try {
			$card = $case->getCard();
			$user = null;

			if (!$card->loaded()) {
				$firstSurgeonId = $case->getFirstSurgeonId();
				$user = $this->orm->get('User', $firstSurgeonId);
				if (!$user->loaded()) {
					$user = null;
				}
			}

			$document = new \OpakeAdmin\Helper\Printing\Document\Cases\StaffCard($case, $card, $user);
			$printHelper =  new \OpakeAdmin\Helper\Printing\PrintCompiler();
			$printResult = $printHelper->compile([$document]);

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

	public function actionUploadTemplate()
	{
		$files = $this->request->getFiles();

		if (empty($files['file'])) {
			throw new BadRequest('Empty file');
		}
		$file = $files['file'];
		if ($file->isEmpty()) {
			throw new BadRequest('Empty file');
		}
		if ($file->hasErrors()) {
			throw new \Exception('An error occurred while file loading');
		}

		$tmpFile = new \Opake\Helper\File\TemporaryFile($file);
		$tmpFile->create();

		$importer = new \OpakeAdmin\Helper\Import\PrefCardStaff($this->pixie);
		$success = $importer->load($tmpFile->getFilePath(), $this->org->id());

		$tmpFile->cleanup();

		if ($success) {
			$caseTypesResult = [];
			foreach ($importer->getCaseTypes() as $caseType) {
				$caseTypesResult[] = $caseType->toArray();
			}

			$notesResult = [];
			foreach ($importer->getNotes() as $note) {
				$notesResult[] = $note->toArray();
			}

			$itemsResult = [];
			foreach ($importer->getItems() as $item) {
				$itemsResult[] = $item->toArray();
			}

			$this->result = [
				'success' => true,
				'name' => $importer->getName(),
				'case_types' => $caseTypesResult,
				'notes' => $notesResult,
				'items' => $itemsResult
			];
		} else {
			$this->result = [
				'success' => false,
				'errors' => $importer->getErrors()
			];
		}
	}
}

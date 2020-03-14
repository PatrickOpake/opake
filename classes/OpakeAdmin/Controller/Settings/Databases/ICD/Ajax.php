<?php

namespace OpakeAdmin\Controller\Settings\Databases\ICD;

use Opake\Exception\BadRequest;
use Opake\Exception\InvalidMethod;
use Opake\Helper\Pagination;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}
	}

	public function actionIndex()
	{
		$pagination = new Pagination();
		$pagination->setPage($this->request->get('p'));
		$pagination->setLimit($this->request->get('l'));

		$model = $this->orm->get('IcdYear');
		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));

		$results = $model->pagination($pagination)->find_all()->as_array();

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->toArray();
		}

		$count = $this->pixie->db
				->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
				->execute()->get('count');

		$this->result = [
			'items' => $items,
			'total_count' => $count
		];
	}

	public function actionGetYearById()
	{
		$model = $this->loadModel('IcdYear', 'id');

		$this->result = [
			'year' => $model->year
		];
	}

	public function actionGetIcdsForYear()
	{
		$yearId = $this->request->param('id');
		$pagination = new Pagination();
		$pagination->setPage($this->request->get('p'));
		$pagination->setLimit($this->request->get('l'));

		$model = $this->orm->get('ICD');

		$model->query->join('icd_to_icd_year', ['icd_to_icd_year.icd_id', 'icd.id'])
			->where('icd_to_icd_year.year_id', $yearId);

		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*, icd_to_icd_year.active'));
		$model->order_by('code', 'asc');

		$results = $model->pagination($pagination)->find_all()->as_array();

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->toArrayWithStatus();
		}

		$count = $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');

		$this->result = [
			'items' => $items,
			'total_count' => $count
		];
	}

	public function actionActivate()
	{
		$icdId = $this->request->param('id');
		$yearId = $this->request->get('year_id');

		$this->pixie->db->query('update')
			->table('icd_to_icd_year')
			->data(['active' => 1])
			->where(['icd_id', $icdId], ['year_id', $yearId])
			->execute();
	}

	public function actionDeactivate()
	{
		$icdId = $this->request->param('id');
		$yearId = $this->request->get('year_id');

		$this->pixie->db->query('update')
			->table('icd_to_icd_year')
			->data(['active' => 0])
			->where(['icd_id', $icdId], ['year_id', $yearId])
			->execute();
	}

	public function actionUploadNewDb()
	{
		try {

			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			/** @var \Opake\Request $req */
			$req = $this->request;

			$postData = $req->post();
			$postData['files'] = $req->getFiles();

			$files = $req->getFiles();
			if (empty($files['file'])) {
				throw new BadRequest('Empty file');
			}

			$file = $files['file'];
			$year = $postData['year'];
			$yearId = $postData['year_id'];
			$notes = isset($postData['notes']) ? $postData['notes'] : null;

			$type = $file->getType();

			$allowedTypes = \OpakeAdmin\Helper\Import\ICD::getAllowedMimeTypes();

			if (!in_array($type, $allowedTypes)) {
				$this->result = [
					'success' => false,
					'errors' => ['Uploaded file is not supported for import']
				];
				return;
			}
			
			if ($yearId) {
				$this->pixie->db->query('delete')->table('icd_to_icd_year')->where('year_id', $yearId)->execute();
				$this->pixie->db->query('delete')->table('icd_year')->where('id', $yearId)->execute();
			}

			$icdYear = $this->orm->get('IcdYear', isset($yearId) ? $yearId : null);
			$icdYear->year = $year;
			$icdYear->note = $notes;
			$icdYear->save();

			$tmpFile = new \Opake\Helper\File\TemporaryFile($file);
			$tmpFile->create();

			try {
				$importer = new \OpakeAdmin\Helper\Import\ICD($this->pixie);
				$importer->setYearId($icdYear->id);
				$importer->load($tmpFile->getFilePath());
				$tmpFile->cleanup();
			} catch (\Exception $e) {
				$tmpFile->cleanup();
				$this->logSystemError($e);
				$this->result = [
					'success' => false,
					'errors' => [$e->getMessage()]
				];
				return;
			}

			$this->result = 'ok';

		} catch (\Exception $e) {
			$this->result = [
				'success' => false,
				'errors' => $e->getMessage()
			];
		}
	}

	public function actionHasDbForYear()
	{
		$icdYear = $this->orm->get('IcdYear')->where('year', $this->request->param('id'))->limit(1)->find();
		$yearId = false;
		if ($icdYear->loaded()) {
			$yearId = $icdYear->id;
		}

		$this->result = [
			'yearId' => $yearId
		];
	}
}

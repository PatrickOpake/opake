<?php

namespace OpakeAdmin\Controller\Settings\Databases\HCPC;

use Opake\Exception\BadRequest;
use Opake\Exception\InvalidMethod;
use Opake\Helper\Pagination;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();

		if (!$this->logged() || !$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}
	}

	public function actionIndex()
	{
		$pagination = new Pagination();
		$pagination->setPage($this->request->get('p'));
		$pagination->setLimit($this->request->get('l'));

		$model = $this->orm->get('HCPCYear');
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
		$model = $this->loadModel('HCPCYear', 'id');

		$this->result = [
			'year' => $model->year
		];
	}

	public function actionGetHcpcForYear()
	{
		$yearId = $this->request->param('id');
		$pagination = new Pagination();
		$pagination->setPage($this->request->get('p'));
		$pagination->setLimit($this->request->get('l'));

		$model = $this->orm->get('HCPC');

		$model->query->join('hcpc_to_hcpc_year', ['hcpc_to_hcpc_year.hcpc_id', 'hcpc.id'])
			->where('hcpc_to_hcpc_year.year_id', $yearId);

		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));
		$model->order_by('code', 'asc');

		$results = $model->pagination($pagination)->find_all()->as_array();

		$count = $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $count
		];
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

			$allowedTypes = \OpakeAdmin\Helper\Import\HCPC::getAllowedMimeTypes();

			if (!in_array($type, $allowedTypes)) {
				$this->result = [
					'success' => false,
					'errors' => ['Uploaded file is not supported for import']
				];
				return;
			}

			if ($yearId) {
				$this->pixie->db->query('delete')
					->table('hcpc_to_hcpc_year')
					->where('year_id', $yearId)
					->execute();

				$this->pixie->db->query('delete')
					->table('hcpc_year')
					->where('id', $yearId)
					->execute();
			}

			$hcpcYear = $this->orm->get('HCPCYear', isset($yearId) ? $yearId : null);
			$hcpcYear->year = $year;
			$hcpcYear->note = $notes;
			$hcpcYear->save();

			$tmpFile = new \Opake\Helper\File\TemporaryFile($file);
			$tmpFile->create();

			try {
				$importer = new \OpakeAdmin\Helper\Import\HCPC($this->pixie);
				$importer->setYearId($hcpcYear->id);
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
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
		}
	}

	public function actionHasDbForYear()
	{
		$hcpcYear = $this->orm->get('HCPCYear')->where('year', $this->request->param('id'))->limit(1)->find();
		$yearId = false;
		if ($hcpcYear->loaded()) {
			$yearId = $hcpcYear->id;
		}

		$this->result = [
			'yearId' => $yearId
		];
	}

}

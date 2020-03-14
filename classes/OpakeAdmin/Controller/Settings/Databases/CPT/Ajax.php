<?php

namespace OpakeAdmin\Controller\Settings\Databases\CPT;

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

		$model = $this->orm->get('CptYear');
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
		$model = $this->loadModel('CptYear', 'id');

		$this->result = [
			'year' => $model->year
		];
	}

	public function actionGetCptsForYear()
	{
		$yearId = $this->request->param('id');
		$pagination = new Pagination();
		$pagination->setPage($this->request->get('p'));
		$pagination->setLimit($this->request->get('l'));

		$model = $this->orm->get('CPT');

		$model->query->join('cpt_to_cpt_year', ['cpt_to_cpt_year.cpt_id', 'cpt.id'])
			->where('cpt_to_cpt_year.year_id', $yearId);

		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*, cpt_to_cpt_year.active'));
		$model->order_by('code', 'asc');

		$results = $model->pagination($pagination)->find_all()->as_array();

		$count = $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->toArrayWithStatus();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $count
		];
	}

	public function actionActivate()
	{
		$cptId = $this->request->param('id');
		$yearId = $this->request->get('year_id');

		$this->pixie->db->query('update')
			->table('cpt_to_cpt_year')
			->data(['active' => 1])
			->where(['cpt_id', $cptId], ['year_id', $yearId])
			->execute();
	}

	public function actionDeactivate()
	{
		$cptId = $this->request->param('id');
		$yearId = $this->request->get('year_id');

		$this->pixie->db->query('update')
			->table('cpt_to_cpt_year')
			->data(['active' => 0])
			->where(['cpt_id', $cptId], ['year_id', $yearId])
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

			$allowedTypes = \OpakeAdmin\Helper\Import\CPT::getAllowedMimeTypes();

			if (!in_array($type, $allowedTypes)) {
				$this->result = [
					'success' => false,
					'errors' => ['Uploaded file is not supported for import']
				];
				return;
			}
			
			if ($yearId) {
				$this->pixie->db->query('delete')
					->table('cpt_to_cpt_year')
					->where('year_id', $yearId)
					->execute();

				$this->pixie->db->query('delete')
					->table('cpt_year')
					->where('id', $yearId)
					->execute();
			}

			$cptYear = $this->orm->get('CptYear', isset($yearId) ? $yearId : null);
			$cptYear->year = $year;
			$cptYear->note = $notes;
			$cptYear->save();

			$tmpFile = new \Opake\Helper\File\TemporaryFile($file);
			$tmpFile->create();

			try {
				$importer = new \OpakeAdmin\Helper\Import\CPT($this->pixie);
				$importer->setYearId($cptYear->id);
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
		$cptYear = $this->orm->get('CptYear')->where('year', $this->request->param('id'))->limit(1)->find();
		$yearId = false;
		if ($cptYear->loaded()) {
			$yearId = $cptYear->id;
		}

		$this->result = [
			'yearId' => $yearId
		];
	}
}

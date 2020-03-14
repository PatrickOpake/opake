<?php

namespace OpakeAdmin\Model\Search\Forms;

use Opake\Model\Search\AbstractSearch;

class Document extends AbstractSearch
{
	public function search($model, $request) {

		$model = parent::prepare($model, $request);

		$this->_params = [
			'segment' => trim($request->get('segment')),
			'type' => trim($request->get('type')),
			'caseId' => trim($request->get('caseId')),
		];

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));

		if ($this->_params['segment'] !== '') {
			$model->where($model->table . '.segment', $this->_params['segment'] );
		}

		if ($this->_params['type'] !== '') {
			$model->where($model->table . '.type', $this->_params['type'] );
		}

		if ($this->_params['caseId'] !== '') {

			$case = $this->pixie->orm->get('Cases_Item')
				->where('id', $this->_params['caseId'])
				->find();

			if (!$case->loaded()) {
				throw new \Exception('Case is not loaded');
			}

			$siteId = $case->location->site->id;

			$model->query->join(['forms_document_site', 'fds'], ['forms_document.id', 'fds.doc_id'])
				->join('site', ['site.id', 'fds.site_id'])
				->where([[['site.id', $siteId]], ['or', ['forms_document.is_all_sites', 1]]]);

		}

		$model->order_by('name', 'ASC');

		$results = $model->find_all()->as_array();

		$count = $this->pixie->db
				->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
				->execute()->get('count');

		$this->_pagination->setCount($count);

		return $results;
	}

}

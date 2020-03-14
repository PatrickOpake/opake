<?php

namespace OpakeAdmin\Controller\Analytics\SmsLog;

use Opake\Helper\Pagination;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionList()
	{
		$pagination = new Pagination();
		$pagination->setPage($this->request->get('p'));
		$pagination->setLimit($this->request->get('l'));

		$model = $this->orm->get('SmsLog');
		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));
		$model->query->join('case_sms_log', ['sms_log.id', 'case_sms_log.sms_log_id']);
		$model->query->join('case', ['case_sms_log.case_id', 'case.id']);
		$model->query->where('case.organization_id', $this->org->id());
		$model->order_by('id', 'DESC');


		if ($caseId = $this->request->get('case_id')) {
			$model->query->where('case_sms_log.case_id', $caseId);
		}

		$results = $model->pagination($pagination)
			->find_all()
			->as_array();

		$count = $this->pixie->db
			->query('select')
			->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()
			->get('count');

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->getFormatter('AnalyticsSmsLog')->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $count
		];
	}
}
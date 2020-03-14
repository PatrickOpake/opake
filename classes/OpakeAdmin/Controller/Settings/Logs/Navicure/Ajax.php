<?php

namespace OpakeAdmin\Controller\Settings\Logs\Navicure;

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

		$model = $this->orm->get('Billing_Navicure_Log');
		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));
		$model->order_by('id', 'DESC');

		if ($claimId = $this->request->get('claim_id')) {
			$model->query->where('claim_id', $claimId);
		}

		if ($transaction = $this->request->get('transaction')) {
			if ($transaction == -1) {
				$model->query->where('transaction', 'IS NULL', $this->pixie->db->expr(''));
			} else {
				$model->query->where('transaction', $transaction);
			}
		}

		if ($this->request->get('only_with_errors') === 'true') {
			$model->query->where('error', 'IS NOT NULL', $this->pixie->db->expr(''));
			$model->query->where('error', '!=', '');
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
			$items[] = $result->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $count
		];
	}

}

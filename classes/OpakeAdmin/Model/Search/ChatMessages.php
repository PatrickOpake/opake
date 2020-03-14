<?php

namespace OpakeAdmin\Model\Search;

use Opake\Model\Search\AbstractSearch;

class ChatMessages extends AbstractSearch
{
	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$this->_params = [
			'date' => trim($request->get('date'))
		];

		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));

		if ($this->_params['date'] !== '') {
			$model->where($this->pixie->db->expr('DATE(chat_message.date)'), \Opake\Helper\TimeFormat::formatToDB($this->_params['date']))
				->order_by('chat_message.date', 'ASC');
		}

		$results = $model->pagination($this->_pagination)->find_all()->as_array();

		$count = $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');

		$this->_pagination->setCount($count);

		return $results;
	}
}

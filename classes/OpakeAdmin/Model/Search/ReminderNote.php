<?php

namespace OpakeAdmin\Model\Search;

use Opake\Model\Search\AbstractSearch;

class ReminderNote extends AbstractSearch
{
	protected $user;

	/**
	 * @return mixed
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param mixed $user
	 */
	public function setUser($user)
	{
		$this->user = $user;
	}

	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$db = $this->pixie->db;

		$this->_params = [
		];

		$user = $this->getUser();

		if (!$user) {
			throw new \Exception('User is required to get reminders');
		}

		$query = $model->query;
		$query->fields($db->expr('SQL_CALC_FOUND_ROWS `' . $model->table .'`.*'));
		$query->where('user_id', $user->id());
		$query->where('is_completed', 0);
		$query->where($this->pixie->db->expr('DATE(reminder_date)'), \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

		$query->order_by('id', 'desc');

		if ($this->_pagination) {
			$model->pagination($this->_pagination);
		}

		$results = $model->find_all()
			->as_array();

		$count = $db->query('select')
			->fields($db->expr('FOUND_ROWS() as count'))
			->execute()
			->get('count');

		if ($this->_pagination) {
			$this->_pagination->setCount($count);
		}

		return $results;
	}


	public function getCount($model)
	{
		$user = $this->getUser();
		$query = $model->query;
		$query->where('user_id', $user->id);
		$query->where('is_completed', 0);
		$query->where($this->pixie->db->expr('DATE(reminder_date)'), \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

		return $model->count_all();
	}

}
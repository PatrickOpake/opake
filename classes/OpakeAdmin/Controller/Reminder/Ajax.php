<?php

namespace OpakeAdmin\Controller\Reminder;


class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function actionList()
	{
		$user = $this->logged();
		if (!$user) {
			throw new \Opake\Exception\Forbidden('Not authorized');
		}

		$items = [];

		$search = new \OpakeAdmin\Model\Search\ReminderNote($this->pixie, false);
		$search->setUser($user);
		$results = $search->search(
			$this->orm->get('ReminderNote'),
			$this->request
		);

		foreach ($results as $result) {
			$items[] = $result
				->getFormatter('WidgetList')
				->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getCount($this->orm->get('ReminderNote'))
		];
	}

	public function actionGetCountReminders()
	{
		$user = $this->logged();
		if (!$user) {
			throw new \Opake\Exception\Forbidden('Not authorized');
		}
		$search = new \OpakeAdmin\Model\Search\ReminderNote($this->pixie, false);
		$search->setUser($user);
		$count = $search->getCount(
			$this->orm->get('ReminderNote')
		);

		$this->result = [
			'count' => (int)$count,
		];
	}

	public function actionComplete()
	{
		$reminders = $this->request->post('reminders');

		$this->pixie->db->query('update')
			->table('reminder_note')
			->data(['is_completed' => 1])
			->where('id', 'IN', $this->pixie->db->arr($reminders))
			->execute();

		$this->result = 'ok';
	}
}
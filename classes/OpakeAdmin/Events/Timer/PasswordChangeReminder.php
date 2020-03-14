<?php


namespace OpakeAdmin\Events\Timer;

use Opake\Events\AbstractListener;
use Opake\Helper\TimeFormat;

class PasswordChangeReminder extends AbstractListener
{
	public function dispatch($event)
	{
		$isEnabled = $this->pixie->config->get('app.password_change_reminder.enabled');
		if ($isEnabled) {
			$daysSinceLastChange = (int) $this->pixie->config->get('app.password_change_reminder.days_since_last_change');

			$date = new \DateTime();
			$date->modify('- ' . $daysSinceLastChange . ' days');

			$rows = $this->pixie->db->query('select')
				->table('user')
				->fields('id')
				->where('last_password_change_date', '<=', TimeFormat::formatToDBDatetime($date))
				//exclude super admin
				->where('type', 'external')
				->where('status', 'active')
				->where('is_scheduled_password_change', 0)
				->execute()
				->as_array();

			$this->pixie->db->begin_transaction();

			try {

				foreach ($rows as $row) {
					$this->pixie->db->query('update')
						->table('user')
						->data([
							'is_temp_password' => 1,
							'is_scheduled_password_change' => 1
						])
						->where('id', $row->id)
						->execute();
				}

				$this->pixie->db->commit();

			} catch (\Exception $e) {
				$this->pixie->db->rollback();
				throw $e;
			}
		}

	}

}
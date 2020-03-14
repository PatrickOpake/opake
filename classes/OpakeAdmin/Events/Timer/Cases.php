<?php

namespace OpakeAdmin\Events\Timer;

class Cases extends \Opake\Events\AbstractListener
{

	public function dispatch($obj)
	{

		$now = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB);
		$now2 = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime('+2 day'));

		$model = $this->orm->get('Cases_Item');
		$model->where([
			['time_start', '>', $now2],
			['and', ['alert_status', '!=', \Opake\Model\Cases\Item::STATUS_BEFORE]]
		], ['or', [
			['time_start', '>', $now],
			['and', ['time_start', '<', $now2]],
			['and', ['alert_status', '!=', \Opake\Model\Cases\Item::STATUS_PRIOR]]
		]], ['or', [
			['time_start', '<', $now],
			['and', ['time_end', '>', $now]],
			['and', ['alert_status', '!=', \Opake\Model\Cases\Item::STATUS_DURING]]
		]], ['or', [
			['time_end', '<', $now],
			['and', ['alert_status', '!=', \Opake\Model\Cases\Item::STATUS_AFTER]]
		]]);

		foreach ($model->find_all() as $item) {
			$this->pixie->events->fireEvent('update.case', $item);
		}
	}

}

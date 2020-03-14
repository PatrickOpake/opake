<?php

namespace OpakeAdmin\Events\Timer;

use Opake\Events\AbstractListener;
use Opake\Helper\TimeFormat;
use OpakeAdmin\Helper\SMS\CaseNotifier;

class CasesSmsReminder extends AbstractListener
{

	public function dispatch($event)
	{
		$now = new \DateTime();
		$model = $this->orm->get('Cases_Item');
		$model->query->fields($this->pixie->db->expr('`' . $model->table . '`.*'))
			->join('sms_template', [$model->table . '.organization_id', 'sms_template.organization_id'])
			->where('sms_template.reminder_sms', 1)
			->where('case.time_start', '<=', $this->db->expr('DATE_ADD(\'' . TimeFormat::formatToDBDatetime($now) . '\', INTERVAL sms_template.hours_before HOUR)'))
			->where('case.time_start', '>', $this->db->expr('DATE_ADD(\'' . TimeFormat::formatToDBDatetime($now) . '\', INTERVAL (sms_template.hours_before - 1) HOUR)'));

		$notifiers = [];
		foreach ($model->find_all() as $case) {
			$orgId = $case->organization_id;
			if (!isset($notifiers[$orgId])) {
				$smsTemplate = $this->orm->get('SmsTemplate')
					->where('organization_id', $orgId)
					->limit(1)->find();
				$notifiers[$orgId] = new CaseNotifier($smsTemplate);
			}
			$notifier = $notifiers[$orgId];
			$notifier->remind($case);
		}
	}

}

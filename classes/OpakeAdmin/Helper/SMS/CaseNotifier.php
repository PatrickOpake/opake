<?php

namespace OpakeAdmin\Helper\SMS;

use Opake\Model\Cases\SmsLog;
use Opake\Helper\TimeFormat;

class CaseNotifier
{

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var \Opake\Model\SmsTemplate 
	 */
	protected $template;

	public function __construct($template)
	{
		$this->pixie = \Opake\Application::get();
		if (!$template || !$template->loaded()) {
			throw new \Exception('SMS Template is\'n defined');
		}
		$this->template = $template;
	}

	protected function notify($case, $type, $phone, $template)
	{
		$phoneCode = null;
		if ($this->pixie->environment === 'qa' && $case->registration->first_name === 'SmsTest') {
			$phoneCode = '+7';
		}

		$body = $this->replaceDynamicFieldsTemplate($case, $template);

		try {
			$log = $this->pixie->orm->get('SmsLog');
			$log->send($body, $phone, $phoneCode);
		} catch (\Exception $e) {
			$this->pixie->logger->exception($e);
		}
		$log->save();

		$casesLog = $this->pixie->orm->get('Cases_SmsLog');
		$casesLog->case_id = $case->id;
		$casesLog->sms_log_id = $log->id;
		$casesLog->type = $type;
		$casesLog->save();

		return (boolean) $log->status;
	}

	public function remind($case)
	{
		if (!$this->template->reminder_sms || !$this->template->schedule_msg) {
			return;
		}
		$registration = $case->registration;
		$template = $this->template->schedule_msg;
		$phones = [];

		if ($registration->home_phone_type == \Opake\Model\Patient::PHONE_CELL && $registration->home_phone) {
			$phones[] = $registration->home_phone;
		}
		if ($registration->additional_phone_type == \Opake\Model\Patient::PHONE_CELL && $registration->additional_phone) {
			$phones[] = $registration->additional_phone;
		}

		if (!empty($phones)) {
			$remainedPhones = $this->getRemindedPhones($case);
			foreach ($phones as $phone) {
				if (in_array($phone, $remainedPhones)) {
					continue;
				}
				$this->notify($case, SmsLog::TYPE_REMIND, $phone, $template);
			}
		}
	}

	public function notifyPointContact($case)
	{
		if ($this->template->poc_sms) {
			$registration = $case->registration;
			$template = $this->template->poc_msg;
			if ($registration->point_of_contact_phone_type == \Opake\Model\Patient::PHONE_CELL && $registration->point_of_contact_phone) {
				return $this->notify($case, SmsLog::TYPE_POINT_OF_CONTACT, $registration->point_of_contact_phone, $template);
			}
		}
		return false;
	}

	/**
	 * @param $case
	 * @param $template
	 * @return string
	 */
	protected function replaceDynamicFieldsTemplate($case, $template)
	{
		return strtr($template, [
			'{Appointment}' => TimeFormat::fromDBDatetime($case->time_start)->format('g:ia \o\n M j, Y')
		]);
	}

	protected function getRemindedPhones($case)
	{
		$remindTime = TimeFormat::fromDBDatetime($case->time_start)
			->modify('-' . $this->template->hours_before . ' hour');

		$rows = $this->pixie->db->query('select')
			->table('case_sms_log')
			->fields('sms_log.phone_to')
			->join('sms_log', ['case_sms_log.sms_log_id', 'sms_log.id'])
			->where('sms_log.send_date', '>=', TimeFormat::formatToDBDatetime($remindTime))
			->where('case_sms_log.type', SmsLog::TYPE_REMIND)
			->where('case_sms_log.case_id', $case->id())
			->execute();

		$result = [];
		foreach ($rows as $row) {
			$result[] = $row->phone_to;
		}
		return $result;
	}

}

<?php

namespace Opake\Model;

/**
 * Reminder for Note
 *
 */
class ReminderNote extends AbstractModel
{
	const TYPE_NOTE_CASES = 1;
	const TYPE_NOTE_BILLING = 2;
	const TYPE_NOTE_BOOKING = 3;
	const TYPE_NOTE_OP_REPORT = 4;
	const TYPE_NOTE_CARD_STAFF = 5;
	const TYPE_NOTE_CARD_PREF_CARD = 6;
	const TYPE_NOTE_APPLIED_PAYMENT = 7;

	public $id_field = 'id';
	public $table = 'reminder_note';
	protected $_row = array(
		'id' => null,
		'user_id' => null,
		'is_completed' => 0,
		'reminder_date' => null,
		'note_type' => null,
		'note_id' => null,
	);

	protected $belongs_to = [
		'user' => [
			'model' => 'User',
			'key' => 'user_id'
		],
	];

	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ReminderNote\BaseReminderNoteFormatter',
	];

	protected $formatters = [
		'WidgetList' => [
			'class' => '\Opake\Formatter\ReminderNote\WidgetListFormatter'
		]
	];

	public function getNote()
	{
		switch ($this->note_type)
		{
			case self::TYPE_NOTE_CASES :
				return $this->pixie->orm->get('Cases_Note', $this->note_id);
			break;
			case self::TYPE_NOTE_BILLING :
				return $this->pixie->orm->get('Billing_Note', $this->note_id);
			break;
			case self::TYPE_NOTE_BOOKING :
				return $this->pixie->orm->get('Booking_Note', $this->note_id);
			break;
			case self::TYPE_NOTE_OP_REPORT :
				return $this->pixie->orm->get('Cases_OperativeReport_Note', $this->note_id);
			break;
			case self::TYPE_NOTE_CARD_STAFF :
				return $this->pixie->orm->get('Card_Staff_Note', $this->note_id);
			break;
			case self::TYPE_NOTE_CARD_PREF_CARD :
				return $this->pixie->orm->get('PrefCard_Staff_Note', $this->note_id);
			break;
			case self::TYPE_NOTE_APPLIED_PAYMENT :
				return $this->pixie->orm->get('Billing_PaymentPosting_AppliedPayment_Note', $this->note_id);
			break;
		}
		return null;
	}

}

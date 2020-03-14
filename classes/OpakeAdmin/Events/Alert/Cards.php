<?php

namespace OpakeAdmin\Events\Alert;

use Opake\Model\Alert\Alert as OpakeAlert;

class Cards extends \Opake\Events\AbstractListener
{

	/**
	 * Заменяет или создаёт алерт для Preference Card
	 *
	 * @param \Opake\Model\Cases\PrefCard $card
	 * @return \Opake\Model\Alert\Alert
	 */
	public function dispatch($card)
	{

		// находим алерты, связанные с картой
		$alerts = $this->orm->get('alert_alert')
			->where('type', OpakeAlert::TYPE_PREFERENCE_CARD)
			->where('object_id', $card->id())
			->find_all()->as_array();

		if (sizeof($alerts)) {
			// теперь находим кейсы для этих алертов
			// и запускаем для них событие 'save'
			foreach ($alerts as $alert) {
				$case = $this->orm->get('cases_item')->where('id', $alert->case_id)->find();
				$this->pixie->events->fireEvent('save.case', $case);
			}
		}
		// находим кейсы, на которые могла повлиять карта
		$cases = $this->orm->get('cases_item');
		$cases = $cases->where('type_id', $card->case_type_id);
		// @todo нужно ещё сюда добавить проверку пользователя (что именно для него кейс)
		$cases = $cases->find_all()->as_array();
		foreach ($cases as $case) {
			$this->pixie->events->fireEvent('save.case', $case);
		}
	}

}

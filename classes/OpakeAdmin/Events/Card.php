<?php

namespace OpakeAdmin\Events;

use Opake\Events\AbstractListener;

class Card extends AbstractListener
{

	/**
	 * Заменяет или создаёт алерт для Preference Card
	 *
	 * @param \Opake\Model\Card\Staff $card
	 */
	public function dispatch($card)
	{
		$this->pixie->events->fireEvent('update.case', $card->case);
	}

}

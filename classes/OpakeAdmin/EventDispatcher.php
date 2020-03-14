<?php

/**
 * Доступ к наблюдателям приложения
 */
namespace OpakeAdmin;

class EventDispatcher extends \Opake\EventDispatcher
{

	protected function initEvents()
	{
		$self = $this;
		// TODO: подумать чтобы эти лентяи сами себя регистрировали, тогда и get не нужен
		$this->register('save.case', $this->get('alert_cases'));
		//$this->register('save.card_staff', $this->get('card'));
		//$this->register('save.pref_card_staff', $this->get('alert_cards'));
		$this->register('save.inventory', $this->get('alert_inventory_low'));
		$this->register('save.inventory', $this->get('alert_inventory_expiring'));
		$this->register('save.packs', $this->get('alert_inventory_low'));
		$this->register('save.packs', $this->get('alert_inventory_expiring'));
		$this->register('save.order_outgoing', $this->get('order_outgoing_save'));
		$this->register('save.case_registration', function($obj) use ($self) {
			$self->get('alert_cases')->dispatch($obj->case);
		});

		$this->register('update.expiring', $this->get('alert_inventory_expiring'));
		$this->register('update.case', $this->get('alert_cases'));
		$this->register('update.expiring_credentials', $this->get('alert_credentials'));

		$this->register('order.delete_item', function ($obj) use ($self) {
			$self->get('alert_inventory_low')->dispatch($obj->inventory);
			$self->get('order_outgoing_item_delete')->dispatch($obj);
		});

		$this->register('timer.hour', $this->get('timer_expiring'));
		$this->register('timer.minute', $this->get('timer_cases'));
		$this->register('timer.hour', $this->get('Timer_CleaningTemporaryDocuments'));
		$this->register('timer.day', $this->get('Timer_PasswordChangeReminder'));
		$this->register('timer.day', $this->get('Timer_Credentials'));
		$this->register('timer.minute10', $this->get('Timer_CasesSmsReminder'));
		$this->register('timer.minute10', $this->get('Timer_NavicureClaimPoll'));
		$this->register('timer.minute10', $this->get('Timer_EfaxChecking'));
	}
}

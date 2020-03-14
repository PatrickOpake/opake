<?php

namespace OpakeAdmin\Events\Timer;

use Opake\Events\AbstractListener;

class EfaxChecking extends AbstractListener
{
	public function dispatch($obj)
	{
		$app = \Opake\Application::get();

		if ($app->config->has('app.scrypt_sfax_api.disable_polling')) {
			if ($app->config->get('app.scrypt_sfax_api.disable_polling')) {
				return;
			}
		}

		$faxService =  new \OpakeAdmin\Service\Scrypt\SFax\FaxService();
		$faxService->checkInboundFaxes();
	}
}
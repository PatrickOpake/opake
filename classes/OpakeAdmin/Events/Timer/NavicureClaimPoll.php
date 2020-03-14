<?php

namespace OpakeAdmin\Events\Timer;

use Opake\Events\AbstractListener;

class NavicureClaimPoll extends AbstractListener
{
	public function dispatch($obj)
	{
		$app = \Opake\Application::get();

		if ($app->config->has('app.navicure_api.sftp.disable_polling')) {
			if ($app->config->get('app.navicure_api.sftp.disable_polling')) {
				return;
			}
		}

		$handler = new \OpakeAdmin\Service\Navicure\Claims\ResponseHandler();
		$handler->handleIncomingFiles();
	}
}
<?php

class BaseApiCest
{
	protected function loginAsAdmin(ApiTester $I)
	{
		$I->sendPOST('/?route=/user/login', [
			'user' => 'api-test-admin@example.com',
			'pwd' => 'password'
		]);
	}
}
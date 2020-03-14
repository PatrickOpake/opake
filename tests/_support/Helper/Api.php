<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module
{
	public function _beforeSuite()
	{
		$root = __DIR__ . '/../../../';
		$pixie = new \OpakeApi\Application();
		$pixie->bootstrap($root);

		$provider = new Api\DataProvider($pixie);
		$provider->init();
	}
}

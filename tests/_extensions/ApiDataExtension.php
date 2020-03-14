<?php

class ApiDataExtension extends \Codeception\Extension
{
	// list events to listen to
	public static $events = array(
		'suite.before' => 'beforeSuite'
	);

	// methods that handle events

	public function beforeSuite(\Codeception\Event\SuiteEvent $e)
	{

	}
}
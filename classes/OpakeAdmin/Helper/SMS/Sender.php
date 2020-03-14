<?php

namespace OpakeAdmin\Helper\SMS;

use Twilio\Rest\Client;
use Opake\Helper\Config;

class Sender
{

	/**
	 * @var Twilio\Rest\Client
	 */
	protected $client;

	/**
	 * @var string
	 */
	protected $phone_from;

	/**
	 * @var Sender
	 */
	protected static $instance;

	/**
	 * @return Sender
	 */
	public static function getInstance()
	{
		return (self::$instance) ? : (self::$instance = new self());
	}

	/**
	 * @param string $sid
	 * @param string $token
	 */
	protected function __construct()
	{
		$config = Config::get('app.twilio_api');
		$this->client = new Client($config['account_sid'], $config['auth_token']);
		$this->phone_from = $config['phone_from'];
	}

	/**
	 * @param string $body
	 * @param string $phoneTo
	 * @return string sid of new message
	 */
	public function send($body, $phoneTo, $code = null)
	{
		if ($code) {
			$phoneTo = $code . $phoneTo;
		}
		$message = $this->client->messages->create($phoneTo, [
			'from' => $this->phone_from,
			'body' => $body
			]
		);
		return $message->sid;
	}

}

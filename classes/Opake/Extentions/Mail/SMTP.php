<?php

namespace Opake\Extentions\Mail;

class SMTP
{

	protected $mailer = null;
	protected $config = array();

	public function __construct()
	{
		$this->mailer = new \PHPMailer(true);
		$this->mailer->isSMTP();
		$this->mailer->AllowEmpty = true;
	}

	public function setConfig($config)
	{
		$this->prepareConfig($config);
		$this->config = $config;
		$this->mailer->Host = $config['host'];
		$this->mailer->Port = $config['port'];
		if (array_key_exists('auth', $config)) {
			$this->mailer->SMTPAuth = $config['auth'];
		}
		if (array_key_exists('secure', $config)) {
			$this->mailer->SMTPSecure = $config['secure'];
		}
		if (array_key_exists('user', $config)) {
			$this->mailer->Username = $config['user'];
		}
		if (array_key_exists('password', $config)) {
			$this->mailer->Password = $config['password'];
		}
		if (array_key_exists('from', $config)) {
			$this->setFrom($config['from']);
		}
	}

	protected function prepareConfig(&$config)
	{
		if (array_key_exists('host', $config) && !is_string($config['host'])) {
			throw new Exception\Config('Incorrect \$config[\'host\'] format');
		} elseif (!array_key_exists('host', $config)) {
			$config['host'] = 'localhost';
		}
		if (!array_key_exists('port', $config) || !is_numeric($config['port'])) {
			throw new Exception\Config('Incorrect \$config[\'port\'] format');
		}
	}

	public function send($to, $subject, $body)
	{
		$this->mailer->Subject = $subject;
		$this->mailer->Body = $body;
		try {
			$this->addAddress($to);
		} catch (\phpmailerException $e) {
			throw new Exception\PHPMailer($e->getMessage(), $e->getCode(), $e);
		}
		if ($this->mailer->send()) {
			return true;
		} else {
			throw new Exception\Send($this->mailer->ErrorInfo);
		}
	}

	public function __call($name, $args)
	{
		if (in_array($name, ['addAddress', 'setFrom', 'addReplyTo', 'addCC', 'addBCC']) && sizeof($args)) {
			$emails = is_array($args[0]) ? $args[0] : [$args[0]];

			foreach ($emails as $email) {
				list($email, $ename) = $this->parseAddress($email);
				call_user_func_array([$this->mailer, $name], [$email, $ename]);
			}
		} else {
			throw new Exception\Config('Unknown method: ' . $name);
		}
	}

	public function isHTML($isHtml = true)
	{
		$this->mailer->isHTML($isHtml);
	}

	public function addAttachment($path)
	{
		$this->mailer->addAttachment($path);
	}

	protected function parseAddress($address)
	{
		if (is_string($address)) {
			return $this->parseFromStr($address);
		} elseif (is_array($address)) {
			return $this->parseFromArray($address);
		}
	}

	protected function parseFromStr($str)
	{
		$patterns = array(
			'name email' => '/(.*?)\s*\<(.*)\>/',
			'email name' => '/\<(.*)\>\s*(.*?)/',
		);
		if (preg_match($patterns['name email'], $str, $matches) === 1) {
			return array($matches[2], $matches[1]);
		}
		if (preg_match($patterns['email name'], $str, $matches) === 1) {
			return array($matches[1], $matches[2]);
		}
		if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
			return array($str, '');
		}
		return false;
	}

	protected function parseFromArray($array)
	{
		$email = array_values($array);
		if (isset($email[0]) && isset($email[1])) {
			return array($email[0], $email[1]);
		} elseif (isset($email[0])) {
			return array($email[0], '');
		}
	}

}

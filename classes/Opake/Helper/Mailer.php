<?php

namespace Opake\Helper;

use Opake\Helper\Config;
use Opake\Extentions\Mail\SMTP;

class Mailer
{

	public static function send($data, $user, $html = false, $attachment = null)
	{
		$smtp = new \Opake\Extentions\Mail\SMTP();
		$smtp->isHtml($html);
		$smtp->setConfig(Config::get('mail.accounts.default'));
		$smtp->setFrom($user->getFullName() . ' <' . $user->email . '>');

		if (!empty($data->cc)) {
			$smtp->addCC($data->cc);
		}
		if (!empty($data->bcc)) {
			$smtp->addBCC($data->bcc);
		}
		if ($attachment) {
			$smtp->addAttachment($attachment);
		}

		$smtp->send($data->to,
			isset($data->subject) ? $data->subject : '',
			isset($data->body) ? $data->body : ''
		);
	}

	/**
	 * Отправляет письмо пользователю с просьбой создать пароль
	 *
	 * @param \Opake\Model\User $user
	 * @return boolean
	 */
	public function sendPwdEmail($user)
	{
		$to = sprintf("<%s> %s", $user->getEmail(), $user->getFullName());
		$subject = "Opake setup password";
		$template = "Hello %username%\n"
			. "You can setup your password on the following link\n"
			. "%link%\n"
			. "\n"
			. "Have a nice day";
		$link = rtrim(Config::get('app.web'), '/') . '/user/setuppwd/?hash=' . $user->getHash();
		$placeholders = array(
			'%username%' => $user->getFullName(),
			'%link%' => $link
		);
		$body = strtr($template, $placeholders);

		$smtp = new SMTP();
		$smtp->setConfig(Config::get('mail.accounts.default'));
		$smtp->send($to, $subject, $body);
	}

	/**
	 * @param \Opake\Model\Patient $patient
	 * @param string $password
	 * @throws \Opake\Extentions\Mail\Exception\PHPMailer
	 * @throws \Opake\Extentions\Mail\Exception\Send
	 */
	public static function sendPatientPortalLoginEmail($patient, $password)
	{
		$to = sprintf("<%s> %s", $patient->getEmail(), $patient->getFullName());
		$portalName = $patient->organization->portal->title;
		$portalUrl = $patient->organization->portal->getFullUrl();

		$subject = $portalName . " Patient Portal Login information";

		$template = "Here are your credentials to login to the %portal_name% Opake patient portal.\n" .
			"To login: \n\n" .
			"1. Visit the url: %portal_url% \n" .
			"2. Enter the following credentials:\n\n" .
			"Username: %email%\n" .
			"Password: %password%\n\n" .
			"3. Create a new password\n" .
			"4. Login and fill out your information\n\n" .
			"You can login at any time to view details about your upcoming visit, access your documentation, and to fill out additional required information.";

		$placeholders = array(
			'%portal_name%' => $portalName,
			'%portal_url%' => $portalUrl,
			'%email%' => $patient->home_email,
			'%password%' => $password
		);
		$body = strtr($template, $placeholders);

		$smtp = new SMTP();
		$smtp->setConfig(Config::get('mail.accounts.default'));
		$smtp->send($to, $subject, $body);
	}

}

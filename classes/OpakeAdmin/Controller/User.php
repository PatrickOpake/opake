<?php

namespace OpakeAdmin\Controller;

use Opake\Model\Analytics\UserActivity\ActivityRecord;

class User extends AbstractController
{

	public function before()
	{
		$this->view = $this->pixie->view('user/template');
		$this->view->setDefaultJsCss();
	}

	public function after()
	{
		$this->response->body = $this->view->render();
	}

	public function actionRestore()
	{
		if ($this->logged()) {
			throw new \Opake\Exception\PageNotFound();
		}

		if ($this->request->method === 'POST') {

			$email = $this->request->post('email');

			$validate = $this->pixie->validate->get(['email' => $email]);
			$validate->field('email')->rule('filled')->rule('email')->error('Invalid email');

			if ($validate->valid()) {

				$user = $this->orm->get('User')->where('email', $email)->find();

				if ($user->loaded()) {
					// Set unique hash for user
					$user->setHash();
					$user->save();

					// Sending mail
					$mailer = new \Opake\Helper\Mailer();
					$mailer->sendPwdEmail($user);

					$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_SEND_PW_EMAIL)
						->setUserId($user->id())
						->register();


					$this->flash('message', 'Password reset email sent');
				} else {
					$this->view->errors = 'Not found';
				}
			} else {
				foreach ($validate->errors() as $field => $errors) {
					$this->view->errors = implode('; ', $errors);
				}
			}
			$this->view->email = $email;
		}
		$this->view->subview = 'restore';
	}

	public function actionSetuppwd()
	{
		if ($this->logged()) {
			throw new \Opake\Exception\BadRequest('You are currently logged in, please logout and click link again to reset password');
		}

		$hash = $this->request->get('hash');
		if (!$hash) {
			throw new \Opake\Exception\PageNotFound();
		}

		/* @var $user \Opake\Model\User */
		$user = $this->orm->get('user')
			->where('hash', $hash)
			->find();

		if (!$user->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}

		$this->view->hash = $hash;

		if ($this->request->method === 'POST') {

			$password = $this->request->post('password', null, false);
			$passwordConfirm = $this->request->post('password_confirm', null, false);

			$validator = $user->getPasswordValidator($password, $passwordConfirm);

			if ($validator->valid()) {
				$password = $this->request->post('password');
				$service = $this->pixie->services->get('User');
				$service->setNewPassword($user, $password);
				$user->save();

				$service->updateUsedPasswords($user, $password);

				$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_RESET_PW)
					->setForceSave(true)
					->setUserId($user->id())
					->register();

				$this->flash('message', 'Password has been changed');
			} else {

				$errors = $validator->errors();
				$firstField = reset($errors);
				$firstError = reset($firstField);
				$this->view->errors[] = $firstError;
			}
		}
		$this->view->subview = 'setuppwd';
	}

}

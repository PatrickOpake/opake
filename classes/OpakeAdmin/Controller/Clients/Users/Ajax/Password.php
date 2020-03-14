<?php

namespace OpakeAdmin\Controller\Clients\Users\Ajax;

class Password extends \OpakeAdmin\Controller\Ajax
{
	public function actionChangePasswordByUser()
	{
		$user = $this->logged();
		if (!$user) {
			throw new \Opake\Exception\BadRequest('You are currently not logged in');
		}

		if ($this->request->method !== 'POST') {
			throw new \Opake\Exception\InvalidMethod();
		}

		$newPassword = $this->request->post('password', null, false);
		$confirmPassword = $this->request->post('confirm_password', null, false);


		$passwordValidator = $user->getPasswordValidator($newPassword, $confirmPassword);

		if ($passwordValidator->valid()) {

			$service = $this->pixie->services->get('User');
			$service->setNewPassword($user, $newPassword);

			$user->is_temp_password = 0;
			$user->is_scheduled_password_change = 0;
			$user->save();

			$service->updateUsedPasswords($user, $newPassword);

			$this->result = [
				'success' => true
			];

		} else {
			$this->result = [
				'success' => false,
				'error' => $this->getFirstError($passwordValidator)
			];
		}
	}

	protected function getFirstError($validator)
	{
		$errors = $validator->errors();
		if ($errors) {
			$fieldErrors = reset($errors);
			if ($fieldErrors) {
				return reset($fieldErrors);
			}
		}

		return null;
	}
}
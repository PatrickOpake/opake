<?php

namespace Opake\Model\Patient;

use Opake\Helper\StringHelper;
use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;
use OpakePatients\Exception\Authentication;

class User extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'patient_user';
	protected $_row = [
		'id' => null,
		'patient_id' => null,
		'password' => null,
		'new_gen_password' => null,
		'is_tmp_password' => true,
		'hash' => null,
		'active' => null,
		'created' => null,
		'first_login_date' => null,
		'last_login_date' => null,
		'show_insurance_banner' => true
	];

	protected $belongs_to = [
		'patient' => [
			'model' => 'Patient',
			'key' => 'patient_id',
		]
	];

	public function isInternal()
	{
		return false;
	}

	public function setHash($str = '')
	{
		if (!$str) {
			$str = md5(microtime());
		}
		$this->hash = $str;
	}

	public function getPasswordValidator($newPassword, $confirmPassword)
	{
		$validator = $this->pixie->validate->get([
			'new_password' => $newPassword,
			'confirm_password' => $confirmPassword
		]);

		$validator->field('new_password')->rule('filled')->error('Please fill out form');

		$validator->field('new_password')->rule('min_length', 8)->error('Sorry, passwords must follow requirements above. Please enter a new one and try again');
		$validator->field('new_password')->rule('callback', function($password) {

			$uppercase = preg_match('/[A-Z]/', $password);
			$lowercase = preg_match('/[a-z]/', $password);
			$special   = preg_match("/[" . preg_quote('~`!@#$%^&*()_+{}[]:;"\'|?/>.<,-=', '/') . "]/", $password);
			$len = StringHelper::strlen($password);

			return ($uppercase && $lowercase && $special && $len >= \Opake\Model\User::MIN_PASSWORD_LENGTH);

		})->error('Sorry, passwords must follow requirements above. Please enter a new one and try again');

		$validator->field('new_password')->rule('callback', function() use ($confirmPassword) {
			return (bool) $confirmPassword;
		})->error('Please fill out form');
		$validator->field('new_password')->rule('same_as', 'confirm_password')->error('Passwords do not match, please re-enter');

		return $validator;
	}

	public function toArray()
	{
		return [
			'id' => $this->id(),
			'email' => $this->patient->getEmail(),
			'full_name' => $this->patient->getFullName(),
			'first_name' => $this->patient->first_name,
			'last_name' => $this->patient->last_name,
			'patient_id' => $this->patient_id,
			'active' => (bool) $this->active,
			'created' => $this->created ? date('D M d Y H:i:s O', strtotime($this->created)) : null,
			'patient' => $this->patient->toArray(),
			'photo_tiny_src' => $this->patient->getPhoto('tiny'),
			'photo_default_src' => $this->patient->getPhoto('default'),
			'first_login_date' => $this->first_login_date ? date('D M d Y H:i:s O', strtotime($this->first_login_date)) : null,
			'last_login_date' => $this->last_login_date ? date('D M d Y H:i:s O', strtotime($this->last_login_date)) : null,
			'show_insurance_banner' => $this->show_insurance_banner
		];
	}

	public function updateLoginDates()
	{
		$now = TimeFormat::formatToDBDatetime(new \DateTime());
		if (!$this->first_login_date) {
			$this->first_login_date = $now;
		}

		$this->last_login_date = $now;

		$this->save();
	}

	public function setPassword($password)
	{
		$this->password = $this->pixie->auth->provider('password')->hash_password($password);
	}

	public function generateRandomPassword()
	{
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = [];
		$alphaLength = strlen($alphabet) - 1;
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass);
	}

	public function resetInsuranceBanner()
	{
		$this->conn->query('update')->table($this->table)
			->data(['show_insurance_banner' => false])
			->where('id', $this->id)
			->execute();
	}
}

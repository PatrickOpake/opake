<?php

namespace OpakePatients\Exception;

class Authentication extends \Opake\Exception\BadRequest
{

	public static function userNotFound()
	{
		return new self('Sorry this username isn’t recognized. Please check the email with your credentials and try again');
	}

	public static function incorrectPassword()
	{
		return new self('Sorry this password isn’t recognized. Please check the email with your credentials and try again');
	}

	public static function formIsNotFilled()
	{
		return new self('Please fill out form');
	}

	public static function formPasswordsDoNotMatch()
	{
		return new self('Passwords do not match, please re-enter');
	}

	public static function formPasswordsMustFollowRequirements()
	{
		return new self('Sorry, passwords must follow requirements above. Please enter a new one and try again');
	}

}

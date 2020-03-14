<?php

return array(
	'default' => array(
		'model' => 'user', //Name of the users table
		'login' => array(
			'password' => array(
				'login_field' => 'email',
				'password_field' => 'password',
				'repository' => '\Opake\Auth\Repository',
				'login_token_field' => 'remember_me', //Token that the 'remember me' feature uses
				'login_token_lifetime' => 86400 * 14, //Amount in seconds the cookie token is valid
				'not_remember_time' => 1800,
				'is_auth_session_checking_enabled' => true,
				'auth_session_model_name' => 'User_Session',
				'exclude_refresh_for_routes' => [
					'chat/ajax'
				]
			)
		),
		//Role driver configuration
		'roles' => array(
			'driver' => 'field',
			'field' => 'role_id',
			'name_field' => 'name', //Column for the name of the roles
		)
	)
);


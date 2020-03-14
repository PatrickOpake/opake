<?php

return array(
	'default' => array(
		'model' => 'patient_user', //Name of the users table
		'login' => array(
			'password' => array(
				'login_field' => 'patient.home_email',
				'password_field' => 'password',
				'repository' => '\OpakePatients\Auth\Repository',
				'is_auth_session_checking_enabled' => false,
				'auth_session_model_name' => 'Patient_User_Session',
			)
		)
	)
);


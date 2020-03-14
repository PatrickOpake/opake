<?php

use \Console\Migration\BaseMigration;

use \Opake\Model\Analytics\UserActivity\ActivityRecord;

class UserActivityActions extends BaseMigration
{
	public function change()
	{
		$this->query("
            CREATE TABLE `user_activity_action` (
                `id` INT(10) NOT NULL,
                `zone` INT(10) NULL DEFAULT NULL,
                `name` VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

		$this->query("
            CREATE TABLE `user_activity_action_zone` (
                `id` INT(10) NOT NULL,
                `name` VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

		$zones = [
			1 => 'Home',
			2 => 'Schedule',
			3 => 'Intake',
			4 => 'Clinical',
			5 => 'Billing',
			6 => 'Patients',
			7 => 'Inventory',
			8 => 'Settings'
		];

		$actions = [
			ActivityRecord::ACTION_EDIT_PROFILE => [
				'name' => 'Edit Profile',
				'zone' => 1,
			],
			ActivityRecord::ACTION_RESET_PW => [
				'name' => 'Reset Password',
				'zone' => 1,
			],
			ActivityRecord::ACTION_EDIT_PERMISSIONS => [
				'name' => 'Edit Permissions',
				'zone' => 1,
			],
			ActivityRecord::ACTION_SEND_PW_EMAIL => [
				'name' => 'Send Password Email',
				'zone' => 1,
			],
			ActivityRecord::ACTION_EDIT_PREFERENCE_CARDS => [
				'name' => 'Edit Profile',
				'zone' => 1,
			],
			ActivityRecord::ACTION_VIEW_OP_REPORT_TEMPLATES => [
				'name' => 'View Operative Report Template',
				'zone' => 1,
			],
			ActivityRecord::ACTION_EDIT_OP_REPORT_TEMPLATES => [
				'name' => 'Edit Operative Report Template',
				'zone' => 1,
			],
			ActivityRecord::ACTION_EDIT_CALENDAR_SETTINGS => [
				'name' => 'Edit Calendar Settings',
				'zone' => 2,
			],
			ActivityRecord::ACTION_CREATE_CASE => [
				'name' => 'Create Case',
				'zone' => 2,
			],
			ActivityRecord::ACTION_EDIT_CASE => [
				'name' => 'Edit Case',
				'zone' => 2,
			],
			ActivityRecord::ACTION_CREATE_BLOCK => [
				'name' => 'Create Block',
				'zone' => 2,
			],
			ActivityRecord::ACTION_EDIT_BLOCK => [
				'name' => 'Edit Block',
				'zone' => 2,
			],
			ActivityRecord::ACTION_CANCEL_CASE => [
				'name' => 'Cancel Case',
				'zone' => 2,
			],
			ActivityRecord::ACTION_PRINT_SCHEDULE => [
				'name' => 'Print',
				'zone' => 2,
			],
			ActivityRecord::ACTION_INTAKE_EDIT_PATIENT_DETAILS => [
				'name' => 'Edit Patient Details',
				'zone' => 3,
			],
			ActivityRecord::ACTION_INTAKE_EDIT_INSURANCE_INFO => [
				'name' => 'Edit Insurance Info',
				'zone' => 3,
			],
			ActivityRecord::ACTION_INTAKE_EDIT_FORMS => [
				'name' => 'Edit Forms',
				'zone' => 3,
			],
			ActivityRecord::ACTION_CLINICAL_EDIT_PRE_OP => [
				'name' => 'Edit Pre-Op',
				'zone' => 4,
			],
			ActivityRecord::ACTION_CLINICAL_EDIT_OP_REPORT => [
				'name' => 'Edit Operative Report Templates',
				'zone' => 4,
			],
			ActivityRecord::ACTION_CLINICAL_START_CASE => [
				'name' => 'Start Case',
				'zone' => 4,
			],
			ActivityRecord::ACTION_CLINICAL_END_CASE => [
				'name' => 'End Case',
				'zone' => 4,
			],
			ActivityRecord::ACTION_CLINICAL_CONFIRM_AUDIT => [
				'name' => 'Confirm Audit',
				'zone' => 4,
			],
			ActivityRecord::ACTION_CLINICAL_EDIT_POST_OP => [
				'name' => 'Edit Post-Op',
				'zone' => 4,
			],
			ActivityRecord::ACTION_BILLING_EDIT => [
				'name' => 'Edit Billing',
				'zone' => 5,
			],
			ActivityRecord::ACTION_PATIENT_CREATE => [
				'name' => 'Create Patient',
				'zone' => 6,
			],
			ActivityRecord::ACTION_PATIENT_EDIT => [
				'name' => 'Edit Patient Info',
				'zone' => 6,
			],
			ActivityRecord::ACTION_INVENTORY_ADD_ITEM => [
				'name' => 'Add Inventory',
				'zone' => 7,
			],
			ActivityRecord::ACTION_INVENTORY_EDIT_ITEM => [
				'name' => 'Edit Item Details',
				'zone' => 7,
			],
			ActivityRecord::ACTION_INVENTORY_EDIT_QUANTITY_LOCATIONS => [
				'name' => 'Edit Quantity/Locations',
				'zone' => 7,
			],
			ActivityRecord::ACTION_INVENTORY_CREATE_ORDER => [
				'name' => 'Create Order',
				'zone' => 7,
			],
			ActivityRecord::ACTION_INVENTORY_RECEIVE_ORDER => [
				'name' => 'Receive Order',
				'zone' => 7,
			],
			ActivityRecord::ACTION_INVENTORY_MOVE_ITEM => [
				'name' => 'Move Item',
				'zone' => 7,
			],
			ActivityRecord::ACTION_SETTINGS_EDIT_ORGANIZATION => [
				'name' => 'Edit Organization',
				'zone' => 8,
			],
			ActivityRecord::ACTION_SETTINGS_EDIT_USERS => [
				'name' => 'Edit User',
				'zone' => 8,
			],
			ActivityRecord::ACTION_SETTINGS_EDIT_PERMISSIONS => [
				'name' => 'Edit User Permissions',
				'zone' => 8,
			],
			ActivityRecord::ACTION_SETTINGS_CREATE_USER => [
				'name' => 'Create User',
				'zone' => 8,
			],
			ActivityRecord::ACTION_SETTINGS_RESET_PW => [
				'name' => 'Reset User Password',
				'zone' => 8,
			],
			ActivityRecord::ACTION_SETTINGS_SEND_PW_EMAIL => [
				'name' => 'Send User Password Email',
				'zone' => 8,
			],
			ActivityRecord::ACTION_SETTINGS_EDIT_OPERATIVE_REPORT_TEMPLATE => [
				'name' => 'Edit Operative Report Template',
				'zone' => 8,
			],
			ActivityRecord::ACTION_SETTINGS_CREATE_FORM => [
				'name' => 'Create Form',
				'zone' => 8,
			],
			ActivityRecord::ACTION_SETTINGS_EDIT_FORM => [
				'name' => 'Edit Form',
				'zone' => 8,
			],
		];


		foreach ($zones as $id => $name) {
			$this->getDb()->query('insert')
				->table('user_activity_action_zone')
				->data([
					'id' => $id,
					'name' => $name
				])->execute();
		}


		foreach ($actions as $id => $actionData) {
			$this->getDb()->query('insert')
				->table('user_activity_action')
				->data([
					'id' => $id,
					'name' => $actionData['name'],
					'zone' => $actionData['zone']
				])->execute();
		}
	}
}

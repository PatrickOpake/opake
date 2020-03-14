<?php

namespace Opake\Permissions\Organization;

class OrganizationLevel
{
	/**
	 * @var \Opake\Model\Organization
	 */
	protected $organization;

	/**
	 * @var \Opake\Model\Organization\Permission[]
	 */
	protected $permissions;

	/**
	 * @param \Opake\Model\Organization $organization
	 */
	public function __construct($organization = null)
	{
		if ($organization) {
			$this->organization = $organization;
			$this->permissions = $organization->permissions->find_all();
		}
	}

	/**
	 * @return bool
	 */
	public function hasSavedPermissions()
	{
		return (bool)$this->permissions;
	}

	/**
	 * @return array
	 */
	public function getOrganizationPermissions()
	{
		$savedPermissions = [];
		if ($this->hasSavedPermissions()) {
			foreach ($this->permissions as $permission) {
				$savedPermissions[$permission->permission] = (bool)$permission->allowed;
			}
		}

		return array_replace($this->getDefaultPermissions(), $savedPermissions);
	}

	public function getUserPermissions()
	{
		$permissions = $this->getOrganizationPermissions();

		$result = [];

		if (!$permissions['billing']) {
			$result['billing']['view'] = false;
			$result['billing']['index'] = false;
			$result['billing']['view_forms'] = false;
		}

		if (!$permissions['inventory']) {
			$result['databases']['view'] = false;

			$result['inventory']['index'] = false;
			$result['inventory']['create'] = false;
			$result['inventory']['edit'] = false;
			$result['inventory']['move'] = false;
			$result['inventory']['view'] = false;
			$result['inventory']['order'] = false;
			$result['inventory']['receive'] = false;
			$result['inventory']['delete'] = false;

			$result['case_management_clinical']['view_inventory'] = false;
		}

		if (!$permissions['chat']) {
			$result['chat']['messaging'] = false;
			$result['chat']['view_history'] = false;
		}

		if (!$permissions['cm.intake']) {
			$result['case_management_intake']['view'] = false;
		}

		if (!$permissions['cm.pre_op']) {
			$result['case_management_clinical']['view_pre_op'] = false;
		}

		if (!$permissions['cm.operation']) {
			$result['case_management_clinical']['view_op'] = false;
		}

		if (!$permissions['cm.post_op']) {
			$result['case_management_clinical']['view_post_op'] = false;
		}

		if (!$permissions['cm.operative_reports']) {

			$result['operative_reports']['index'] = false;
			$result['operative_reports']['view'] = false;
			$result['operative_reports']['edit'] = false;
			$result['operative_reports']['create'] = false;
			$result['operative_reports']['print'] = false;
			$result['operative_reports']['archive'] = false;

			$result['case_management_clinical']['view_op_report'] = false;
		}

		if (!$permissions['cm.discharge']) {
			$result['case_management_clinical']['view_discharge'] = false;
		}

		if (!$permissions['cm.pre_op'] && !$permissions['cm.operation'] && !$permissions['cm.post_op'] &&
			!$permissions['cm.operative_reports'] && !$permissions['cm.discharge']
		) {

			$result['case_management_clinical']['view'] = false;
		}

		if (!$permissions['cm'] || (!$permissions['cm.pre_op'] && !$permissions['cm.operation'] && !$permissions['cm.post_op'] &&
				!$permissions['cm.operative_reports'] && !$permissions['cm.discharge'])
		) {

			$result['case_management']['view'] = false;
		}

		if (!$permissions['inventory.preference_cards']) {
			$result['card']['index'] = false;
			$result['card']['create'] = false;
			$result['card']['view'] = false;
			$result['card']['edit'] = false;
			$result['card']['assign_to_case'] = false;
		}

		if (!$permissions['cm.dragon_dictation']) {
			$result['operative_reports']['dragon_dictation'] = false;
		}

		return $result;
	}

	/**
	 * @return array
	 */
	public function getDefaultPermissions()
	{
		return [
			'inventory' => true,
			'inventory.preference_cards' => true,
			'chat' => true,
			'billing' => true,
			'cm' => true,
			'cm.intake' => true,
			'cm.pre_op' => true,
			'cm.operation' => true,
			'cm.post_op' => true,
			'cm.operative_reports' => true,
			'cm.dragon_dictation' => true,
			'cm.discharge' => true,
			'patient_portal.login' => true
		];
	}

	/**
	 * @return array
	 */
	public function getPermissionsHierarchy()
	{
		return [
			[
				'name' => 'inventory',
				'label' => 'Inventory',
				'items' => [
					[
						'name' => 'inventory.preference_cards',
						'label' => 'Preference Cards',
						'depends' => ['inventory']
					]
				]
			],
			[
				'name' => 'chat',
				'label' => 'Chat'
			],
			[
				'name' => 'billing',
				'label' => 'Billing'
			],
			[
				'name' => 'cm',
				'label' => 'Case Management',
				'items' => [
					[
						'name' => 'cm.intake',
						'label' => 'Intake',
						'depends' => ['cm']
					],
					[
						'name' => 'cm.pre_op',
						'label' => 'Pre-Op',
						'depends' => ['cm', 'inventory.preference_cards']
					],
					[
						'name' => 'cm.operation',
						'label' => 'Operation',
						'depends' => ['cm']
					],
					[
						'name' => 'cm.post_op',
						'label' => 'Post-Op',
						'depends' => ['cm', 'inventory.preference_cards']
					],
					[
						'name' => 'cm.operative_reports',
						'label' => 'Operative Reports',
						'depends' => ['cm'],
						'items' => [
							[
								'name' => 'cm.dragon_dictation',
								'label' => 'Dragon Dictation',
								'hint' => 'Turning off will remove iPhone dictation for all users with role = surgeon',
								'depends' => ['cm', 'cm.operative_reports']
							]
						],
					],
					[
						'name' => 'cm.discharge',
						'label' => 'Discharge',
						'depends' => ['cm']
					]
				]
			],
			[
				'name' => 'patient_portal.login',
				'label' => 'Patient Portal Login'
			],
		];
	}
}
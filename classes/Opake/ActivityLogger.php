<?php

namespace Opake;

use Opake\ActivityLogger\AbstractAction;
use Opake\ActivityLogger\Action\ArrayAction;
use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\ActionViewer;
use Opake\ActivityLogger\ModelActionQueue;
use Opake\Model\AbstractModel;
use Opake\Model\Analytics\UserActivity\ActivityRecord;

class ActivityLogger
{
	/**
	 * @var array
	 */
	protected $actions = [];

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}


	/**
	 * @param int $type
	 * @return ModelAction|ArrayAction
	 * @throws \Exception
	 */
	public function newAction($type)
	{
		$actionsList = $this->getActionsList();
		if (!isset($actionsList[$type])) {
			throw new \Exception('Unknown activity action');
		}

		$actionData = $actionsList[$type];

		if (isset($actionData['class'])) {
			$className = $actionData['class'];
			$actionObject = new $className($this->pixie, $type);
		} else {
			$actionObject = new ModelAction($this->pixie, $type);
		}

		if (isset($actionData['collectChangesStrategy'])) {
			$actionObject->setCollectChangesStrategy($actionData['collectChangesStrategy']);
		}

		return $actionObject;
	}

	/**
	 * @param \Opake\Model\Analytics\UserActivity\ActivityRecord $activityRecord
	 * @return ActionViewer
	 * @throws \Exception
	 */
	public function newActionViewer($activityRecord)
	{
		$actionsList = $this->getActionsList();
		if (!isset($actionsList[$activityRecord->action])) {
			throw new \Exception('Unknown activity action');
		}

		return new ActionViewer($this->pixie, $activityRecord, $actionsList[$activityRecord->action]);
	}

	/**
	 * @param AbstractModel $model
	 * @return ModelActionQueue
	 */
	public function newModelActionQueue($model)
	{
		$queue = new ModelActionQueue($this->pixie);
		$queue->setNotSavedModel($model);

		return $queue;
	}

	/**
	 * @param int $type
	 * @return string
	 */
	public function getFullActionTitle($type)
	{
		$actionsList = $this->getActionsList();

		if (isset($actionsList[$type])) {
			$actionData = $actionsList[$type];

			$name = '';
			if (isset($actionData['area'])) {
				$name .= $actionData['area'] . ': ';
			}
			$name .= (isset($actionData['title']) ? $actionData['title'] : 'Unknown action');

			return $name;
		}

		return 'Unknown action';
	}

	/**
	 * @return array
	 */
	public function getActionsWithTitles()
	{
		$results = [];

		foreach ($this->getActionsList() as $actionType => $actionData) {

			$name = '';
			if (isset($actionData['area'])) {
				$name .= $actionData['area'] . ': ';
			}
			$name .= (isset($actionData['title']) ? $actionData['title'] : 'Unknown action');

			$results[] = [
				'id' => $actionType,
				'name' => $name
			];
		}

		return $results;
	}

	public function getActionsList()
	{
		if (!$this->actions) {
			$activityConfig = $this->getActivityConfig();
			$labelRows = $this->pixie->db
				->query('select')
				->table('user_activity_action')
				->fields(
					['user_activity_action.id', 'action_id'],
					['user_activity_action.name', 'action_name'],
					['user_activity_action_zone.name', 'action_zone']
				)
				->join('user_activity_action_zone', ['user_activity_action_zone.id', 'user_activity_action.zone'], 'left')
				->execute();

			foreach ($labelRows as $row) {
				$actionId = $row->action_id;
				if (isset($activityConfig[$actionId])) {
					$activityConfig[$actionId]['title'] = $row->action_name;
					$activityConfig[$actionId]['area'] = $row->action_zone;
				}
			}

			$this->actions = $activityConfig;

		}

		return $this->actions;
	}

	protected function getActivityConfig()
	{
		return [

			/* Auth actions */

			ActivityRecord::ACTION_AUTH_LOGIN => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Auth\BlankAction',
			],

			ActivityRecord::ACTION_AUTH_LOGOUT => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Auth\BlankAction',
			],

			/* Home actions */

			ActivityRecord::ACTION_EDIT_PROFILE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Profile\ProfileChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Profile\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Profile\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_RESET_PW => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Profile\ProfilePasswordChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Profile\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Profile\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_EDIT_PERMISSIONS => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Profile\ProfilePermissionsChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Profile\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Profile\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SEND_PW_EMAIL => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Profile\ProfileChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Profile\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Profile\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_VIEW_OP_REPORT_TEMPLATES => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\OperativeReports\OperativeReportAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\OperativeReports\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\OperativeReports\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_EDIT_OP_REPORT_TEMPLATES => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\OperativeReports\OperativeReportAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\OperativeReports\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\OperativeReports\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_ADD_PREFERENCE_CARDS => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\PrefCard\PrefCardChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\PrefCard\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\PrefCard\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_BILLING_EDIT => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Billing\CaseBillingChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Billing\CaseBilling\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Billing\CaseBilling\DetailsFormatter'
				]
			],

			/* Schedule actions */

			ActivityRecord::ACTION_EDIT_CALENDAR_SETTINGS => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Schedule\EditCalendarSettingsAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Schedule\CalendarSettingsFormatter',
				]
			],
			ActivityRecord::ACTION_CREATE_CASE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\CaseItem\CaseChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\CaseItem\ChangesFormatter',
				]
			],
			ActivityRecord::ACTION_EDIT_CASE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\CaseItem\CaseChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\CaseItem\ChangesFormatter',
				]
			],
			ActivityRecord::ACTION_CREATE_BLOCK => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\CaseBlock\CaseBlockChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\CaseBlock\Block\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\CaseBlock\Block\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_EDIT_BLOCK => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\CaseBlock\CaseBlockItemChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\CaseBlock\BlockItem\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\CaseBlock\BlockItem\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_CANCEL_CASE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\CaseItem\CaseCancelAction',
			],
			ActivityRecord::ACTION_PRINT_SCHEDULE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Schedule\PrintScheduleAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Schedule\SchedulePrintFormatter'
				]
			],
		    ActivityRecord::ACTION_RESCHEDULE_CASE => [
			    'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
			    'class' => '\Opake\ActivityLogger\Action\CaseItem\CaseRescheduleAction',
			    'formatter' => [
				    'details' => '\Opake\ActivityLogger\Formatter\CaseItem\Reschedule\DetailsFormatter'
			    ]
		    ],
			ActivityRecord::ACTION_DELETE_CASE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\CaseItem\CaseDeleteAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\CaseItem\Delete\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SEND_POINT_OF_CONTACT_SMS => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\CaseItem\SendPointOfContactSmsAction',
				'formatter' => []
			],
			ActivityRecord::ACTION_CASE_CHECK_IN => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\CaseItem\CaseCheckInAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\CaseItem\CheckIn\DetailsFormatter'
				]
			],

			/* Intake actions */

			/*ActivityRecord::ACTION_INTAKE_CREATE_FORMS => [
				'collectChangesStrategy' => AbstractAction::CHANGES_ADD_ALL,
				'class' => '\Opake\ActivityLogger\Action\Intake\FormChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Intake\Forms\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Intake\Forms\DetailsFormatter'
				]
			],*/
			ActivityRecord::ACTION_INTAKE_EDIT_FORMS => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Intake\FormChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Intake\Forms\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Intake\Forms\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_INTAKE_EDIT_PATIENT_DETAILS => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Intake\PatientChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Intake\Patient\ChangesFormatter',
				]
			],
			ActivityRecord::ACTION_INTAKE_ADD_INSURANCE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_ADD_ALL,
				'class' => '\Opake\ActivityLogger\Action\Intake\InsuranceChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Intake\Patient\Insurance\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Intake\Patient\Insurance\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_INTAKE_EDIT_INSURANCE_INFO => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Intake\InsuranceChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Intake\Patient\Insurance\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Intake\Patient\Insurance\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_INTAKE_REMOVE_INSURANCE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Intake\InsuranceChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Intake\Patient\Insurance\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Intake\Patient\Insurance\DetailsFormatter'
				]
			],

			/* Clinical actions */

			ActivityRecord::ACTION_CLINICAL_ADD_CHECKLIST_ITEM => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Clinical\ChecklistItemChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Clinical\Items\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Clinical\Items\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_CLINICAL_EDIT_CHECKLIST_ITEM => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Clinical\ChecklistItemChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Clinical\Items\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Clinical\Items\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_CLINICAL_REMOVE_CHECKLIST_ITEM => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Clinical\ChecklistItemChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Clinical\Items\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Clinical\Items\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_CLINICAL_ADD_INVENTORY_ITEM => [
				'collectChangesStrategy' => AbstractAction::CHANGES_ADD_ALL,
				'class' => '\Opake\ActivityLogger\Action\Clinical\InventoryItemChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Clinical\Items\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Clinical\Items\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_CLINICAL_EDIT_INVENTORY_ITEM => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Clinical\InventoryItemChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Clinical\Items\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Clinical\Items\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_CLINICAL_REMOVE_INVENTORY_ITEM => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Clinical\InventoryItemChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Clinical\Items\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Clinical\Items\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_CLINICAL_START_CASE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Clinical\CaseStatusAction',
			],
			ActivityRecord::ACTION_CLINICAL_END_CASE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Clinical\CaseStatusAction',
			],
			ActivityRecord::ACTION_CLINICAL_CONFIRM_AUDIT => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Clinical\CaseStatusAction',
			],
			ActivityRecord::ACTION_CLINICAL_EDIT_OP_REPORT => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\OperativeReports\OperativeReportAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\OperativeReports\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\OperativeReports\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_OP_REPORT_SIGN => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\OperativeReports\OperativeReportAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\OperativeReports\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\OperativeReports\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_OP_REPORT_AMENDED => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\OperativeReports\OperativeReportAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\OperativeReports\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\OperativeReports\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_OP_REPORT_SUBMITTED => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\OperativeReports\OperativeReportAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\OperativeReports\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\OperativeReports\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_OP_REPORT_BEGIN => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\OperativeReports\OperativeReportAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\OperativeReports\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\OperativeReports\DetailsFormatter'
				]
			],

			/* Patient actions */

			ActivityRecord::ACTION_PATIENT_CREATE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Patient\PatientChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Patient\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Patient\DetailsFormatter',
				]
			],
			ActivityRecord::ACTION_PATIENT_EDIT => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Patient\PatientChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Patient\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Patient\DetailsFormatter',
				]
			],
			ActivityRecord::ACTION_PATIENT_ADD_INSURANCE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_ADD_ALL,
				'class' => '\Opake\ActivityLogger\Action\Patient\InsuranceChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Patient\Insurance\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Patient\Insurance\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_PATIENT_EDIT_INSURANCE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Patient\InsuranceChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Patient\Insurance\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Patient\Insurance\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_PATIENT_REMOVE_INSURANCE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Patient\InsuranceChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Patient\Insurance\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Patient\Insurance\DetailsFormatter'
				]
			],

			/* Inventory actions */

			ActivityRecord::ACTION_INVENTORY_ADD_ITEM => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Inventory\InventoryItemChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Inventory\Item\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Inventory\Item\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_INVENTORY_EDIT_ITEM => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Inventory\InventoryItemChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Inventory\Item\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Inventory\Item\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_INVENTORY_ADD_QUANTITY_LOCATIONS => [
				'collectChangesStrategy' => AbstractAction::CHANGES_ADD_ALL,
				'class' => '\Opake\ActivityLogger\Action\Inventory\QuantityLocationChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Inventory\QuantityLocation\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Inventory\QuantityLocation\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_INVENTORY_EDIT_QUANTITY_LOCATIONS => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Inventory\QuantityLocationChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Inventory\QuantityLocation\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Inventory\QuantityLocation\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_INVENTORY_REMOVE_QUANTITY_LOCATIONS => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Inventory\QuantityLocationChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Inventory\QuantityLocation\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Inventory\QuantityLocation\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_INVENTORY_CREATE_ORDER => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Inventory\OrderChangeAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Inventory\Order\OutgoingDetailsFormatter'
				]
			],
			ActivityRecord::ACTION_INVENTORY_RECEIVE_ORDER => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Inventory\OrderChangeAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Inventory\Order\ReceivedDetailsFormatter'
				]
			],


			/* Settings actions */

			ActivityRecord::ACTION_SETTINGS_EDIT_USERS => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\User\UserChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Profile\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Profile\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_EDIT_PERMISSIONS => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\User\UserPermissionsChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Profile\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Profile\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_CREATE_USER => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\User\UserChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Profile\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Profile\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_RESET_PW => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\User\UserPasswordChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Profile\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Profile\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_SEND_PW_EMAIL => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\User\UserChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Profile\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Profile\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_ADD_PREFERENCE_CARDS => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\PrefCard\PrefCardChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\PrefCard\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\PrefCard\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_EDIT_PREFERENCE_CARDS => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\PrefCard\PrefCardChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\PrefCard\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\PrefCard\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_EDIT_ORGANIZATION => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Organization\OrganizationChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Organization\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Organization\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_ADD_OPERATIVE_REPORT_TEMPLATE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Settings\OperativeReportTemplates\OperativeReportTemplateChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Settings\OperativeReportTemplates\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Settings\OperativeReportTemplates\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_EDIT_OPERATIVE_REPORT_TEMPLATE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Settings\OperativeReportTemplates\OperativeReportTemplateChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Settings\OperativeReportTemplates\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Settings\OperativeReportTemplates\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_ADD_SITE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Site\SiteChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Site\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Site\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_EDIT_SITE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Site\SiteChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Site\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Site\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_REMOVE_SITE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Site\SiteChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Site\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Site\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_CREATE_FORM => [
				'collectChangesStrategy' => AbstractAction::CHANGES_ADD_ALL,
				'class' => '\Opake\ActivityLogger\Action\Settings\Forms\FormChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Settings\Forms\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Settings\Forms\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_EDIT_FORM => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Settings\Forms\FormChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Settings\Forms\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Settings\Forms\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_SETTINGS_EDIT_SMS_TEMPLATE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
			    'class' => '\Opake\ActivityLogger\Action\Settings\SmsTemplateEditAction',
			    'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Settings\SmsTemplate\ChangesFormatter',
			        'details' => '\Opake\ActivityLogger\Formatter\Settings\SmsTemplate\DetailsFormatter'
			    ]
			],
			ActivityRecord::ACTION_BOOKING_CREATE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Booking\BookingChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Booking\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Booking\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_BOOKING_EDIT => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Booking\BookingChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Booking\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Booking\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_BOOKING_REMOVE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Booking\BookingChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Booking\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Booking\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_BOOKING_FILE_UPLOAD => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Booking\BookingFileChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Booking\File\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Booking\File\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_BOOKING_FILE_RENAME => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Booking\BookingFileChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Booking\File\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Booking\File\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_BOOKING_FILE_REMOVE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Booking\BookingFileChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Booking\File\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Booking\File\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_BOOKING_ADD_NOTE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_ADD_ALL,
				'class' => '\Opake\ActivityLogger\Action\Booking\BookingNoteChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Booking\Note\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Booking\Note\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_BOOKING_PRINT => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Booking\BookingPrintAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Booking\Printing\DetailsFormatter'
				]
			],
		    ActivityRecord::ACTION_BOOKING_SCHEDULE => [
			    'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
			    'class' => '\Opake\ActivityLogger\Action\Booking\BookingScheduleAction',
			    'formatter' => [
				    'details' => '\Opake\ActivityLogger\Formatter\Booking\Schedule\DetailsFormatter'
			    ]
		    ],
			ActivityRecord::ACTION_BOOKING_PATIENT_CREATE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Patient\PatientChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Patient\ChangesFormatter',
				    'details' => '\Opake\ActivityLogger\Formatter\Patient\DetailsFormatter',
				]
			],
		    ActivityRecord::ACTION_CHART_UPLOAD_CHART => [
			    'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
			    'class' => '\Opake\ActivityLogger\Action\Chart\ChartUploadAction',
			    'formatter' => [
				    'details' => '\Opake\ActivityLogger\Formatter\Chart\DetailsFormatter',
				    'changes' => '\Opake\ActivityLogger\Formatter\Chart\ChangesFormatter'
			    ]
		    ],
			ActivityRecord::ACTION_CHART_REUPLOAD_CHART => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Chart\ChartUploadAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Chart\DetailsFormatter',
					'changes' => '\Opake\ActivityLogger\Formatter\Chart\ChangesFormatter',
				]
			],
			ActivityRecord::ACTION_CHART_CREATE_CHART => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Chart\ChartChangeAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Chart\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_CHART_EDIT_CHART => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Chart\ChartChangeAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Chart\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_CHART_RENAME_CHART => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Chart\ChartRenameAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Chart\DetailsFormatter',
					'changes' => '\Opake\ActivityLogger\Formatter\Chart\ChangesFormatter',
				]
			],
			ActivityRecord::ACTION_CHART_ASSIGN_CHART => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Chart\ChartAssignAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Chart\DetailsFormatter',
					'changes' => '\Opake\ActivityLogger\Formatter\Chart\ChangesFormatter',
				]
			],
			ActivityRecord::ACTION_CHART_REMOVE_CHART => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Chart\ChartRemoveAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Chart\DetailsFormatter',
					'changes' => '\Opake\ActivityLogger\Formatter\Chart\ChangesFormatter',
				]
			],
			ActivityRecord::ACTION_CHART_MOVE_CHART => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Chart\ChartMoveAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Chart\DetailsFormatter',
					'changes' => '\Opake\ActivityLogger\Formatter\Chart\ChangesFormatter',
				]
			],
			ActivityRecord::ACTION_CHART_GROUP_CREATE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\ChartGroup\ChartGroupChangeAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\ChartGroup\DetailsFormatter',
					'changes' => '\Opake\ActivityLogger\Formatter\ChartGroup\ChangesFormatter',
				]
			],
			ActivityRecord::ACTION_CHART_GROUP_EDIT => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\ChartGroup\ChartGroupChangeAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\ChartGroup\DetailsFormatter',
					'changes' => '\Opake\ActivityLogger\Formatter\ChartGroup\ChangesFormatter',
				]
			],
			ActivityRecord::ACTION_CHART_GROUP_REMOVE => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\ChartGroup\ChartGroupChangeAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\ChartGroup\DetailsFormatter',
					'changes' => '\Opake\ActivityLogger\Formatter\ChartGroup\ChangesFormatter',
				]
			],
		    ActivityRecord::ACTION_BILLING_CLAIM_PAPER_UB04_SENT => [
			    'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
		        'class' => '\Opake\ActivityLogger\Action\Billing\ClaimSentAction',
		        'formatter' => [
			        'details' => '\Opake\ActivityLogger\Formatter\Billing\Claims\DetailsFormatter',
		        ]
		    ],
			ActivityRecord::ACTION_BILLING_CLAIM_PAPER_1500_SENT => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Billing\ClaimSentAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Billing\Claims\DetailsFormatter',
				]
			],
			ActivityRecord::ACTION_BILLING_CLAIM_ELECTRONIC_UB04_SENT => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Billing\ClaimSentAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Billing\Claims\DetailsFormatter',
				]
			],
			ActivityRecord::ACTION_BILLING_CLAIM_ELECTRONIC_1500_SENT => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Billing\ClaimSentAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Billing\Claims\DetailsFormatter',
				]
			],
			ActivityRecord::ACTION_MASTER_CHARGE_SAVE_EDITED => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Site\MasterChargeChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Site\MasterCharge\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Site\MasterCharge\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_MASTER_CHARGE_UPLOAD => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Site\MasterChargeFileChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Site\MasterCharge\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Site\MasterCharge\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_MASTER_CHARGE_DOWNLOAD => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Site\MasterChargeFileChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Site\MasterCharge\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Site\MasterCharge\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_PAPER_CLAIMS_PRINT => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Billing\PaperClaimAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Billing\PaperClaims\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_BILLING_CLICK_CHECK_ELIGIBILITY => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Billing\EligibilityAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Billing\Eligibility\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_CLINICAL_VERIFICATION_EDIT => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Clinical\VerificationChangeAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Clinical\Verification\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Clinical\Verification\DetailsFormatter'
				]
			],


			ActivityRecord::ACTION_CODING_PAGE_SAVED => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Billing\CodingAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Billing\Coding\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_CODING_PAGE_CLAIM_PRINT => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Billing\CodingAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Billing\Coding\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_CODING_PAGE_CLAIM_PREVIEW => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Billing\CodingAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Billing\Coding\DetailsFormatter'
				]
			],

			ActivityRecord::ACTION_BILLING_NOTES_SAVED => [
				'collectChangesStrategy' => AbstractAction::CHANGES_ADD_ALL,
				'class' => '\Opake\ActivityLogger\Action\Billing\NoteAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Billing\Note\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Billing\Note\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_BILLING_NOTES_EDITED => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Billing\NoteAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Billing\Note\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Billing\Note\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_BILLING_NOTES_DELETED => [
				'collectChangesStrategy' => AbstractAction::CHANGES_ADD_ALL,
				'class' => '\Opake\ActivityLogger\Action\Billing\NoteAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Billing\Note\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Billing\Note\DetailsFormatter'
				]
			],

			ActivityRecord::ACTION_CLINICAL_NOTES_SAVED => [
				'collectChangesStrategy' => AbstractAction::CHANGES_ADD_ALL,
				'class' => '\Opake\ActivityLogger\Action\Clinical\NoteAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Clinical\Note\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Clinical\Note\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_CLINICAL_NOTES_EDITED => [
				'collectChangesStrategy' => AbstractAction::CHANGES_COMPARE,
				'class' => '\Opake\ActivityLogger\Action\Clinical\NoteAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Clinical\Note\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Clinical\Note\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_CLINICAL_NOTES_DELETED => [
				'collectChangesStrategy' => AbstractAction::CHANGES_ADD_ALL,
				'class' => '\Opake\ActivityLogger\Action\Clinical\NoteAction',
				'formatter' => [
					'changes' => '\Opake\ActivityLogger\Formatter\Clinical\Note\ChangesFormatter',
					'details' => '\Opake\ActivityLogger\Formatter\Clinical\Note\DetailsFormatter'
				]
			],

			ActivityRecord::ACTION_BILLING_PATIENT_STATEMENT_GENERATED => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Billing\PatientStatementAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Billing\PatientStatement\DetailsFormatter'
				]
			],

			ActivityRecord::ACTION_BILLING_LEDGER_PAYMENTS_APPLIED => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Billing\PaymentsAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Billing\Payments\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_BILLING_LEDGER_PAYMENTS_EDITED => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Billing\PaymentsAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Billing\Payments\DetailsFormatter'
				]
			],
			ActivityRecord::ACTION_BILLING_LEDGER_PAYMENTS_DELETED => [
				'collectChangesStrategy' => AbstractAction::CHANGES_DONT_STORE,
				'class' => '\Opake\ActivityLogger\Action\Billing\PaymentsDeleteAction',
				'formatter' => [
					'details' => '\Opake\ActivityLogger\Formatter\Billing\Payments\DeleteFormatter'
				]
			],
		];
	}
}
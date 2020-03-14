<?php

namespace Opake\Model\Analytics\UserActivity;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;

class ActivityRecord extends AbstractModel
{

	const ACTION_EDIT_PROFILE = 1;
	const ACTION_RESET_PW = 2;
	const ACTION_EDIT_PERMISSIONS = 3;
	const ACTION_SEND_PW_EMAIL = 4;
	const ACTION_ADD_PREFERENCE_CARDS = 55;
	const ACTION_EDIT_PREFERENCE_CARDS = 5;
	const ACTION_VIEW_OP_REPORT_TEMPLATES = 6;
	const ACTION_EDIT_OP_REPORT_TEMPLATES = 7;

	const ACTION_EDIT_CALENDAR_SETTINGS = 8;
	const ACTION_CREATE_CASE = 9;
	const ACTION_EDIT_CASE = 10;
	const ACTION_CREATE_BLOCK = 11;
	const ACTION_EDIT_BLOCK = 12;
	const ACTION_CANCEL_CASE = 13;
	const ACTION_PRINT_SCHEDULE = 14;
	const ACTION_RESCHEDULE_CASE = 85;
	const ACTION_DELETE_CASE = 86;
	const ACTION_SEND_POINT_OF_CONTACT_SMS = 89;
	const ACTION_CASE_CHECK_IN = 90;

	const ACTION_INTAKE_EDIT_PATIENT_DETAILS = 15;
	const ACTION_INTAKE_EDIT_INSURANCE_INFO = 16;
	const ACTION_INTAKE_EDIT_FORMS = 17;
	const ACTION_INTAKE_CREATE_FORMS = 43;
	const ACTION_INTAKE_ADD_INSURANCE = 44;
	const ACTION_INTAKE_REMOVE_INSURANCE = 45;

	const ACTION_CLINICAL_EDIT_PRE_OP = 18;
	const ACTION_CLINICAL_EDIT_OP_REPORT = 19;
	const ACTION_CLINICAL_START_CASE = 20;
	const ACTION_CLINICAL_END_CASE = 21;
	const ACTION_CLINICAL_CONFIRM_AUDIT = 22;
	const ACTION_CLINICAL_EDIT_POST_OP = 23;
	const ACTION_CLINICAL_ADD_CHECKLIST_ITEM = 48;
	const ACTION_CLINICAL_EDIT_CHECKLIST_ITEM = 49;
	const ACTION_CLINICAL_REMOVE_CHECKLIST_ITEM = 50;
	const ACTION_CLINICAL_ADD_INVENTORY_ITEM = 51;
	const ACTION_CLINICAL_EDIT_INVENTORY_ITEM = 52;
	const ACTION_CLINICAL_REMOVE_INVENTORY_ITEM = 53;

	const ACTION_BILLING_EDIT = 24;

	const ACTION_PATIENT_CREATE = 25;
	const ACTION_PATIENT_EDIT = 26;
	const ACTION_PATIENT_EDIT_INSURANCE = 42;
	const ACTION_PATIENT_ADD_INSURANCE = 46;
	const ACTION_PATIENT_REMOVE_INSURANCE = 47;

	const ACTION_INVENTORY_ADD_ITEM = 27;
	const ACTION_INVENTORY_EDIT_ITEM = 28;
	const ACTION_INVENTORY_ADD_QUANTITY_LOCATIONS = 57;
	const ACTION_INVENTORY_EDIT_QUANTITY_LOCATIONS = 29;
	const ACTION_INVENTORY_REMOVE_QUANTITY_LOCATIONS = 58;
	const ACTION_INVENTORY_CREATE_ORDER = 30;
	const ACTION_INVENTORY_RECEIVE_ORDER = 31;
	const ACTION_INVENTORY_MOVE_ITEM = 32;

	const ACTION_SETTINGS_EDIT_ORGANIZATION = 33;
	const ACTION_SETTINGS_EDIT_USERS = 34;
	const ACTION_SETTINGS_EDIT_PERMISSIONS = 35;
	const ACTION_SETTINGS_CREATE_USER = 36;
	const ACTION_SETTINGS_RESET_PW = 37;
	const ACTION_SETTINGS_SEND_PW_EMAIL = 38;
	const ACTION_SETTINGS_ADD_OPERATIVE_REPORT_TEMPLATE = 59;
	const ACTION_SETTINGS_EDIT_OPERATIVE_REPORT_TEMPLATE = 39;
	const ACTION_SETTINGS_CREATE_FORM = 40;
	const ACTION_SETTINGS_EDIT_FORM = 41;
	const ACTION_SETTINGS_EDIT_PREFERENCE_CARDS = 54;
	const ACTION_SETTINGS_ADD_PREFERENCE_CARDS = 56;
	const ACTION_SETTINGS_ADD_SITE = 60;
	const ACTION_SETTINGS_EDIT_SITE = 61;
	const ACTION_SETTINGS_REMOVE_SITE = 62;
	const ACTION_SETTINGS_EDIT_SMS_TEMPLATE = 88;

	const ACTION_AUTH_LOGIN = 63;
	const ACTION_AUTH_LOGOUT = 64;

	const ACTION_BOOKING_CREATE = 65;
	const ACTION_BOOKING_EDIT = 66;
	const ACTION_BOOKING_REMOVE = 67;
	const ACTION_BOOKING_FILE_UPLOAD = 68;
	const ACTION_BOOKING_FILE_RENAME = 69;
	const ACTION_BOOKING_FILE_REMOVE = 70;
	const ACTION_BOOKING_ADD_NOTE = 71;
	const ACTION_BOOKING_PRINT = 72;
	const ACTION_BOOKING_SCHEDULE = 73;
	const ACTION_BOOKING_PATIENT_CREATE = 87;

	const ACTION_CHART_CREATE_CHART = 74;
	const ACTION_CHART_UPLOAD_CHART = 75;
	const ACTION_CHART_REUPLOAD_CHART = 76;
	const ACTION_CHART_EDIT_CHART = 77;
	const ACTION_CHART_RENAME_CHART = 78;
	const ACTION_CHART_ASSIGN_CHART = 79;
	const ACTION_CHART_REMOVE_CHART = 80;
	const ACTION_CHART_MOVE_CHART = 81;

	const ACTION_CHART_GROUP_CREATE = 82;
	const ACTION_CHART_GROUP_EDIT = 83;
	const ACTION_CHART_GROUP_REMOVE = 84;

	const ACTION_OP_REPORT_SIGN = 91;
	const ACTION_OP_REPORT_AMENDED = 92;
	const ACTION_OP_REPORT_SUBMITTED = 93;
	const ACTION_OP_REPORT_BEGIN = 94;

	const ACTION_BILLING_CLAIM_PAPER_UB04_SENT = 95;
	const ACTION_BILLING_CLAIM_PAPER_1500_SENT = 96;
	const ACTION_BILLING_CLAIM_ELECTRONIC_UB04_SENT = 97;
	const ACTION_BILLING_CLAIM_ELECTRONIC_1500_SENT = 98;

	const ACTION_MASTER_CHARGE_DOWNLOAD = 99;
	const ACTION_MASTER_CHARGE_UPLOAD = 100;
	const ACTION_MASTER_CHARGE_SAVE_EDITED = 101;
	const ACTION_PAPER_CLAIMS_PRINT = 102;

	const ACTION_BILLING_CLICK_CHECK_ELIGIBILITY = 103;

	const ACTION_BILLING_LEDGER_PAYMENTS_APPLIED = 104;
	const ACTION_BILLING_LEDGER_PAYMENTS_EDITED = 105;

	const ACTION_BILLING_PATIENT_STATEMENT_GENERATED = 106;

	const ACTION_BILLING_NOTES_SAVED = 107;
	const ACTION_BILLING_NOTES_EDITED = 108;
	const ACTION_BILLING_NOTES_DELETED = 109;

	const ACTION_CLINICAL_NOTES_SAVED = 110;
	const ACTION_CLINICAL_NOTES_EDITED = 111;
	const ACTION_CLINICAL_NOTES_DELETED = 112;

	const ACTION_CODING_PAGE_SAVED = 113;
	const ACTION_CODING_PAGE_CLAIM_PRINT = 114;
	const ACTION_CODING_PAGE_CLAIM_PREVIEW = 115;

	const ACTION_CLINICAL_VERIFICATION_EDIT = 116;

	const ACTION_BILLING_LEDGER_PAYMENTS_DELETED = 117;


	public $id_field = 'id';
	public $table = 'user_activity';
	protected $_row = [
		'id' => null,
		'user_id' => null,
		'date' => null,
		'action' => null,
		'details' => null,
		'changes' => null
	];

	protected $belongs_to = [
		'user' => [
			'model' => 'User',
			'key' => 'user_id',
		],
	];

	protected $formatters = [
		'ActivityList' => [
			'class' => '\Opake\Formatter\ActivityLogger\ActivityListFormatter'
		]
	];

	public function getActionTitle()
	{
		return $this->pixie->activityLogger->getFullActionTitle($this->action);
	}

	public function getChangesArray()
	{
		if ($this->changes) {
			return unserialize($this->changes);
		}

		return null;
	}

	public function getDetailsArray()
	{
		if ($this->details) {
			return unserialize($this->details);
		}

		return null;
	}

	public function toArray()
	{
		$actionDate = TimeFormat::fromDBDatetime($this->date);
		$actionViewer = $this->pixie->activityLogger->newActionViewer($this);
		$formattedChanges = $actionViewer->formatChanges();
		$formattedDetails = $actionViewer->formatDetails();

		$changes = [];
		$details = [];

		if ($formattedChanges) {
			foreach ($formattedChanges as $label => $value) {
				$changes[] = [
					'label' => $label,
					'value' => $value
				];
			}
		}

		if ($formattedDetails) {
			foreach ($formattedDetails as $label => $value) {
				$details[] = [
					'label' => $label,
					'value' => $value
				];
			}
		}

		return [
			'id' => $this->id,
			'user_id' => $this->user_id,
			'user' => ($this->user && $this->user->loaded()) ? $this->user->toArray() : null,
			'user_org' => ($this->user && $this->user->loaded() && $this->user->organization && $this->user->organization->loaded()) ?
				$this->user->organization->toArray() : null,
			'date' => TimeFormat::getDate($actionDate),
			'time' => TimeFormat::getTimeWithSeconds($actionDate),
			'action' => $this->getActionTitle(),
			'changes' => $changes,
			'details' => $details,
		];
	}

}
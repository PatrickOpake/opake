<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;
use OpakeAdmin\Helper\Chart\DynamicFieldsHelper;

class OperativeReport extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'case_op_report';
	protected $_row = [
		'id' => null,
		'case_id' => null,
		'type' => self::TYPE_SURGEON,
		'surgeon_id' => null,
		'time_start' => null,
		'time_submitted' => null,
		'time_signed' => null,
		'signed_user_id' => null,
		'procedure_id' => null,
		'operation_time' => '',
		'specimens_removed' => '',
		'anesthesia_administered' => '',
		'ebl' => '',
		'blood_transfused' => '',
		'fluids' => '',
		'drains' => '',
		'urine_output' => '',
		'total_tourniquet_time' => '',
		'consent' => '',
		'complications' => '',
		'clinical_history' => '',
		'approach' => '',
		'findings' => '',
		'description_procedure' => '',
		'follow_up_care' => '',
		'conditions_for_discharge' => '',
		'scribe' => '',
		'applied_template_id' => null,
		'status' => null,
		'notes_count' => 0,
		'is_archived' => 0,
		'is_active' => 0,
		'is_exist_template' => 0
	];
	protected $belongs_to = [
		'case' => [
			'model' => 'Cases_Item',
			'key' => 'case_id'
		],
		'procedure' => [
			'model' => 'Cases_Type',
			'key' => 'procedure_id'
		],
		'applied_template' => [
			'model' => 'Cases_OperativeReport_Future',
			'key' => 'applied_template_id'
		],
		'signed_user' => [
			'model' => 'User',
			'key' => 'signed_user_id'
		]
	];

	protected $has_many = [
		'pre_op_diagnosis' => [
			'model' => 'ICD',
			'through' => 'case_op_report_pre_op_diagnosis',
			'key' => 'report_id',
			'foreign_key' => 'diagnosis_id'
		],
		'post_op_diagnosis' => [
			'model' => 'ICD',
			'through' => 'case_op_report_post_op_diagnosis',
			'key' => 'report_id',
			'foreign_key' => 'diagnosis_id'
		],
		'amendments' => [
			'model' => 'Cases_OperativeReport_Amendment',
			'key' => 'report_id',
			'cascade_delete' => true
		],
		'notes' => [
			'model' => 'Cases_OperativeReport_Note',
			'key' => 'report_id',
			'cascade_delete' => true
		],
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	protected $formatters = [
		'PatientCharts' => [
			'class' => '\Opake\Formatter\Cases\OperativeReport\PatientChartsFormatter'
		]
	];

	const STATUS_OPEN = 1;
	const STATUS_DRAFT = 2;
	const STATUS_SUBMITTED = 3;
	const STATUS_SIGNED = 4;

	const TYPE_SURGEON = 'surgeon';
	const TYPE_ANESTHESIOLOGIST = 'anesthesiologist';
	const TYPE_CO_SURGEON = 'co_surgeon';
	const TYPE_SUPERVISING_SURGEON = 'supervising_surgeon';
	const TYPE_FIRST_ASSISTANT_SURGEON = 'first_assistant_surgeon';
	const TYPE_ASSISTANT = 'assistant';
	const TYPE_DICTATED_BY = 'dictated_by';
	const TYPE_OTHER_STAFF = 'other_staff';
	const TYPE_NON_SURGEON = 'non_surgeon';

	public static function 	getTypeSurgeons()
	{
		$result = [
			self::TYPE_SURGEON,
			self::TYPE_ANESTHESIOLOGIST,
			self::TYPE_CO_SURGEON,
			self::TYPE_SUPERVISING_SURGEON,
			self::TYPE_FIRST_ASSISTANT_SURGEON,
			self::TYPE_ASSISTANT,
			self::TYPE_DICTATED_BY,
			self::TYPE_OTHER_STAFF,
		];

		return $result;
	}

	public static function isNonSurgeonReport($user)
	{
		return $user->is_enabled_op_report && !$user->isDoctor();
	}

	/**
	 * @return Item
	 */
	public function getCase()
	{
		return $this->case;
	}

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		/* Validators for max characters length */
		$validator->field('anesthesia_administered')->rule('max_words_html', 250)->error('The Anesthesia Administered must be less than or equal to 250 words');
		$validator->field('ebl')->rule('max_words_html', 250)->error('The EBL must be less than or equal to 250 words');
		$validator->field('blood_transfused')->rule('max_words_html', 250)->error('The Blood Transfused must be less than or equal to 250 words');
		$validator->field('fluids')->rule('max_words_html', 250)->error('The Fluids must be less than or equal to 250 words');
		$validator->field('drains')->rule('max_words_html', 250)->error('The Drains must be less than or equal to 250 words');
		$validator->field('urine_output')->rule('max_words_html', 250)->error('The Urine Output must be less than or equal to 250 words');
		$validator->field('total_tourniquet_time')->rule('max_words_html', 250)->error('The Total Tourniquet Time must be less than or equal to 250 words');
		$validator->field('specimens_removed')->rule('max_words_html', 10000)->error('The Specimens Removed must be less than or equal to 10000 words');
		$validator->field('consent')->rule('max_words_html', 10000)->error('The Consent must be less than or equal to 10000 words');
		$validator->field('complications')->rule('max_words_html', 10000)->error('The Complications must be less than or equal to 10000 words');
		$validator->field('clinical_history')->rule('max_words_html', 10000)->error('The Clinical History & Indications for Procedure must be less than or equal to 10000 words');
		$validator->field('approach')->rule('max_words_html', 10000)->error('The Approach must be less than or equal to 10000 words');
		$validator->field('findings')->rule('max_words_html', 10000)->error('The Findings must be less than or equal to 10000 words');
		$validator->field('description_procedure')->rule('max_words_html', 10000)->error('The Description of Procedure must be less than or equal to 10000 words');
		$validator->field('follow_up_care')->rule('max_words_html', 10000)->error('The Follow Up Care must be less than or equal to 10000 words');
		$validator->field('conditions_for_discharge')->rule('max_words_html', 10000)->error('The Conditions for Discharge must be less than or equal to 10000 words');
		$validator->field('scribe')->rule('max_words_html', 10000)->error('The Scribe must be less than or equal to 10000 words');

		return $validator;
	}

	public function save()
	{
		if (!$this->time_start && ($this->status != self::STATUS_OPEN)) {
			$this->time_start = strftime('%Y-%m-%d %H:%M:%S');
		}
		if($this->status == self::STATUS_SUBMITTED) {
			$this->time_submitted = strftime('%Y-%m-%d %H:%M:%S');
		}

		parent::save();
	}

	public function updateNotesCount()
	{
		$this->conn->query('update')->table($this->table)
			->data(['notes_count' => $this->pixie->db->expr('notes_count + 1')])
			->where('id', $this->id)
			->execute();
	}

	public function reduceNotesCount()
	{
		$this->conn->query('update')->table($this->table)
			->data(['notes_count' => $this->pixie->db->expr('notes_count - 1')])
			->where('id', $this->id)
			->execute();
	}

	public function fromArray($data)
	{
		if (isset($data->procedure) && $data->procedure->id) {
			$data->procedure_id = $data->procedure->id;
		}
		if (isset($data->pre_op_diagnosis) && $data->pre_op_diagnosis) {
			$pre_op_diagnosis = [];
			foreach ($data->pre_op_diagnosis as $diagnosis) {
				$pre_op_diagnosis[] = $diagnosis->id;
			}
			$data->pre_op_diagnosis = $pre_op_diagnosis;
		}
		if (isset($data->post_op_diagnosis) && $data->post_op_diagnosis) {
			$post_op_diagnosis = [];
			foreach ($data->post_op_diagnosis as $diagnosis) {
				$post_op_diagnosis[] = $diagnosis->id;
			}
			$data->post_op_diagnosis = $post_op_diagnosis;
		}
		if (isset($data->applied_template) && $data->applied_template) {
			$data->applied_template_id = $data->applied_template->id;
		}

		unset($data->time_start);
		unset($data->time_submitted);
		unset($data->notes_count);
		return $data;
	}

	public function updateDynamicVariables($caseItem)
	{
		$helper = new DynamicFieldsHelper($caseItem);
		foreach ($this->getFieldsForDynamicVariablesReplace() as $fieldName) {
			$this->{$fieldName} = $helper->replaceDynamicFields($this->{$fieldName});
		}
	}

	public function getNotes()
	{
		return $this->notes->find_all()->as_array();
	}

	public function hasUnreadNotesForUser($userId)
	{
		$query = $this->pixie->db->query('select')
			->table('user_operative_report_note')
			->fields('last_read_note_id')
			->where([['user_id', $userId], ['report_id', $this->id]])
			->execute()
			->current();

		if (!$query) {
			$this->pixie->db->query('insert')
				->table('user_operative_report_note')
				->data(['user_id' => $userId, 'report_id' => $this->id])
				->execute();

			return true;
		} else {
			$lastReadNoteId = $query->last_read_note_id;
			$reportNotes = $this->notes->find_all()->as_array();

			if (count($reportNotes) && ($lastReadNoteId < $reportNotes[count($reportNotes) - 1]->id)) {
				return true;
			} else {
				return false;
			}
		}
	}

	public function readNotes($userId)
	{
		$reportNote = $this->notes->order_by('id', 'DESC')->limit(1)->find();
		$lastReportNoteId = $reportNote->id;

		$this->pixie->db->query('update')
			->table('user_operative_report_note')
			->data(['last_read_note_id' => $lastReportNoteId])
			->where([['user_id', $userId], ['report_id', $this->id]])
			->execute();
	}

	public function toArray()
	{
		$data = parent::toArray();
		$data['case'] = $this->getCase()->toOpReportArray();

		$data['pre_op_diagnosis'] = [];
		foreach ($this->pre_op_diagnosis->find_all() as $diagnosis) {
			$data['pre_op_diagnosis'][] = $diagnosis->toArray();
		}
		if (!$data['pre_op_diagnosis']) {
			foreach ($this->getCase()->registration->admitting_diagnosis->find_all() as $diagnosis) {
				$data['pre_op_diagnosis'][] = $diagnosis->toArray();
			}
		}
		if (!$this->procedure_id) {
			$data['procedure'] = $this->getCase()->type->toArray();
		}
		foreach ($this->post_op_diagnosis->find_all() as $diagnosis) {
			$data['post_op_diagnosis'][] = $diagnosis->toArray();
		}

		$amendments = [];
		foreach ($this->amendments->find_all() as $item) {
			$amendments[] = $item->toArray();
		}
		$data['amendments'] = $amendments;


		return $data;
	}

	public function toShortArray()
	{
		$case = $this->getCase();

		$users = [];
		foreach ($case->getUsers() as $user) {
			$users[] = $user->toArray();
		}

		$other_staff = [];
		foreach ($case->other_staff->find_all() as $user) {
			$other_staff[] = $user->toArray();
		}

		$registration = $this->case->registration;
		return [
			'id' => (int) $this->id,
			'case_id' => $case->id(),
			'case' => [
				'time_start' => date('D M d Y H:i:s O', strtotime($case->time_start)),
				'type_full_name' => $case->type->getFullName(),
				'users' => $users,
				'other_staff' => $other_staff
			],
			'patient' => [
				'age' => $registration->getAge(),
				'sex' => $registration->getGender(),
				'first_name' => $registration->first_name,
				'last_name' => $registration->last_name,
				'mrn' => $registration->patient->getFullMrn(),
			],
			'time_start' => $this->time_start ?  date('D M d Y H:i:s O', strtotime($this->time_start)) : null,
			'time_submitted' => $this->time_submitted ? date('D M d Y H:i:s O', strtotime($this->time_submitted)) : null,
			'time_signed' => $this->time_signed ? date('D M d Y H:i:s O', strtotime($this->time_signed)) : null,
			'status' => $this->status,
			'surgeon_id' => $this->surgeon_id,
			'notes_count' => (int)$this->notes_count,
		];
	}

	protected function getFieldsForDynamicVariablesReplace()
	{
		return [
			'anesthesia_administered',
		    'drains',
		    'consent',
		    'complications',
		    'approach',
		    'description_procedure',
		    'follow_up_care',
		    'conditions_for_discharge',
		    'scribe',
		    'clinical_history',
		    'total_tourniquet_time',
		    'ebl',
		    'blood_transfused',
		    'fluids',
		    'urine_output',
		    'findings',
		    'specimens_removed'
		];
	}
}

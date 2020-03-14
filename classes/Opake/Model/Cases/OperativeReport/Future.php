<?php

namespace Opake\Model\Cases\OperativeReport;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;
use Opake\Model\Cases\OperativeReport;

class Future extends AbstractModel {

	public $id_field = 'id';
	public $table = 'case_op_report_future';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'name' => '',
		'cpt_id' => '',
		'anesthesia_administered' => '',
		'ebl' => '',
		'drains' => '',
		'consent' => '',
		'complications' => '',
		'approach' => '',
		'description_procedure' => '',
		'follow_up_care' => '',
		'conditions_for_discharge' => '',
		'scribe' => '',
		'specimens_removed' => '',
		'findings' => '',
		'urine_output' => '',
		'fluids' => '',
		'blood_transfused' => '',
		'total_tourniquet_time' => '',
		'clinical_history' => '',
		'updated' => null,
	];

	protected $belongs_to = [
		'case_type' => [
			'model' => 'Cases_Type',
			'key' => 'cpt_id'
		]
	];

	protected $has_many = [
		'surgeons' => [
			'model' => 'User',
			'through' => 'case_op_report_future_user',
			'key' => 'report_id',
			'foreign_key' => 'user_id'
		]
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	public function fromArray($data)
	{
		if (isset($data->surgeons) && $data->surgeons) {
			$users = [];
			foreach ($data->surgeons as $user) {
				$users[] = $user->id;
			}
			$data->surgeons = $users;
		}

		return $data;
	}

	public function fromOpReport(\Opake\Model\Cases\OperativeReport $report) {
		$this->cpt_id = $report->case->type_id;
		$this->organization_id = $report->case->organization_id;
		$this->anesthesia_administered = $report->anesthesia_administered;
		$this->ebl = $report->ebl;
		$this->drains = $report->drains;
		$this->consent = $report->consent;
		$this->complications = $report->complications;
		$this->approach = $report->approach;
		$this->description_procedure = $report->description_procedure;
		$this->follow_up_care = $report->follow_up_care;
		$this->conditions_for_discharge = $report->conditions_for_discharge;
		$this->scribe = $report->scribe;
		$this->specimens_removed = $report->specimens_removed;
		$this->findings = $report->findings;
		$this->urine_output = $report->urine_output;
		$this->fluids = $report->fluids;
		$this->blood_transfused = $report->blood_transfused;
		$this->total_tourniquet_time = $report->total_tourniquet_time;
		$this->clinical_history = $report->clinical_history;
	}

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('name')->rule('filled')->error('You must specify template name');
		$validator->field('name')->rule('callback', function ($val, $validator, $field){
			$surgeons = [];
			$currentSurgeons = $validator->get('surgeons');
			if($currentSurgeons) {
				foreach($currentSurgeons as $surgeon) {
					$surgeons[] = $surgeon->id();
				}
			}
			$model = $this->pixie->orm->get('Cases_OperativeReport_Future');
			if(!$surgeons) {
				return true;
			}
			$model->query->join(['case_op_report_future_user', 'u'], [$model->table . '.id', 'u.report_id']);
			$model->where('u.user_id', 'IN', $this->pixie->db->arr($surgeons))
				->where('name', $val);
			if ($this->id) {
				$model->where($this->table . '.id', '!=', $this->id);
			}
			return !$model->count_all();
		})->error('Template name must be unique');

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

	/**
	 * Определяет принадлежность модели
	 * @param \Opake\Model\User $user
	 * @return boolean
	 */
	public function isSelf($user) {

		foreach($this->surgeons->find_all() as $surgeon) {
			if ($user->id() == $surgeon->id()) {
				return true;
			}
		}

		return false;
	}

	public function toShortArray() {
		$surgeons = [];
		foreach($this->surgeons->find_all() as $surgeon) {
			$surgeons[] = $surgeon->toArray();
		}
		$data = [
			'id' => $this->id,
			'name' => $this->name,
			'case_type' => $this->case_type->toArray(),
			'updated' => date('D M d Y H:i:s O', strtotime($this->updated)),
			'surgeons' => $surgeons
		];
		return $data;
	}

	public function save()
	{
		$this->updated = TimeFormat::formatToDBDatetime(new \DateTime());

		parent::save();
	}
}

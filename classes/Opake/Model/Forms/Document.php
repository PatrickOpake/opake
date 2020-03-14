<?php

namespace Opake\Model\Forms;

use Opake\Model\AbstractModel;

/**
 * Class Document
 *
 * @property-read \Opake\Model\UploadedFile $file
 *
 * @package Opake\Model\Forms
 */
class Document extends AbstractModel
{

	const SEGMENT_INTAKE = 'intake';
	const SEGMENT_CLINICAL = 'clinical';
	const SEGMENT_BILLING = 'billing';

	const TYPE_ASSIGNMENT_BENEFITS = 'assignment_of_benefits';
	const TYPE_ADVANCED_BENEFICIARY = 'advanced_beneficiary_notice';
	const TYPE_CONSENT_TREATMENT = 'consent_for_treatment';
	const TYPE_SMOKING_STATUS = 'smoking_status';
	const TYPE_MEDICAL_HISTORY = 'medical_history';
	const TYPE_HIPAA = 'hipaa';
	const TYPE_OTHER = 'other';

	public $id_field = 'id';
	public $table = 'forms_document';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'uploaded_file_id' => null,
		'remote_file_id' => null,
		'segment' => '',
		'type' => '',
		'name' => '',
		'own_text' => null,
		'include_header' => null,
		'is_landscape' => false,
		'doc_type_id' => null,
		'is_all_sites' => true,
		'is_all_case_types' => false
	];

	protected $belongs_to = [
		'file' => [
			'model' => 'UploadedFile',
			'key' => 'uploaded_file_id',
			'cascade_delete' => true
		],
		'remote_file' => [
			'model' => 'RemoteStorageDocument',
			'key' => 'remote_file_id',
			'cascade_delete' => true
		],
		'doc_type' => [
			'model' => 'Cases_Registration_Document_Type',
			'key' => 'doc_type_id',
			'cascade_delete' => false
		],
		'organization' => [
			'model' => 'Organization',
			'key' => 'organization_id'
		]
	];

	protected $has_many = [
		'sites' => [
			'model' => 'Site',
			'through' => 'forms_document_site',
			'key' => 'doc_id',
			'foreign_key' => 'site_id'
		],
		'case_types' => [
			'model' => 'Cases_Type',
			'through' => 'forms_document_case_type',
			'key' => 'doc_id',
			'foreign_key' => 'case_type_id'
		],
		'dynamic_fields' => [
			'model' => 'Forms_PDF_DynamicField',
			'key' => 'doc_id',
			'cascade_delete' => true
		]
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	/**
	 * @var array
	 */
	protected $formatters = [
		'SettingsList' => [
			'class' => '\Opake\Formatter\Chart\SettingsListFormatter'
		],
		'UploadedForm' => [
			'class' => '\Opake\Formatter\Chart\UploadedFormFormatter'
		]
	];

	/**
	 * @param \Opake\Model\Cases\Item $caseItem
	 * @return \PHPixie\ORM\Result
	 */
	public function getFormsForCase($caseItem)
	{
		$query = $this->query;
		$query->fields($this->pixie->db->expr('DISTINCT forms_document.*'));
		$query->join('forms_document_case_type', ['forms_document_case_type.doc_id', 'forms_document.id']);
		$query->join('forms_document_site', ['forms_document_site.doc_id', 'forms_document.id']);
		$query->where('segment', 'intake');
		$query->where('organization_id', $caseItem->organization_id);
		$query->where(['is_all_sites', 1],
			['or', ['forms_document_site.site_id', $caseItem->location->site_id]]);
		$query->where(['is_all_case_types', 1],
			['or', ['forms_document_case_type.case_type_id', $caseItem->type_id]]);

		$forms = $this->find_all();

		return $forms;
	}

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('name')->rule('filled')->error('You must specify form name')->condition('uploaded_file_id')->rule('!filled');
		return $validator;
	}

	public function fromArray($data)
	{
		if (isset($data->sites) && $data->sites) {
			$sites = [];
			foreach ($data->sites as $site) {
				$sites[] = $site->id;
			}
			$data->sites = $sites;
		}

		if (isset($data->case_types) && $data->case_types) {
			$case_types = [];
			foreach ($data->case_types as $type) {
				$case_types[] = $type->id;
			}
			$data->case_types = $case_types;
		}

		return $data;
	}

	public function isAllowedCase($caseItem)
	{
		if ($this->is_all_sites) {
			return true;
		}

		$locationId = (int) $caseItem->location_id;

		$rows = $this->pixie->db->query('select')
			->table('forms_document_site')
			->fields('location.id')
			->join('location', ['location.site_id', 'forms_document_site.site_id'], 'inner')
			->where('doc_id', $this->id())
			->execute();

		$locationIds = [];
		foreach ($rows as $row) {
			$locationIds[] = (int) $row->id;
		}

		return in_array($locationId, $locationIds);
	}

	public function getChartGroups()
	{
		if (!$this->loaded()) {
			return [];
		}

		$model = $this->pixie->orm->get('Forms_ChartGroup');
		$model->query->join('forms_chart_group_document', [$model->table . '.id', 'forms_chart_group_document.chart_group_id'], 'inner');
		$model->query->where('forms_chart_group_document.form_document_id', $this->id());
		$model->query->fields($model->table . '.*');


		return $model->find_all()->as_array();
	}

	public function getFileNameForExport()
	{
		if ($this->uploaded_file_id && $this->file && $this->file->loaded()) {

			$name = $this->name;
			if ($this->file->extension) {
				$name .= '.' . $this->file->extension;
			}

			return $name;
		}

		return $this->name . '.pdf';
	}

	public function toArray()
	{
		$sites = [];
		if ($this->is_all_sites) {
			$sitesQuery = $this->pixie->orm->get('Site')->where('organization_id', $this->organization_id);
		} else {
			$sitesQuery = $this->sites;
		}
		foreach ($sitesQuery->find_all() as $site) {
			$sites[] = $site->toArray();
		}

		$data = parent::toArray();
		$data['organization_id'] = $this->organization_id;
		$data['url'] = $this->file->getWebPath();
		$data['sites'] = $sites;
		$data['is_all_sites'] = (bool) $this->is_all_sites;
		$data['is_all_case_types'] = (bool) $this->is_all_case_types;
		$data['include_header'] = (boolean)$this->include_header;

		$data['chart_group_ids'] = [];

		foreach ($this->getChartGroups() as $chartGroup) {
			$data['chart_group_ids'][] = (int) $chartGroup->id();
		}

		$data['filename_for_export'] = $this->getFileNameForExport();

		return $data;
	}

	public function toCustomArray()
	{
		return [
			'id' => $this->id,
			'segment' => $this->segment,
			'name' => $this->name,
			'own_text' => $this->own_text,
			'include_header' => (boolean)$this->include_header,
			'is_landscape' => (boolean)$this->is_landscape
		];
	}

}

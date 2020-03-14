<?php

namespace Opake\Model\Cases\OperativeReport\Future;

use Opake\Model\Cases\OperativeReport\AbstractTemplate;
use Opake\Model\Cases\OperativeReport\SiteTemplate;

class Template extends AbstractTemplate
{
	public $id_field = 'id';
	public $table = 'case_op_report_future_fields_template';
	protected $_row = [
		'id' => null,
		'future_template_id' => null,
		'field' => '',
		'name' => '',
		'group_id' => null,
		'sort' => null,
		'active' => null,
		'custom_value' => null,
		'list_value' => null, // serialized object
	];

	protected $belongs_to = [
		'future_template' => [
			'model' => 'Cases_OperativeReport_Future',
			'key' => 'future_template_id',
		],
	];

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('name')->rule('callback', function($name, $validator) {
			$model = $this->pixie->orm->get('Cases_OperativeReport_Future_Template')
				->where('future_template_id', $this->future_template_id)
				->where('name', $name)
				->find();
			return !$model->loaded();
		})->error(sprintf("Field with name %s already exist", $this->name));

		if($this->field === 'custom') {
			$validator->field('custom_value')->rule('max_words_html', 10000)->error('The ' . $this->name . ' must be less than or equal to 10000 words');
		}

		return $validator;
	}

	public function toArray() {
		$service = $this->pixie->services->get('cases_operativeReports');
		$data = [
			'id' => $this->id,
			'future_template_id' => $this->future_template_id,
			'field' => $this->field,
			'type' => SiteTemplate::getTypeByField($this->field),
			'sort' => $this->sort,
			'group_id' => (int)$this->group_id,
			'show' => SiteTemplate::getShowByField($this->field),
			'active' => (bool)$this->active,
			'custom_value' => $this->custom_value,
		];
		if($this->field === 'custom') {
			$data['name'] = $this->name;
			$data['is_site_template_custom_field'] = $service->isSiteTemplateCustomField($this->name, $this->future_template->organization_id);
			$data['type'] = 'text';
		} else {
			$data['name'] = SiteTemplate::getNameByField($this->field);
		}
		if($this->field === 'list') {
			$data['name'] = $this->name;
			$data['type'] = 'list';
			$data['list_value'] = json_decode($this->list_value, true);
 		}
		return $data;
	}
}

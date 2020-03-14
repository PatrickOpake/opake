<?php

namespace OpakeAdmin\Form\Analytics\Reports;


use Opake\Form\AbstractForm;

class CustomReportForm extends AbstractForm
{
	/**
	 * @return array
	 */
	protected function getFields()
	{
		return [
			'name',
		    'columns',
			'parent',
		];
	}

	/**
	 * @param array $data
	 * @return array
	 * @throws \Exception
	 */
	protected function prepareValues($data)
	{
		$result = parent::prepareValues($data);

		if (isset($result['columns'])) {
			$result['columns'] = implode(',', array_filter($data['columns']));
		}

		if (isset($result['parent'])) {
			$result['parent_id'] = $result['parent'];
		}

		return $result;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	protected function prepareValuesForModel($data)
	{
		$data['user_id'] = $this->pixie->auth->user()->id;
		return $data;
	}

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$name = $this->getValueByName('name');
		$userId = $this->pixie->auth->user() ? $this->pixie->auth->user()->id : null;

		$validator->field('name')->rule('filled')->error('Name is empty');
		$validator->field('name')->rule('max_length', 30)
			->error('Maximum name length is 30 symbols');
		$validator->field('name')->rule('matches', '/^[\w\s\-_]+$/usi')
			->error('Name contains incorrect symbols');

		$validator->field('name')->rule('callback', function ($val, $validator, $field) use ($userId, $name) {
			$model = $this->pixie->orm->get('Analytics_Reports_CustomReport')
				->where('name', $name)
				->where('user_id', $userId)
				->find();
			return !$model->loaded();
		})->error(sprintf('Report type "%s" is already in use - Please use a different name', $name));

		$validator->field('columns')->rule('filled')->error('Please select at least one column');
	}
}
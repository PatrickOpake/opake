<?php

namespace Opake\Form;

use Opake\Model\AbstractModel;

abstract class AbstractForm
{

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * Values of fields
	 *
	 * @var array
	 */
	protected $values = [];

	/**
	 * Array of errors
	 *
	 * @var array
	 */
	protected $errors = [];

	/**
	 * @var AbstractModel
	 */
	protected $model;

	/**
	 * @param \Opake\Application $pixie
	 * @param AbstractModel $model
	 */
	public function __construct($pixie, $model = null)
	{
		$this->pixie = $pixie;
		$this->model = $model;
	}

	/**
	 * @param $data
	 */
	public function load($data)
	{
		$this->values = $this->prepareValues($data);
	}

	/**
	 * @return \Opake\Extentions\Validate
	 */
	public function getValidator()
	{
		$validator = $this->pixie->validate->get($this->values);
		$this->setValidationRules($validator);

		return $validator;
	}

	/**
	 * @return bool
	 */
	public function isValid()
	{
		$this->errors = [];

		$validator = $this->getValidator();
		if ($validator->valid()) {
			return true;
		}

		$this->errors = $validator->errors();

		return false;
	}

	/**
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * @return array
	 */
	public function getValues()
	{
		return $this->values;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getValueByName($name)
	{
		return (isset($this->values[$name])) ? $this->values[$name] : null;
	}

	/**
	 * @return AbstractModel
	 */
	public function getModel()
	{
		return $this->model;
	}

	/**
	 * @return array
	 */
	public function getCommonErrorList()
	{
		if ($this->errors) {
			$fullList = [];
			foreach ($this->errors as $fieldErrors) {
				$fullList = array_merge($fullList, $fieldErrors);
			}
			return $fullList;
		}

		return [];
	}

	/**
	 * @return string
	 */
	public function getFirstErrorKey()
	{
		if ($this->errors) {
			reset($this->errors);
			return key($this->errors);
		}

		return null;
	}

	/**
	 * @return string
	 */
	public function getFirstErrorMessage()
	{
		if ($this->errors) {
			$firstField = reset($this->errors);
			return reset($firstField);
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public function hasLoadedModel()
	{
		return ($this->model && $this->model->loaded());
	}

	/**
	 * @throws \Exception
	 */
	public function save()
	{
		if (!$this->model) {
			throw new \Exception('Model for saving is not specified');
		}
		$this->model->fill($this->prepareValuesForModel($this->values));
		$this->model->save();
	}

	public function fillModel()
	{
		if (!$this->model) {
			throw new \Exception('Model for saving is not specified');
		}
		$this->model->fill($this->prepareValuesForModel($this->values));
	}

	/**
	 * @param array $data
	 * @return array
	 * @throws \Exception
	 */
	protected function prepareValues($data)
	{
		if ($data instanceof \stdClass) {
			$data = (array) $data;
		}

		if (!(is_array($data) || $data instanceof \Traversable)) {
			throw new \Exception('Input data is not iterable');
		}
		$result = [];

		foreach ($this->getFields() as $fieldName) {
			if (array_key_exists($fieldName, $data)) {
				$result[$fieldName] = $data[$fieldName];
			}
		}

		return $result;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	protected function prepareValuesForModel($data)
	{
		return $data;
	}

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{

	}

	/**
	 * @return array
	 */
	protected function getFields()
	{
		return [

		];
	}
}
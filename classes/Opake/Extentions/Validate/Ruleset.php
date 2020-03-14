<?php

namespace Opake\Extentions\Validate;

use PHPixie\Validate\Ruleset as PHPixieRuleset;

class Ruleset extends PHPixieRuleset
{

	/**
	 * Validates a Validator field.
	 * If the field is empty and the rule is other than 'filled'
	 * it will always be considered valid.
	 *
	 * @param   string $rule Rule to check against
	 * @param   array $params Rule parameters
	 * @param   string $filed Field to validate
	 * @param   \PHPixie\Validate\Validator Validator the field belongs to
	 * @return  bool If the field is valid
	 */
	public function validate($rule, $params, $field, $validator)
	{
		$value = $validator->get($field);
		$params = array_merge(
			array($value),
			$params,
			array($validator, $field)
		);

		if ($rule !== 'filled_callback') {
			if (empty($value) && $value !== 0 && $value !== '0' && $value !== 0.00) {
				return $rule !== 'filled';
			}
		}

		return call_user_func_array(array($this, 'rule_' . $rule), $params);
	}

	/**
	 * Checks if the value is an email
	 *
	 * @param   string $val Value to check
	 * @return  bool
	 */
	public function rule_email($val)
	{
		return ($val && filter_var($val, FILTER_VALIDATE_EMAIL));
	}

	public function rule_url($val)
	{
		if (!parse_url($val, PHP_URL_SCHEME)) {
			$val = 'http://' . $val;
		}
		return filter_var($val, FILTER_VALIDATE_URL);
	}

	public function rule_phone($val)
	{
		return (bool)preg_match('/^[0-9]{10}/', $val);
	}

	public function rule_date($val)
	{
		try {
			$date = new \DateTime($val);
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	public function rule_filled($val)
	{
		if (is_array($val)) {
			return (bool)sizeof($val);
		} else {
			return !empty($val) || $val === 0 || $val === '0' || $val === 0.00;
		}
	}

	public function rule_unique($val, $model, $validator, $field)
	{
		$rq = $validator->pixie->orm->get($model->model_name)
			->where($field, $val);
		if (isset($model->organization_id)) {
			$rq->where('organization_id', $model->organization_id);
		}
		if ($model->id()) {
			$rq->where($model->id_field, '!=', $model->id());
		}
		return !$rq->find()->loaded();
	}

	public function rule_unique_for_site($val, $model, $validator, $field)
	{
		$rq = $validator->pixie->orm->get($model->model_name)
			->where($field, $val);
		if (isset($model->site_id)) {
			$rq->where('site_id', $model->site_id);
		}
		if ($model->id()) {
			$rq->where($model->id_field, '!=', $model->id());
		}
		return !$rq->find()->loaded();
	}

	public function rule_sequence_dates($start, $end)
	{
		if (is_string($start)) {
			$start = strtotime($start);
		}
		if (is_string($end)) {
			$end = strtotime($end);
		}
		if (!$start || !$end) {
			return false;
		}
		return $start < $end;
	}

	public function rule_filled_callback($val, $callback, $field, $validator)
	{
		return $this->rule_callback($val, $callback, $field, $validator);
	}

	public function rule_min_words($val, $min)
	{
		return str_word_count($val, 0) >= $min;
	}

	public function rule_max_words($val, $max)
	{
		return str_word_count($val, 0) <= $max;
	}

	public function rule_max_words_html($val, $max)
	{
		return (str_word_count(strip_tags(html_entity_decode($val)), 0) <= $max);
	}

}

<?php

namespace Opake\Formatter;

class ModelMethodFormatter extends AbstractFormatter
{
	/**
	 * Old logic where belong_to fields are also included in response and keys for these belong_to fields are removed.
	 * It was left only for backward compatibility with old model logic.
	 * Don't turn it on for any new models, use explicit building instead.
	 *
	 * @var bool
	 */
	protected $includeBelongsTo = false;


	protected function init()
	{
		if (isset($this->config['includeBelongsTo'])) {
			$this->includeBelongsTo = $this->config['includeBelongsTo'];
		}
	}

	public function toArray()
	{
		$data = $this->model->formatArray();
		return $data;
	}

}
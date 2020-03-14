<?php

namespace Opake\Model\Cases\OperativeReport;

use Opake\Model\AbstractModel;

class AbstractTemplate extends AbstractModel
{

	public function fromArray($data)
	{
		if($data->field !== 'custom' && $data->field !== 'list') {
			$data->name = null;
		}

		if($data->field === 'list') {
			$this->clearEmptyListItems($data->list_value);
			$data->list_value = json_encode($data->list_value);
		}

		return $data;
	}

	private function clearEmptyListItems($list_value)
	{
		foreach ($list_value->column1 as $key => $item) {
			if(!$item->text) {
				unset($list_value->column1[$key]);
			}
		}
		$list_value->column1 = array_values($list_value->column1);

		if(isset($list_value->column2)) {
			foreach ($list_value->column2 as $key => $item) {
				if(!$item->text) {
					unset($list_value->column2[$key]);
				}
			}
			$list_value->column2 = array_values($list_value->column2);
		}
	}
}

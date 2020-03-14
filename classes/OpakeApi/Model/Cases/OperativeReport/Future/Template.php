<?php

namespace OpakeApi\Model\Cases\OperativeReport\Future;


use Opake\Helper\StringHelper;
use Opake\Model\Cases\OperativeReport\Future\Template as OpakeOperativeReportFutureTemplate;

class Template extends OpakeOperativeReportFutureTemplate
{
	public function toArray()
	{
		//fixme: force string for API
		$data = parent::toArray();
		if (isset($data['sort'])) {
			$data['sort'] = (string) $data['sort'];
		}

		$data['custom_value'] = StringHelper::stripHtmlTags($this->custom_value);

		return $data;
	}
}
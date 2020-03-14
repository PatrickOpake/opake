<?php

namespace OpakeApi\Model\Cases\OperativeReport;


use Opake\Helper\StringHelper;
use Opake\Model\Cases\OperativeReport\ReportTemplate as OpakeOperativeReportTemplate;

class ReportTemplate extends OpakeOperativeReportTemplate
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
<?php

namespace OpakeApi\Model\Cases\OperativeReport;


use Opake\Helper\StringHelper;
use Opake\Model\Cases\OperativeReport\Future as OpakeOperativeReportFuture;

class Future extends OpakeOperativeReportFuture
{
	public function toArray()
	{
		$data = parent::toArray();
		$data = array_merge($data, [
			'anesthesia_administered' => StringHelper::stripHtmlTags($this->anesthesia_administered),
			'ebl' => StringHelper::stripHtmlTags($this->ebl),
			'drains' => StringHelper::stripHtmlTags($this->drains),
			'consent' => StringHelper::stripHtmlTags($this->consent),
			'complications' => StringHelper::stripHtmlTags($this->complications),
			'approach' => StringHelper::stripHtmlTags($this->approach),
			'description_procedure' => StringHelper::stripHtmlTags($this->description_procedure),
			'follow_up_care' => StringHelper::stripHtmlTags($this->follow_up_care),
			'conditions_for_discharge' => StringHelper::stripHtmlTags($this->conditions_for_discharge),
			'scribe' => StringHelper::stripHtmlTags($this->scribe),
			'specimens_removed' => StringHelper::stripHtmlTags($this->specimens_removed),
			'findings' => StringHelper::stripHtmlTags($this->findings),
			'urine_output' => StringHelper::stripHtmlTags($this->urine_output),
			'fluids' => StringHelper::stripHtmlTags($this->fluids),
			'blood_transfused' => StringHelper::stripHtmlTags($this->blood_transfused),
			'total_tourniquet_time' => StringHelper::stripHtmlTags($this->total_tourniquet_time),
			'clinical_history' => StringHelper::stripHtmlTags($this->clinical_history),
		]);
		return $data;
	}
}
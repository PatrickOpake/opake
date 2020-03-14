<?php

namespace Opake\Formatter\Billing;

use Opake\Formatter\BaseDataFormatter;

class EOBListFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
			    'id',
			    'organization_id',
			    'name',
			    'insurer_name',
			    'cpt',
			    'charge_master_amount',
			    'amount_reimbursed',
			    'uploaded_file_id',
			    'remote_file_id',
			    'url',
			    'mime_type',
			    'file_name',
			],
			'fieldMethods' => [
				'id' => 'int',
				'insurer_name' => 'insurerName',
				'cpt' => 'cpt',
				'url' => 'url',
				'mime_type' => 'mimeType',
				'file_name' => 'fileName',
				'charge_master_amount' => 'money',
				'amount_reimbursed' => 'money',
			]
		]);
	}

	protected function formatInsurerName($name, $options, $model)
	{
		return $model->insurer->name;
	}

	protected function formatCpt($name, $options, $model)
	{
		return $model->charge_master->cpt;
	}

	protected function formatUrl($name, $options, $model)
	{
		return ($model->file && $model->file->loaded()) ? $model->file->getWebPath() : null;
	}

	protected function formatMimeType($name, $options, $model)
	{
		return $model->file->mime_type;
	}

	protected function formatFileName($name, $options, $model)
	{
		return $model->file->original_filename;
	}

}
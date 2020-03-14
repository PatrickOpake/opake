<?php

namespace Opake\Formatter\Cases\Item;

class FinancialDocsFormatter extends ItemFormatter
{

	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'type_name',
				'first_surgeon_name',
				'time_start',
				'appointment_status',
				'financial_documents'
			],
			'fieldMethods' => [
				'type_name' => 'typeName',
				'first_surgeon_name' => 'firstSurgeonName',
				'time_start' => 'toJsDate',
				'financial_documents' => 'financialDocuments',
			]
		]);
	}

	protected function formatTypeName($name, $options, $model)
	{
		if($model->type->loaded()) {
			return $model->type->name;
		}
		return '';
	}

	protected function formatFirstSurgeonName($name, $options, $model)
	{
		return $model->getFirstSurgeonForDashboard();
	}

	protected function formatFinancialDocuments($name, $options, $model)
	{
		return $model->getFinancialDocumentsArray();
	}

}

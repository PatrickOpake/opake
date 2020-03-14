<?php

namespace Opake\Formatter\Billing\Navicure\Claim;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Navicure\Claim;

class CodingFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'type',
			    'title',
			    'status',
			    'additional_status',
			    'status_description',
			    'additional_status_description',
			    'last_update',
			    'sending_date',
			    'last_transaction_date',
			    'errors',
				'status_acknowledgment',
			    'can_send_new'
			],
			'fieldMethods' => [
				'id' => 'int',
				'type' => 'int',
				'title' => 'title',
				'status' => 'int',
				'sending_date' => 'sendingDate',
				'last_transaction_date' => 'lastTransactionDate',
				'additional_status' => 'int',
				'last_update' => 'toDateTime',
			    'status_description' => 'statusDescription',
			    'additional_status_description' => 'additionalStatusDescription',
			    'errors' => 'errors',
				'status_acknowledgment' => 'statusAcknowledgment',
			    'can_send_new' => 'canSendNew'
			]
		]);
	}

	protected function formatTitle($name, $options, $model)
	{
		return $model->getTitle();
	}

	protected function formatStatusDescription($name, $options, $model)
	{
		$listOfStatuses = Claim::getListOfStatusDescription();
		return (isset($listOfStatuses[$model->status])) ? $listOfStatuses[$model->status] : 'Unknown';
	}

	protected function formatAdditionalStatusDescription($name, $options, $model)
	{
		$listOfStatuses = Claim::getListOfAdditionalStatusDescription();
		return (isset($listOfStatuses[$model->additional_status])) ? $listOfStatuses[$model->additional_status] : '';
	}

	protected function formatErrors($name, $options, $model)
	{
		$errors = [];
		if ($model->error) {
			$errors[] = $model->error;
		}

		return $errors;
	}

	protected function formatStatusAcknowledgment($name, $options, $model)
	{
		$result = [];

		$statuses = $model->status_acknowledgments->find_all();
		if ($statuses) {

			$statusesArray = [];
			foreach ($statuses as $item) {
				$statusesArray[] = $item->getFormatter('Coding')->toArray();
			}

			if ($statusesArray) {
				$result['statuses'] = $statusesArray;
			}
		}

		$services = $model->status_acknowledgments_service->find_all();
		if ($services) {

			$servicesArray = [];
			foreach ($services as $item) {
				$servicesArray[] = $item->getFormatter('Coding')->toArray();
			}

			if ($servicesArray) {
				$result['services'] = $servicesArray;
			}
		}

		if (!$result) {
			return null;
		}

		return $result;
	}

	protected function formatCanSendNew($name, $options, $model)
	{
		return true;
	}

	protected function formatSendingDate($name, $options, $model)
	{
		if ($model->sending_date) {
			$date = TimeFormat::fromDBDatetime($model->sending_date);
			if ($date) {
				return TimeFormat::getDateTime($date);
			}
		}

		return '';
	}

	protected function formatLastTransactionDate($name, $options, $model)
	{
		if ($model->last_transaction_date) {
			$date = TimeFormat::fromDBDatetime($model->last_transaction_date);
			if ($date) {
				return TimeFormat::getDateTime($date);
			}
		}

		return '';
	}

}
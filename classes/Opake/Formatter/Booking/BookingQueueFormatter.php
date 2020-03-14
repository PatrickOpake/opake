<?php

namespace Opake\Formatter\Booking;

class BookingQueueFormatter extends \Opake\Formatter\Cases\Item\ItemFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
			    'patient_id',
			    'patient_name',
			    'mrn',
			    'booking_patient_name',
			    'first_surgeon',
			    'time_start',
			    'time_end',
			    'status',
			    'charts_count',
			    'is_valid_for_schedule',
			    'is_self_for_user',
			    'notes_count'
			],

		    'fieldMethods' => [
				'id' => 'int',
				'patient_id' => 'int',
				'patient_name' => ['delegateRelationField', [
					'relation' => 'patient',
			        'formatMethod' => ['modelMethod', [
				        'modelMethod' => 'getFullNameForBooking'
			        ]]
				]],
				'mrn' => ['delegateRelationField', [
					'relation' => 'patient',
					'formatMethod' => ['modelMethod', [
						'modelMethod' => 'getFullMrn'
					]]
				]],
				'booking_patient_name' => ['delegateRelationField', [
					'relation' => 'booking_patient',
					'formatMethod' => ['modelMethod', [
						'modelMethod' => 'getFullNameForBooking'
					]]
				]],
				'first_surgeon' => ['modelMethod', [
					'modelMethod' => 'getFirstSurgeon'
				]],
				'time_start' => 'toJsDate',
				'time_end' => 'toJsDate',
				'status' => 'int',
				'charts_count' => 'chartsCount',
				'is_valid_for_schedule' => ['modelMethod', [
					'modelMethod' => 'isValidForSchedule'
				]],
			    'is_self_for_user' => 'isSelfForUser'
		    ]

		]);
	}


	protected function formatIsSelfForUser($name, $options, $model)
	{
		if ($user = $this->pixie->auth->user()) {
			return $model->isSelf($user);
		}

		return null;
	}

	protected function formatChartsCount($name, $options, $model)
	{
		return (int) $model->getCharts()->count_all();
	}

}
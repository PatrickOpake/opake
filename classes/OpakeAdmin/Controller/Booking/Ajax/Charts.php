<?php

namespace OpakeAdmin\Controller\Booking\Ajax;

use Opake\Exception\BadRequest;
use Opake\Exception\InvalidMethod;
use Opake\Helper\TimeFormat;
use Opake\Model\Analytics\UserActivity\ActivityRecord;

class Charts extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionList()
	{
		$booking = $this->loadModel('Booking', 'subid');
		$bookingCharts = []; ;
		foreach ($booking->getCharts()->find_all() as $chart) {
			$bookingCharts[] = $chart->toArray();
		}

		$this->result = [
			'charts' => $bookingCharts
		];
	}

	public function actionUpload()
	{
		try {
			$booking = $this->loadModel('Booking', 'subid');

			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			$chartId = $this->request->post('id');

			if ($chartId) {
				$chart = $this->orm->get('Cases_Chart', $chartId);
				$chart->name = $this->request->post('name');

				$actionQueue = $this->pixie->activityLogger
					->newModelActionQueue($chart)
					->addAction(ActivityRecord::ACTION_BOOKING_FILE_RENAME)
					->setAdditionalInfo('booking', $booking)
					->assign();

				$chart->save();

				$actionQueue->registerActions();

			} else {
				/** @var \Opake\Request $req */
				$req = $this->request;
	
				$files = $req->getFiles();
				if (empty($files['file'])) {
					throw new BadRequest('Empty file');
				}
	
				$upload = $files['file'];
				if (!$upload->isEmpty() && !$upload->hasErrors()) {
					/** @var \Opake\Model\UploadedFile $uploadedFile */
					$uploadedFile = $this->pixie->orm->get('UploadedFile');
					$uploadedFile->storeFile($upload, [
						'is_protected' => true,
						'protected_type' => 'cases_chart'
					]);
					$uploadedFile->save();

					$chart = $this->orm->get('Cases_Chart');
					$chart->list_id = $booking->getCaseBookingListId();
					$chart->uploaded_file_id = $uploadedFile->id;
					$chart->uploaded_date = strftime(TimeFormat::DATE_FORMAT_DB, time());
					$chart->name = $this->request->post('name');
					$chart->save();

					$this->pixie->activityLogger
						->newModelActionQueue($chart)
						->addAction(ActivityRecord::ACTION_BOOKING_FILE_UPLOAD)
						->setAdditionalInfo('booking', $booking)
						->assign()
						->registerActions();
				}
			}

			$this->result = 'ok';

		} catch (\Exception $e) {
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	public function actionRemove()
	{
		$chart = $this->loadModel('Cases_Chart', 'subid');
		if ($chart->loaded()) {
			$chart->delete();
			$this->result = 'ok';

			$booking = $this->orm->get('Booking', $this->request->get('booking'));
			$this->pixie->activityLogger
				->newModelActionQueue($chart)
				->addAction(ActivityRecord::ACTION_BOOKING_FILE_REMOVE)
				->setAdditionalInfo('booking', $booking)
				->assign()
				->registerActions();

		}
	}

}

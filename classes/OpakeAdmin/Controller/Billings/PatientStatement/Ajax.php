<?php

namespace OpakeAdmin\Controller\Billings\PatientStatement;

use Opake\Exception\BadRequest;
use Opake\Helper\StringHelper;
use Opake\Model\Billing\PatientStatement\History;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use OpakeAdmin\Helper\Printing\Document\Billing\PatientStatementGenerator;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$this->checkAccess('billing', 'view');

		$search = new \OpakeAdmin\Model\Search\Billing\PatientStatement\Patient($this->pixie);
		$search->setOrganizationId($this->org->id());
		$models = $search->search(
			$this->pixie->orm->get('Patient'),
			$this->request
		);



		$result = [];
		foreach ($models as $model) {
			$result[] = $model->getFormatter('BillingPatientStatementListEntry')->toArray();
		}

		$patientStatementCommentOptions = [];
		$patientStatementCommentOptions[] = [
			'id' => null,
			'title' => ''
		];

		foreach (PatientStatementGenerator::getCommentOptions() as $index => $option) {
			$patientStatementCommentOptions[] = [
				'id' => $index,
				'title' => $option
			];
		}

		$this->result = [
			'items' => $result,
			'total_count' => $search->getPagination()->getCount(),
			'statement_comment_options' => $patientStatementCommentOptions
		];
	}

	public function actionGeneratePatientStatement()
	{
		$this->checkAccess('billing', 'view');
		
		$patient = $this->loadModel('Patient', 'subid');

		$data = $this->getData(true);

		$comment = '';
		if (isset($data['chosen_comment']['title'])) {
			$comment = $data['chosen_comment']['title'];
		} else if (isset($data['custom_comment'])) {
			$comment = $data['custom_comment'];
		}

		$comment = StringHelper::truncate($comment, 120, '');

		$document = new \OpakeAdmin\Helper\Printing\Document\Billing\PatientStatementGenerator($patient);
		$document->setComment($comment);
		$printHelper = new \OpakeAdmin\Helper\Printing\PrintCompiler();
		$printHelper->setCleanTemporaryFiles(false);
		$result = $printHelper->compile([$document]);
		$this->logToHistoryStatement($patient, $result->id());

		$this->pixie->activityLogger
			->newAction(ActivityRecord::ACTION_BILLING_PATIENT_STATEMENT_GENERATED)
			->setModel($patient)
			->register();

		$this->result = [
			'success' => true,
			'id' => $result->id(),
			'url' => $result->getResultUrl()
		];
	}

	public function actionCompilePatientStatements()
	{
		$this->checkAccess('billing', 'view');

		try {

			$patients = $this->request->post('patients');

			if (!$patients || !is_array($patients)) {
				throw new BadRequest('Patients list is empty');
			}

			$documentsToPrint = [];
			$patientModels = [];
			foreach ($patients as $patientId) {
				$patient = $this->pixie->orm->get('Patient', $patientId);
				if ($patient->loaded()) {
					$document = new \OpakeAdmin\Helper\Printing\Document\Billing\PatientStatementGenerator($patient);
					$documentsToPrint[] = $document;

					$patientModels[] = $patient;
				}
			}

			$helper = new \OpakeAdmin\Helper\Printing\PrintCompiler();
			$helper->setCleanTemporaryFiles(false);
			$result = $helper->compile($documentsToPrint);

			foreach ($patientModels as $patient) {
				$this->logToHistoryStatement($patient, $result->id(), true);
				$this->pixie->activityLogger
					->newModelActionQueue($patient)
					->addAction(ActivityRecord::ACTION_BILLING_PATIENT_STATEMENT_GENERATED)
					->assign()
					->registerActions();
			}

			$this->result = [
				'success' => true,
				'id' => $result->id(),
				'url' => $result->getResultUrl()
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	protected function logToHistoryStatement($patient, $printResultId, $bulkPrint = false)
	{
		$model = $this->orm->get('Billing_PatientStatement_History');
		$model->patient_id = $patient->id();
		$model->print_result_id = $printResultId;
		$model->is_bulk_print = ($bulkPrint) ? 1 : 0;
		$model->type = History::GENERATED_TYPE_PATIENT_STATEMENT;
		$model->save();
	}

}
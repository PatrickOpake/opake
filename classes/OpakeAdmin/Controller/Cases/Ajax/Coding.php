<?php

namespace OpakeAdmin\Controller\Cases\Ajax;

use Opake\Exception\BadRequest;
use Opake\Exception\Forbidden;
use Opake\Exception\PageNotFound;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use OpakeAdmin\Form\Cases\Coding\CodingForm;
use OpakeAdmin\Form\Cases\Coding\BillForm;

class Coding extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionCoding()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$coding = $case->coding;
		$coding->case_id = $case->id;

		$this->result = [
			'coding' => $coding->toArray(),
			'case_primary_insurance_type' => $case->registration->getPrimaryInsuranceType(),
			'case_secondary_insurance_type' => $case->registration->getSecondaryInsuranceType()
		];
	}

	public function actionSave()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$data = $this->getData(true);

		$coding = $case->coding;
		$coding->case_id = $case->id;

		list($form, $billForms, $diagnoses, $occurrences, $values) = $this->fillData($data, $coding, $case);

		$errors = [];

		if (!$form->isValid()) {
			$errors = $form->getCommonErrorList();
		}

		foreach ($billForms as $billForm) {
			if (!$billForm->isValid()) {
				$errors = array_merge($errors, $billForm->getCommonErrorList());
			}
		}

		if (!$errors) {
			$form->save();

			// Update Diagnoses
			$oldDiagnoses = [];
			foreach ($coding->diagnoses->find_all() as $diagnosis) {
				$oldDiagnoses[$diagnosis->id] = $diagnosis;
			}
			foreach ($diagnoses as $diagnosis) {
				if ($diagnosis->loaded() && isset($oldDiagnoses[$diagnosis->id])) {
					unset($oldDiagnoses[$diagnosis->id]);
				}
				$diagnosis->coding_id = $coding->id;
				$diagnosis->save();
			}
			foreach ($oldDiagnoses as $diagnosis) {
				$diagnosis->delete();
			}

			// Update Occurrences
			$oldOccurrences = [];
			foreach ($coding->occurrences->find_all() as $occurrence) {
				$oldOccurrences[$occurrence->id] = $occurrence;
			}
			foreach ($occurrences as $occurrence) {
				if ($occurrence->loaded() && isset($oldOccurrences[$occurrence->id])) {
					unset($oldOccurrences[$occurrence->id]);
				}
				$occurrence->coding_id = $coding->id;
				$occurrence->save();
			}
			foreach ($oldOccurrences as $occurrence) {
				$occurrence->delete();
			}

			// Update Values
			$oldValues = [];
			foreach ($coding->values->find_all() as $value) {
				$oldValues[$value->id] = $value;
			}
			foreach ($values as $value) {
				if ($value->loaded() && isset($oldValues[$value->id])) {
					unset($oldValues[$value->id]);
				}
				$value->coding_id = $coding->id;
				$value->save();
			}
			foreach ($oldValues as $value) {
				$value->delete();
			}

			// Bills update
			$oldBills = [];
			foreach ($coding->bills->find_all() as $bill) {
				$oldBills[$bill->id] = $bill;
			}
			foreach ($billForms as $billForm) {
				if ($billForm->getModel()->loaded() && isset($oldBills[$billForm->getModel()->id])) {
					unset($oldBills[$billForm->getModel()->id]);
				}
				$billForm->getModel()->coding_id = $coding->id;
				$billForm->save();
			}
			foreach ($oldBills as $bill) {
				$bill->delete();
			}

			$updater = new \OpakeAdmin\Helper\Insurance\InputDataUpdater\CodingInsuranceUpdater($case->registration, $this->getData());
			$updater->update();

			$this->pixie->events->fireEvent('Case.CodingSaved', $case);

			$this->pixie->activityLogger
				->newAction(ActivityRecord::ACTION_CODING_PAGE_SAVED)
				->setModel($case)
				->register();

			$this->result = [
				'success' => true
			];

		} else {

			$this->result = [
				'success' => false,
				'errors' => $errors
			];

		}

	}

	public function actionSaveDuplicate()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$data = $this->getData(true);

		$coding = $case->coding;
		if ($coding->loaded()) {
			try {
				$coding->bill_type = $data['bill_type'];
				$coding->reference_number = $data['reference_number'] ?? null;
				$coding->original_claim_id = $data['original_claim_id'] ?? null;
				$coding->save();

				$this->result = [
					'success' => true
				];
			}
			catch (\Exception $e) {
				$this->result = [
					'success' => false,
					'errors' => [$e->getMessage()]
				];
			}
		}
		else {
			$this->result = [
				'success' => false,
				'errors' => ['No coding exists']
			];
		}
	}


	public function actionGenerateUB04()
	{
		$case = $this->loadModel('Cases_Item', 'subid');

		$helper = new \OpakeAdmin\Helper\Billing\Coding\UB04($case);
		$result = $helper->compile();

		$this->pixie->activityLogger
			->newAction(ActivityRecord::ACTION_CODING_PAGE_CLAIM_PREVIEW)
			->setModel($case)
			->register();

		$this->response->file('application/pdf', 'chart_preview.pdf', $result, false);
		$this->execute = false;
	}

	public function actionGenerate1500()
	{
		$case = $this->loadModel('Cases_Item', 'subid');

		$helper = new \OpakeAdmin\Helper\Billing\Coding\CMS1500($case);
		$result = $helper->compile();

		$this->pixie->activityLogger
			->newAction(ActivityRecord::ACTION_CODING_PAGE_CLAIM_PREVIEW)
			->setModel($case)
			->register();

		$this->response->file('application/pdf', 'chart_preview.pdf', $result, false);
		$this->execute = false;
	}

	public function actionFees()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$caseTypeId = $this->request->get('case_type_id');

		$caseType = $this->pixie->orm->get('CPT')
			->where('id', $caseTypeId)
			->find();
		if (!$caseType->loaded()) {
			throw new BadRequest('Unknown procedure');
		}

		$result = [];
		$fees = $this->pixie->orm->get('Billing_FeeSchedule_Record')
			->where('site_id', $case->location->site_id)
			->where('hcpcs', $caseType->code)
			->find_all();

		foreach ($fees as $fee) {
			$result[] = $fee->toArray();
		}

		$this->result = $result;
	}

	public function actionAdditionalHcpcsInfo()
	{
		$chargeMasterEntryId = $this->request->get('charge_master_entry_id');

		$chargeMasterRecord = $this->pixie->orm->get('Master_Charge', $chargeMasterEntryId);

		$modifiers = [];
		if ($chargeMasterRecord->cpt_modifier1 || $chargeMasterRecord->cpt_modifier2) {
			$modifiers[] = [
				'name' => $chargeMasterRecord->getModifiersTitle(),
				'charge_master_entry' => $chargeMasterRecord->getFormatter('ListOption')->toArray()
			];
		}

		$similarRecords = $this->pixie->orm->get('Master_Charge')
			->where('cpt', $chargeMasterRecord->cpt)
			->where('id', '!=', $chargeMasterRecord->id())
			->where('site_id', $chargeMasterRecord->site_id)
			->find_all();

		foreach ($similarRecords as $record) {
			if ($record->cpt_modifier1 || $record->cpt_modifier2) {
				$modifiers[] = [
					'name' => $record->getModifiersTitle(),
					'charge_master_entry' => $record->getFormatter('ListOption')->toArray()
				];
			}
		}

		if (!$chargeMasterRecord->loaded()) {
			throw new PageNotFound();
		}

		$this->result = [
			'charge' => $chargeMasterRecord->amount,
		    'revenue_code' => $chargeMasterRecord->revenue_code,
			'modifiers' => $modifiers,
		];
	}

	public function actionRemoveHcpcsRow()
	{
		$codingBill = $this->loadModel('Cases_Coding_Bill', 'subid');
		if ($codingBill->loaded()) {
			$codingBill->delete();
			$this->result = 'ok';
		}
	}

	public function actionGetCaseInsurances()
	{
		$caseId = $this->request->param('subid');
		if (!$caseId) {
			throw new BadRequest('Bad Request');
		}

		$case = $this->orm->get('Cases_Item', $caseId);
		if (!$case->loaded()) {
			throw new PageNotFound();
		}

		if ($case->organization_id != $this->org->id()) {
			throw new Forbidden();
		}

		$insurances = [];
		foreach ($case->registration->insurances->where('deleted', 0)->find_all() as $insuranceModel) {
			$insurances[] = $insuranceModel->toArray();
		}

		$this->result = [
			'success' => true,
		    'insurances' => $insurances
		];

	}

	protected function fillData($data, $model, $case)
	{
		$form = new CodingForm($this->pixie, $model);
		$form->load($data);

		$billForms = [];

		if (isset($data['bills'])) {
			foreach ($data['bills'] as $key => $billData) {
				if (!empty($billData['id']) && $model->loaded()) {
					$bill = $this->pixie->orm->get('Cases_Coding_Bill', $billData['id']);
				} else {
					$bill = $this->pixie->orm->get('Cases_Coding_Bill');
				}
				$billData['sort'] = (int)$key;

				$billForm = new BillForm($this->pixie, $bill);
				$billForm->load($billData);
				$billForms[] = $billForm;
			}
		}

		$diagnoses = $this->fillDiagnoses($data, $model);
		$occurrences = $this->fillOccurrences($data, $model);
		$values = $this->fillValues($data, $model);

		return [
			$form,
			$billForms,
			$diagnoses,
			$occurrences,
			$values
		];
	}

	protected function fillDiagnoses($data, $model)
	{
		$diagnoses = [];
		$oldDiagnoses = [];
		foreach ($model->diagnoses->find_all() as $diagnosis) {
			$oldDiagnoses[$diagnosis->id] = $diagnosis;
		}

		if (isset($data['diagnoses'])) {
			foreach ($data['diagnoses'] as $diagnosisData) {
				if (!empty($diagnosisData['icd']) && !empty($diagnosisData['icd']['id'])) {
					if (!empty($diagnosisData['id']) && isset($oldDiagnoses[$diagnosisData['id']])) {
						$diagnosis = $oldDiagnoses[$diagnosisData['id']];
					} else {
						$diagnosis = $this->pixie->orm->get('Cases_Coding_Diagnosis');
					}
					$diagnosis->row = $diagnosisData['row'];
					$diagnosis->icd_id = $diagnosisData['icd']['id'];
					$diagnoses[] = $diagnosis;
				}
			}
		}
		return $diagnoses;
	}

	protected function fillOccurrences($data, $model)
	{
		$occurrences = [];
		$oldOccurrences = [];
		foreach ($model->occurrences->find_all() as $occurrence) {
			$oldOccurrences[$occurrence->id] = $occurrence;
		}

		if (isset($data['occurrences'])) {
			foreach ($data['occurrences'] as $occurrenceData) {
				if (!empty($occurrenceData['id']) && isset($oldOccurrences[$occurrenceData['id']])) {
					$occurrence = $oldOccurrences[$occurrenceData['id']];
				} else {
					$occurrence = $this->pixie->orm->get('Cases_Coding_Occurrence');
				}
				if (!empty($occurrenceData['occurrence_code']) && isset($occurrenceData['occurrence_code']['id'])) {
					$occurrence->occurrence_code_id = $occurrenceData['occurrence_code']['id'];
				}
				if (isset($occurrenceData['date'])) {
					$occurrence->date = $occurrenceData['date'];
				}
				$occurrences[] = $occurrence;
			}
		}
		return $occurrences;
	}

	protected function fillValues($data, $model)
	{
		$values = [];
		$oldValues = [];
		foreach ($model->values->find_all() as $value) {
			$oldValues[$value->id] = $value;
		}

		if (isset($data['values'])) {
			foreach ($data['values'] as $valueData) {
				if (!empty($valueData['id']) && isset($oldValues[$valueData['id']])) {
					$value = $oldValues[$valueData['id']];
				} else {
					$value = $this->pixie->orm->get('Cases_Coding_Value');
				}
				if (!empty($valueData['value_code']) && isset($valueData['value_code']['id'])) {
					$value->value_code_id = $valueData['value_code']['id'];
				}
				if (isset($valueData['amount'])) {
					$value->amount = $valueData['amount'];
				}
				$values[] = $value;
			}
		}

		return $values;
	}
}
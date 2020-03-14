<?php

namespace OpakeAdmin\Controller\Insurances\Eligible;

use Opake\Exception\BadRequest;
use Opake\Exception\HttpException;
use Opake\Exception\PageNotFound;
use Opake\Helper\TimeFormat;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Cases\Registration;
use Opake\Model\Insurance\AbstractType;
use OpakeAdmin\Form\Insurance\CoverageCheckingForm;
use OpakeAdmin\Model\Search\Billing\BatchEligibility;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\BatchGenerator;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Generator;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E271\ValidationChecker;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Parser;
use OpakeAdmin\Service\Navicure\HealthCare\Exception\NavicureException;
use OpakeAdmin\Service\Navicure\HealthCare\Exception\ValidationException;
use OpakeAdmin\Service\Navicure\HealthCare\NavicureExceptionChecker;
use OpakeAdmin\Service\Navicure\HealthCare\Request;
use OpakeAdmin\Service\Navicure\HealthCare\RequestParams;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function actionLoadCoverage()
	{

		$data = json_decode($this->request->post('data', null, false), true);

		try {

			if (!isset($data['organization_id'])) {
				throw new BadRequest('Organization ID is required');
			}

			if (!isset($data['case_registration_id'])) {
				throw new BadRequest('Case Registration ID is required');
			}

			if (!isset($data['case_insurance_id'])) {
				throw new BadRequest('Case Insurance ID is required');
			}

			$form = new CoverageCheckingForm($this->pixie);

			/** @var \Opake\Model\Cases\Registration\Insurance $model */
			$model = $this->pixie->orm->get('Cases_Registration_Insurance', $data['case_insurance_id']);
			if (!$model->loaded()) {
				throw new PageNotFound('Insurance is empty');
			}

			if ($model->isDescriptionInsurance()) {
				throw new HttpException('This type of insurance is not supported');
			}

			$insuranceDataModel = $model->getInsuranceDataModel();
			$caseRegistration = $this->pixie->orm->get('Cases_Registration', $data['case_registration_id']);

			$this->pixie->activityLogger
				->newAction(ActivityRecord::ACTION_BILLING_CLICK_CHECK_ELIGIBILITY)
				->setModel($caseRegistration->case)
				->register();

			$organization = $this->pixie->orm->get('Organization', $data['organization_id']);
			if (!$organization->loaded()) {
				throw new \Exception('Unknown organization');
			}

			$formData = [
				'policy_num' => $insuranceDataModel->policy_number,
				'insured_first_name' => $insuranceDataModel->first_name,
				'insured_last_name' => $insuranceDataModel->last_name,
				'insured_dob' => $insuranceDataModel->dob,
				'organization_id' => $data['organization_id'],
				'payor_id' => $insuranceDataModel->insurance_id,
				'type' => $model->type,
				'relationship_to_insured' => $insuranceDataModel->relationship_to_insured,
				'insured_user_state' =>  $insuranceDataModel->state,
				'npi' => $organization->npi,
				'insurance' => $model
			];

			if ($caseRegistration->loaded()) {
				$formData['patient_first_name'] = $caseRegistration->first_name;
				$formData['patient_last_name'] = $caseRegistration->last_name;
				$formData['patient_dob'] = $caseRegistration->dob;
				$formData['patient_user_state'] = $caseRegistration->home_state;
			}

			$form->load($formData);




			if (!$form->isValid()) {
				$this->result = [
					'success' => false,
					'errors' => $form->getCommonErrorList()
				];

				return;
			}

			$requestParams = new RequestParams($this->pixie, $form);

			if (!$requestParams->getOrganizationNpi()) {
				$this->result = [
					'success' => false,
					'errors' => ['NPI of organization is not filled']
				];

				return;
			}

			$generator = new Generator($requestParams);
			$navicureRequest = new Request($this->pixie, $generator->getEDI(), $caseRegistration->case);
			$response =  $navicureRequest->getResponse();
			if(empty($response)) {
				throw new \Exception('Response is empty');
			}


			if(!$response->statusHeader->requestProcessed) {
				$navicureChecker = new NavicureExceptionChecker($response->statusHeader->statusCode);
				throw new NavicureException($navicureChecker->getStatusMessage());
			}

			if($response->statusHeader->requestProcessed && $response->payload) {
				$doc = new  Parser($response->payload);
				$validation = new ValidationChecker($doc->getBenefit());
				$errorsValidation = $validation->validate();
				if(!empty($errorsValidation)) {
					$this->result = [
						'success' => false,
						'errors' => $errorsValidation
					];
					return;
				}
				$benefit = $doc->toArray();
				$model = $this->pixie->orm->get('Eligible_CaseCoverage');
				$model->case_registration_id = $data['case_registration_id'];
				$model->case_insurance_id = $data['case_insurance_id'];
				$model->coverage = json_encode($benefit);
				$model->updated = TimeFormat::formatToDBDatetime(new \DateTime());

				$json = $model->getCoverageArray();
				$previousModel = $this->pixie->orm->get('Eligible_CaseCoverage')
					->where('case_registration_id', $data['case_registration_id'])
					->where('case_insurance_id', $data['case_insurance_id'])
					->find();

				if ($previousModel->loaded()) {
					$previousModel->delete();
				}

				$model->save();
			} else {
				throw new \Exception('Request doesn\'t processed');
			}

			$this->result = [
				'success' => true,
				'coverage' => $json,
				'latestUpdate' => date('D M d Y H:i:s O', strtotime($model->updated))
			];

		} catch (HttpException $e) {
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
		} catch (ValidationException $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
		} catch (NavicureException $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
		} catch (\Exception $e) {
			$this->logSystemError($e);

			$this->result = [
				'success' => false,
				'errors' => ['An error occurred while Eligibility checking']
			];
		}
	}

	public function actionGetCoverage()
	{
		$caseRegistrationId = $this->request->get('caseRegistrationId');
		$caseInsuranceId = $this->request->get('caseInsuranceId');
		if (!$caseRegistrationId) {
			throw new BadRequest('Case Registration ID is required');
		}
		if (!$caseInsuranceId) {
			throw new BadRequest('Insurance ID is required');
		}

		$model = $this->pixie->orm->get('Eligible_CaseCoverage')
			->where('case_registration_id', $caseRegistrationId)
			->where('case_insurance_id', $caseInsuranceId)
			->find();

		if (!$model->loaded()) {
			$this->result = [
				'success' => true,
				'coverage' => null
			];
		} else {
			$this->result = [
				'success' => true,
				'coverage' => $model->getCoverageArray(),
				'latestUpdate' => date('D M d Y H:i:s O', strtotime($model->updated)),
				'insurance_id' => $model->case_insurance_id
			];
		}
	}

	public function actionBatchChecking()
	{
		$data = json_decode($this->request->post('data', null, false), true);

		try {

			if (!isset($data['organization_id'])) {
				throw new BadRequest('Organization ID is required');
			}

			if (!isset($data['case_insurances_id'])) {
				throw new BadRequest('Case Insurances ID is required');
			}

			$organization = $this->pixie->orm->get('Organization', $data['organization_id']);
			if (!$organization->loaded()) {
				throw new \Exception('Unknown organization');
			}

			$caseInsurances = $this->orm->get('Cases_Registration_Insurance')
				->where('id', 'IN', $this->pixie->db->arr($data['case_insurances_id']))
				->find_all()
				->as_array();

			if (empty($caseInsurances)) {
				throw new PageNotFound('Insurances is empty');
			}

			$batchInsurances = [];

			foreach ($caseInsurances as $insurance) {
				if ($insurance->isDescriptionInsurance()) {
					continue;
				}

				$form = new CoverageCheckingForm($this->pixie);

				$insuranceDataModel = $insurance->getInsuranceDataModel();
				$caseRegistration = $this->pixie->orm->get('Cases_Registration', $insurance->registration_id);

				$formData = [
					'policy_num' => $insuranceDataModel->policy_number,
					'insured_first_name' => $insuranceDataModel->first_name,
					'insured_last_name' => $insuranceDataModel->last_name,
					'insured_dob' => $insuranceDataModel->dob,
					'organization_id' => $data['organization_id'],
					'payor_id' => $insuranceDataModel->insurance_id,
					'type' => $insurance->type,
					'relationship_to_insured' => $insuranceDataModel->relationship_to_insured,
					'insured_user_state' =>  $insuranceDataModel->state,
					'npi' => $organization->npi
				];

				if ($caseRegistration->loaded()) {
					$formData['patient_first_name'] = $caseRegistration->first_name;
					$formData['patient_last_name'] = $caseRegistration->last_name;
					$formData['patient_dob'] = $caseRegistration->dob;
					$formData['patient_user_state'] = $caseRegistration->home_state;
				}

				$form->load($formData);

				if (!$form->isValid()) {
					$this->result = [
						'success' => false,
						'errors' => $form->getCommonErrorList()
					];

					return;
				}

				$requestParams = new RequestParams($this->pixie, $form);

				if (!$requestParams->getOrganizationNpi()) {
					$this->result = [
						'success' => false,
						'errors' => ['NPI of organization is not filled']
					];

					return;
				}

				$batchInsurances[] = $requestParams;
			}

			//group by info source
			$groupedBatchInsurances = [];
			foreach ($batchInsurances as $item) {
				$groupedBatchInsurances[$item->getInsurancePayor()->id][] = $item;
			}

			$generator = new BatchGenerator($groupedBatchInsurances);
			$navicureRequest = new Request($this->pixie, $generator->getEDI());
			$response =  $navicureRequest->getResponse();
			if(empty($response)) {
				throw new \Exception('Response is empty');
			}

			if($response->statusHeader->requestProcessed && $response->payload) {
				$doc = new  Parser($response->payload);
				$benefit = $doc->toBatchArray();
				$countsOfBatchEligibility = $this->getCountsOfBatchInsurances($doc);
				$model = $this->pixie->orm->get('Eligible_BatchCoverage');
				$model->coverage = json_encode($benefit);
				$model->organization_id = $data['organization_id'];
				$model->entries_sent = count($caseInsurances);
				$model->date_received = TimeFormat::formatToDBDatetime(new \DateTime());
				$model->insufficient_data = $countsOfBatchEligibility['countOfInsufficientData'];
				$model->eligible = $countsOfBatchEligibility['countOfActiveEligibility'];
				$model->not_eligible = $countsOfBatchEligibility['countOfInActiveEligibility'];
				$model->save();

				foreach ($caseInsurances as $insurance) {
					$this->pixie->db->query('insert')
						->table('case_batch_eligibility_cases')
						->data(['batch_id' => $model->id(), 'case_insurance_id' => $insurance->id])
						->execute();
				}
			} else {
				throw new \Exception('Request doesn\'t processed');
			}

			$this->result = [
				'success' => true,
				'coverage' => $model->toArray(),
			];

		} catch (HttpException $e) {
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
		} catch (ValidationException $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
		} catch (\Exception $e) {
			$this->logSystemError($e);

			$this->result = [
				'success' => false,
				'errors' => ['An error occurred while Eligibility checking']
			];
		}
	}

	public function actionExportEligibility()
	{
		try {
			$data = $this->getData();

			if (!$data) {
				throw new BadRequest('Bad Request');
			}

			$model = $this->pixie->orm->get('Eligible_CaseCoverage')
				->where('case_registration_id', $data->case_registration_id)
				->find();

			if (!$model->loaded()) {
				$this->result = [
					'success' => true,
					'coverage' => null
				];
			} else {
				$document = new \OpakeAdmin\Helper\Printing\Document\Cases\EligibleForm($model);
				$printHelper = new \OpakeAdmin\Helper\Printing\PrintCompiler();
				$result = $printHelper->compile([$document]);

				$this->result = [
					'success' => true,
					'id' => $result->id(),
					'url' => $result->getResultUrl()
				];
			}
		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	protected function getCountsOfBatchInsurances($doc)
	{
		$result = ['countOfInsufficientData' => 0, 'countOfActiveEligibility' => 0, 'countOfInActiveEligibility' => 0];
		$activeEligibilityCodes = ['1', '2', '3', '4', '5'];
		$inActiveEligibilityCodes = ['6', '7', '8'];
		foreach ($doc->getBenefit()->getInformationSources() as $infoSource) {
			foreach ($infoSource->getInformationReceivers() as $receiver) {
				foreach ($receiver->getSubscribers() as $subscriber) {
					$subscriberValidation = $subscriber->getRequestValidations();
					$eligibilities = $subscriber->getEligibilities();
					if(!empty($eligibilities)) {
						foreach ($eligibilities as $eb) {
							if(in_array($eb->getEligibility()->getEligibilityOrBenefitInformationCode(), $activeEligibilityCodes)) {
								$result['countOfActiveEligibility']++;
								break;
							}
							if(in_array($eb->getEligibility()->getEligibilityOrBenefitInformationCode(), $inActiveEligibilityCodes)) {
								$result['countOfInActiveEligibility']++;
								break;
							}
						}
					}

					if(!empty($subscriberValidation)) {
						$result['countOfInsufficientData']++;
					} else {
						foreach ($subscriber->getDependents() as $dependent) {
							$dependentValidation = $dependent->getRequestValidations();
							if(!empty($dependentValidation)) {
								$result['countOfInsufficientData']++;
							}
							$dependentEligibilities = $dependent->getEligibilities();
							foreach ($dependentEligibilities as $eb) {
								if(in_array($eb->getEligibility()->getEligibilityOrBenefitInformationCode(), $activeEligibilityCodes)) {
									$result['countOfActiveEligibility']++;
									break;
								}
								if(in_array($eb->getEligibility()->getEligibilityOrBenefitInformationCode(), $inActiveEligibilityCodes)) {
									$result['countOfInActiveEligibility']++;
									break;
								}
							}
						}
					}
				}

			}
		}

		return $result;
	}
}
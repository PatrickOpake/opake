<?php

namespace OpakeAdmin\Controller\Cases\Ajax\Intake;

use Opake\Controller\Cases\Patients\Forms\Influenza as OpakeInfluenzaContoller;
use Opake\Form\Cases\Patients\InfluenzaForm;

class Influenza extends \OpakeAdmin\Controller\Ajax
{
	use OpakeInfluenzaContoller;

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionCompileForm()
	{
		$registrationId = $this->request->get('registration_id');

		$model = $this->pixie->orm->get('Patient_Appointment_Form_Influenza')
			->where('case_registration_id', $registrationId)
			->find();
		$model->case_registration_id = $registrationId;

		$form = new InfluenzaForm($this->pixie, $model);
		$form->load($this->getData(true));
		$form->fillModel();

		try {

			$helper = new \OpakeAdmin\Helper\Printing\PrintCompiler();
			$printResult = $helper->compile([
				new \OpakeAdmin\Helper\Printing\Document\Cases\InfluenzaForm($model)
			]);

			$this->result = [
				'success' => true,
				'url' => $printResult->getResultUrl(),
				'print' => $printResult->isReadyToPrint()
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

}
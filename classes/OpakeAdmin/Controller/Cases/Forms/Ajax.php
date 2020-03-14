<?php

namespace OpakeAdmin\Controller\Cases\Forms;

use Opake\Exception\PageNotFound;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function actionCompileForms()
	{
		try {

			$registrationId = $this->request->param('subid');
			$documents = $this->request->post('documents');

			$registration = $this->pixie->orm->get('Cases_Registration', $registrationId);
			if (!$registration->loaded()) {
				throw new PageNotFound('Unknown registration');
			}
			$case = $registration->case;

			$documentsList = [];
			foreach ($documents as $document) {
				if ($document === 'facesheet') {
					$documentsList = new \OpakeAdmin\Helper\Printing\Document\Cases\FacesheetDocument($case);
				} else {
					$documentObject = $this->pixie->orm->get('Cases_Registration_Document', $document);
					if ($documentObject->loaded()) {
						$documentsList[] = new \OpakeAdmin\Helper\Printing\Document\Cases\AdditionalChart\ChartFile($documentObject);
					}
				}
			}

			$helper =  new \OpakeAdmin\Helper\Printing\PrintCompiler();
			$result = $helper->compile($documentsList);

			$this->result = [
				'success' => true,
				'id' => $result->id(),
				'url' => $result->getResultUrl(),
				'print' => $result->isReadyToPrint()
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
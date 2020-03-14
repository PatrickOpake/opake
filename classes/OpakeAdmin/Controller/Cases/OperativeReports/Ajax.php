<?php

namespace OpakeAdmin\Controller\Cases\OperativeReports;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function actionCompileOperativeReports()
	{
		try {

			$reports = $this->request->post('reports');

			if (!$reports || !is_array($reports)) {
				throw new \Exception('Reports list is empty');
			}

			$documentsToPrint = [];
			foreach ($reports as $reportId) {
				$report = $this->pixie->orm->get('Cases_OperativeReport', $reportId);
				if ($report->loaded()) {
					$documentsToPrint[] = new \OpakeAdmin\Helper\Printing\Document\Cases\OperativeReport($report);
				}
			}

			$helper = new \OpakeAdmin\Helper\Printing\PrintCompiler();
			$result = $helper->compile($documentsToPrint);

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
}
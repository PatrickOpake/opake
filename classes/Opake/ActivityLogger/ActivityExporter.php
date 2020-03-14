<?php

namespace Opake\ActivityLogger;

use Opake\ActivityLogger\Exporter\RichText;
use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use PHPExcel_Exception;
use PHPExcel_Style_Alignment;

class ActivityExporter
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;


	/**
	 * @var bool
	 */
	protected $showOrganization = true;

	/**
	 * @var AbstractModel[]
	 */
	protected $models;

	/**
	 * @var array
	 */
	protected $filters;

	/**
	 * @var string
	 */
	protected $output = '';

	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}

	/**
	 * @return boolean
	 */
	public function isShowOrganization()
	{
		return $this->showOrganization;
	}

	/**
	 * @param boolean $showOrganization
	 */
	public function setShowOrganization($showOrganization)
	{
		$this->showOrganization = $showOrganization;
	}

	/**
	 * @return string
	 */
	public function getOutput()
	{
		return $this->output;
	}

	/**
	 * @return array
	 */
	public function getFilters()
	{
		return $this->filters;
	}

	/**
	 * @param array $filters
	 */
	public function setFilters($filters)
	{
		$this->filters = $filters;
	}

	/**
	 * @return \Opake\Model\AbstractModel[]
	 */
	public function getModels()
	{
		return $this->models;
	}

	/**
	 * @param \Opake\Model\AbstractModel[] $models
	 */
	public function setModels($models)
	{
		$this->models = $models;
	}

	/**
	 * @param \Opake\Request $request
	 */
	public function setFiltersFromRequest($request)
	{
		$this->filters = [
			'action' => trim($request->get('action')),
			'organization' => trim($request->get('organization')),
			'user' => trim($request->get('user')),
			'date_from' => trim($request->get('date_from')),
			'date_to' => trim($request->get('date_to')),
			'case' =>  trim($request->get('case')),
		];
	}

	/**
	 * @throws PHPExcel_Exception
	 */
	public function exportToExcel()
	{
		$excel = new \PHPExcel();
		$excel->getProperties()
			->setCreator('Opake')
			->setLastModifiedBy('Opake')
			->setTitle('User Activity')
			->setSubject('User Activity');

		$sheet = $excel->getSheet(0);
		$sheet->setTitle('User Activity');

		$sheetStyle = $sheet->getStyle('A6:H6');
		$sheetStyle->applyFromArray([
			'font' => ['bold' => true,],
			'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
		]);
		$sheetStyle->getAlignment()->setWrapText(true);

		$sheet->getColumnDimension('A')->setWidth(20);

		for ($col = ord('b'); $col <= ord('h'); $col++) {
			$sheet->getColumnDimension(chr($col))->setAutoSize(true);
		}
		$sheet->getDefaultRowDimension()->setRowHeight(-1);

		$columns = $this->getColumns();

		$formattedFilters = $this->getFormattedFilters();
		$filterPlaces = $this->getFilterPlaces();

		$rowNum = 0;
		foreach ($formattedFilters as $name => $value) {
			if (isset($filterPlaces[$rowNum])) {
				$sheet->setCellValue($filterPlaces[$rowNum][0], $name);
				$sheet->setCellValue($filterPlaces[$rowNum][1], $value);

				$sheet->getStyle($filterPlaces[$rowNum][0])->getFont()->setBold(true);
			}
			++$rowNum;
		}

		foreach ($columns as $index => $name) {
			$sheet->setCellValueByColumnAndRow($index, 6, $name);
		}

		$rowNum = 7;
		foreach ($this->models as $model) {
			$formattedRow = $this->formatRow($model);
			foreach ($formattedRow as $index => $value) {
				$sheet->setCellValueByColumnAndRow($index, $rowNum, $value);
				$sheet->getStyleByColumnAndRow($index, $rowNum)->getAlignment()
					->setWrapText(true)
					->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			}

			$sheet->getRowDimension($rowNum)->setRowHeight(-1);

			$rowNum++;
		}

		$writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$tmpPath = tempnam(sys_get_temp_dir(), 'opk');
		$writer->save($tmpPath);

		if (is_file($tmpPath)) {
			$this->output = file_get_contents($tmpPath);
			unlink($tmpPath);
		}
	}

	/**
	 * @param ActivityRecord $model
	 * @return array
	 */
	protected function formatRow($model)
	{
		$actionDate = TimeFormat::fromDBDatetime($model->date);
		try {
			$actionViewer = $this->pixie->activityLogger->newActionViewer($model);
			$formattedChanges = $actionViewer->formatChanges();
			$formattedDetails = $actionViewer->formatDetails();
		} catch (\Exception $e) {
			$formattedChanges = [];
			$formattedDetails = [];
		}


		$data = [];
		if ($this->isShowOrganization()) {
			$data[] = ($model->user && $model->user->loaded() && $model->user->organization && $model->user->organization->loaded()) ?
				$model->user->organization->name : '';
		}

		$data[] = ($model->user && $model->user->loaded()) ? $model->user->getFullName() : '';
		$data[] = TimeFormat::getDate($actionDate);
		$data[] = TimeFormat::getTime($actionDate);
		$data[] = $model->getActionTitle();

		$details = $this->formatDescription($formattedDetails);
		$changes = $this->formatDescription($formattedChanges);

		$out = new RichText();
		if ($details) {
			$out->addRichText($details);
		}
		if ($details->getRichTextElements() && $changes->getRichTextElements()) {
			$out->createText("\r\n\r\n");
		}
		if ($changes) {
			$out->addRichText($changes);
		}

		$data[] = $out;

		return $data;
	}

	/**
	 * @param $fields
	 * @return RichText
	 */
	protected function formatDescription($fields)
	{
		$out = new RichText();
		try {
			foreach ($fields as $key => $value) {
				$keyObj = $out->createTextRun($key);
				$keyObj->getFont()->setBold(true);
				$out->createText(': ');
				if (isset($value['type'])) {
					if ($value['type'] === 'link') {
						$out->createText($value['title']);
					} else if ($value['type'] === 'changes') {
						$out->createText("\r\n");
						foreach ($value['data'] as $changeData) {
							$out->createText(str_repeat(' ', 2));
							$labelObj = $out->createTextRun($changeData['label']);
							$labelObj->getFont()->setBold(true);
							$out->createText("\r\n");
							foreach ($changeData['data'] as $changeKey => $changeValue) {
								$out->createText(str_repeat(' ', 4));
								$changeKeyObj = $out->createTextRun($changeKey);
								$changeKeyObj->getFont()->setBold(true);
								$out->createText(': ');
								$out->createText((!is_array($changeValue)) ? $changeValue : '');
								$out->createText("\r\n");
							}
						}
					} else if ($value['type'] === 'keyValue') {
						$out->createText("\r\n");
						foreach ($value['data'] as $changeKey => $changeValue) {
							$out->createText(str_repeat(' ', 2));
							$changeKeyObj = $out->createTextRun($changeKey);
							$changeKeyObj->getFont()->setBold(true);
							$out->createText(': ');
							$out->createText((!is_array($changeValue)) ? $changeValue : '');
							$out->createText("\r\n");
						}
					}
				} else {

					$out->createText((!is_array($value)) ? $value : '');
				}
				$out->createText("\r\n");
			}
			$out->removeLastElement();
		} catch (\Exception $e) {

		}
		return $out;
	}

	protected function getFormattedFilters()
	{
		$filters = [];
		if ($this->isShowOrganization()) {
			$filters['Organization'] = '';
			if (!empty($this->filters['organization'])) {
				$filters['Organization'] = FormatterHelper::formatOrganization($this->pixie, $this->filters['organization']);
			}
		}
		$filters['User'] = '';
		if (!empty($this->filters['user'])) {
			$filters['User'] = FormatterHelper::formatUser($this->pixie, $this->filters['user']);
		}
		$filters['Activity'] = '';
		if (!empty($this->filters['action'])) {
			$filters['Activity'] = $this->pixie->activityLogger->getFullActionTitle($this->filters['action']);
		}
		$filters['From'] = '';
		if (!empty($this->filters['date_from'])) {
			$dt = TimeFormat::fromDBDate($this->filters['date_from']);
			$filters['From'] = (string)TimeFormat::getDate($dt);
		}
		$filters['To'] = '';
		if (!empty($this->filters['date_to'])) {
			$dt = TimeFormat::fromDBDate($this->filters['date_to']);
			$filters['To'] = (string)TimeFormat::getDate($dt);
		}
		$filters['Case'] = '';
		if (!empty($this->filters['case'])) {
			$filters['Case'] = $this->filters['case'];
		}

		return $filters;
	}

	protected function getColumns()
	{
		$columns = [];
		if ($this->isShowOrganization()) {
			$columns[] = 'Organization';
		}
		$columns[] = 'User';
		$columns[] = 'Date';
		$columns[] = 'Time';
		$columns[] = 'Activity';
		$columns[] = 'Description';

		return $columns;
	}

	protected function getFilterPlaces()
	{
		return [
			['A2', 'B2'],
			['D2', 'E2'],
			['A3', 'B3'],
			['D3', 'E3'],
			['A4', 'B4'],
			['D4', 'E4'],
		];
	}
}
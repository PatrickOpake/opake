<?php

namespace OpakeAdmin\Helper\Export;

class FeeSchedule
{
	/**
	 * @var int
	 */
	protected $siteId;

	/**
	 * @var int
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $delimiter = ',';

	/**
	 * @return mixed
	 */
	public function getSiteId()
	{
		return $this->siteId;
	}

	/**
	 * @param mixed $siteId
	 */
	public function setSiteId($siteId)
	{
		$this->siteId = $siteId;
	}

	/**
	 * @param mixed $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getType()
	{
		return $this->type;
	}

	public function generateCsv()
	{
		if (!$this->siteId) {
			throw new \Exception('Site ID is required');
		}

		$app = \Opake\Application::get();

		$tmpPath = $app->app_dir . '/_tmp/fee-schedule-' . uniqid() .'.csv';
		$fh = fopen($tmpPath, 'w+');


		fputcsv($fh, [
			'HCPCS/CPT', 'Description', 'Contracted Rate'
		], $this->delimiter);

		fputcsv($fh, [
			'Support alphanumeric combinations. No Special characters', 'Short description of HCPCS - limit to ', 'Support numbers including up to 2 decimal places. Inputs will be without $ symbo'
		], $this->delimiter);

		$records = $app->orm->get('Billing_FeeSchedule_Record')
			->where('site_id', $this->siteId)
			->where('type', $this->type)
			->find_all();

		foreach ($records as $record) {
			$row = [
				$record->hcpcs,
			    $record->description,
			    $record->contracted_rate,
			];

			fputcsv($fh, $row, $this->delimiter);
		}

		fclose($fh);

		$content = file_get_contents($tmpPath);
		unlink($tmpPath);

		return $content;
	}
}
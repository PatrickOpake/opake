<?php

namespace OpakeAdmin\Service\ASCX12\E837\Request\Blocks;

use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;

class RenderingProvider extends AbstractRequestSegment
{
	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @param \Opake\Model\Cases\Item $case
	 */
	public function __construct(\Opake\Model\Cases\Item $case)
	{
		$this->case = $case;
	}

	protected function generateSegmentsBeforeChildren($data)
	{

		$site = $this->case->location->site;

		if (!$site || !$site->loaded()) {
			throw new \Exception('Site for case is not defined');
		}

		if (!$site->name) {
			throw new \Exception('Site Name is not filled for site ' . $site->name);
		}

		if (!$site->npi) {
			throw new \Exception('NPI is not filled for site ' . $site->name);
		}

		//Loop 2010AA Billing Provider
		$data[] = [
			'NM1',
			'82',
			'2',
			$this->prepareString($site->name, 60),
			'',
			'',
			'',
			'',
			'XX',
			$this->prepareNumber($site->npi, 10)
		];

		return $data;
	}


}
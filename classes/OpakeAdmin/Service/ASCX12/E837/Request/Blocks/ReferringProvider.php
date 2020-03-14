<?php

namespace OpakeAdmin\Service\ASCX12\E837\Request\Blocks;

use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;

class ReferringProvider extends AbstractRequestSegment
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

		if ($this->case->point_of_origin == \Opake\Model\Cases\Item::POINT_OF_ORIGIN_NON_CLINIC && $this->case->referring_provider_name && $this->case->referring_provider_npi) {

			$parts = explode(' ', $this->case->referring_provider_name, 2);
			$firstName = $parts[0];
			$lastName = (isset($parts[1])) ? $parts[1] : '';

			$data[] = [
				'NM1',
				'DN',
				'1',
				$this->prepareString($lastName, 60),
				$this->prepareString($firstName, 35),
				'',
				'',
				'',
				'XX',
				$this->prepareNumber($this->case->referring_provider_npi, 10)
			];

		}

		return $data;
	}


}
<?php

namespace OpakeAdmin\Service\ASCX12\E837I\Request\Blocks;

use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;

class AttendingProvider extends AbstractRequestSegment
{
	/**
	 * @var \Opake\Model\User
	 */
	protected $user;

	/**
	 * @param \Opake\Model\User $user
	 */
	public function __construct(\Opake\Model\User $user)
	{
		$this->user = $user;
	}

	protected function generateSegmentsBeforeChildren($data)
	{
		$primaryUser = $this->user;

		if (!$primaryUser->credentials->npi_number) {
			throw new \Exception('NPI is not filled for user ' . $primaryUser->getFullName());
		}

		$data[] = [
			'NM1',
			'71',
			'1',
			$this->prepareString($primaryUser->getLastName(), 60),
			$this->prepareString($primaryUser->getFirstName(), 35),
			'',
			'',
			'',
			'XX',
			$this->prepareNumber($primaryUser->credentials->npi_number, 10)
		];

		return $data;
	}
}
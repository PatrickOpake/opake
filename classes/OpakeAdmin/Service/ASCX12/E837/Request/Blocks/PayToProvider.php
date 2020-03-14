<?php
namespace OpakeAdmin\Service\ASCX12\E837\Request\Blocks;

use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;

class PayToProvider extends AbstractRequestSegment
{

	/**
	 * @var \Opake\Model\User
	 */
	protected $user;

	/**
	 * @var \Opake\Model\Site
	 */
	protected $caseSite;

	/**
	 * PayToProvider constructor.
	 *
	 * @param \Opake\Model\User $user
	 * @param \Opake\Model\Site $caseSite
	 */
	public function __construct(\Opake\Model\User $user, \Opake\Model\Site $caseSite)
	{
		$this->user = $user;
		$this->caseSite = $caseSite;
	}

	protected function generateSegmentsBeforeChildren($data)
	{
		$primaryUser = $this->user;
		$caseSite = $this->caseSite;

		//Loop 2010AB Pay-To Provider

		if ($primaryUser->address_type == \Opake\Model\User::ADDRESS_TYPE_PO_BOX) {
			if ($caseSite->pay_address) {
				$data[] = [
					'NM1',
					'87',
					'2'
				];

				$address = $this->prepareAddress($caseSite->pay_address, 55);
				$data[] = [
					'N3',
					$address[0]
				];

				$data[] = [
					'N4',
					$this->prepareString($caseSite->pay_city->name, 30),
					$this->prepareString($caseSite->pay_state->code, 2),
					$this->prepareString($caseSite->pay_zip_code, 15)
				];
			}
		}
		return $data;
	}


}
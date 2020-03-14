<?php
namespace OpakePatients\Form\Cases\Insurances;

use Opake\Form\AbstractForm;
use Opake\Model\AbstractModel;
use Opake\Model\Insurance\AbstractType;

abstract class AbstractInsuranceForm extends AbstractForm
{
	/**
	 * @var AbstractModel
	 */
	protected $registration;

	/**
	 * @var AbstractType
	 */
	protected $insuranceModel;

	/**
	 * @param \Opake\Application $pixie
	 * @param AbstractModel $model
	 * @param AbstractModel $registration
	 */
	public function __construct($pixie, $model = null, $registration = null, $insuranceModel = null)
	{
		parent::__construct($pixie, $model);

		$this->registration = $registration;
		$this->insuranceModel = $insuranceModel;
	}

}
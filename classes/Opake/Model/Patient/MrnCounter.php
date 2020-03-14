<?php

namespace Opake\Model\Patient;

use Opake\Model\AbstractModel;

class MrnCounter extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'patient_mrn_counter';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'counter' => null
	];

	public function incrementCounterForOrganization($organizationId)
	{
		$model = $this->pixie->orm->get($this->model_name);
		$model->where('organization_id', $organizationId);
		$counter = $model->find();

		if (!$counter->loaded()) {
			$counterValue = $this->getInitialValue();
			$counter = $this->pixie->orm->get($this->model_name);
			$counter->organization_id = $organizationId;
			$counter->counter = $counterValue;
			$counter->save();
		}

		$counterValue = (int) $counter->counter;
		while (true) {
			$model = $this->pixie->orm->get('Patient');
			$model->where('mrn', $counterValue);
			$model->where('organization_id', $organizationId);
			if ($model->count_all() > 0) {
				$counterValue += 1;
			} else {
				break;
			}
		}

		//$counter->counter = $counterValue + 1;
		//$counter->save();

		return $counterValue;
	}

	protected function getInitialValue()
	{
		return 3000;
	}
}
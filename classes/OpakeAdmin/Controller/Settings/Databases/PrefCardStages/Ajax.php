<?php

namespace OpakeAdmin\Controller\Settings\Databases\PrefCardStages;

use OpakeAdmin\Form\Settings\Databases\PrefCardStageForm;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}
	}

	public function actionIndex()
	{
		$items = [];
		foreach ($this->orm->get('PrefCard_Stage')->find_all() as $result) {
			$items[] = $result->toArray();
		}
		$this->result = [
			'items' => $items
		];
	}

	public function actionSave()
	{
		$data = $this->getData();
		if ($data) {
			$model = $this->pixie->orm->get('PrefCard_Stage', (isset($data->id)) ? $data->id : null);

			$form = new PrefCardStageForm($this->pixie, $model);
			$form->load($data);

			if ($form->isValid()) {

				$form->save();

				$this->result = [
					'success' => true,
					'id' => (int) $model->id()
				];

			} else {

				$this->result = [
					'success' => false,
					'errors' => $form->getCommonErrorList()
				];

			}
		}
	}

	public function actionDelete()
	{
		$model = $this->loadModel('PrefCard_Stage', 'id');
		$model->delete();
	}

}

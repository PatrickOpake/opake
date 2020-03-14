<?php

namespace OpakeAdmin\Controller;

use Opake\Model\AbstractModel;

class Ajax extends AbstractController
{
	/**
	 * Current organization
	 *
	 * @var \Opake\Model\Organization
	 */
	protected $org;

	/**
	 * Result Array for response
	 *
	 * @var array
	 */
	protected $result = null;

	public function after()
	{
		$this->response->headers = [
			'Content-type: text/javascript;charset=UTF-8'
		];
		$this->response->body = json_encode($this->result);
	}

	/**
	 * Валидирует и либо обновляет модель, либо пишет ошибки в view
	 *
	 * TODO: копипаста из API
	 *
	 * @param \Opake\Model\AbstractModel $model
	 * @param array $data
	 * @return bool
	 * @throws \Exception
	 */
	protected function updateModel($model, $data)
	{
		if ($data) {
			$model->fill($data);
		}

		$this->checkValidationErrors($model);
		$model->save();

		return true;
	}

	/**
	 * Обновление модели без валидации
	 *
	 * @param \Opake\Model\AbstractOrm $model
	 * @param array $data
	 */
	protected function updateModelWithoutValidation($model, $data)
	{
		if ($data) {
			$model->fill($data);
		}
		$model->save();
	}

	/**
	 * @param AbstractModel $model
	 * @param \PHPixie\Validate\Validator $validator
	 * @throws \Exception
	 */
	protected function checkValidationErrors($model, $validator = null)
	{
		if (!$validator) {
			$validator = $model->getValidator();
		}

		if (!$validator->valid()) {
			$errors_text = '';
			foreach ($validator->errors() as $field => $errors) {
				$errors_text .= implode('; ', $errors) . '; ';
			}
			$error = trim($errors_text, '; ');

			throw new \Opake\Exception\ValidationError($error);
		}
	}

	/**
	 * Возвращает данные запроса
	 * @param bool $asArray
	 * @return array|object
	 */
	public function getData($asArray = false)
	{
		return json_decode($this->request->post('data', null, false), $asArray);
	}


	public function actionEmail()
	{
		$data = $this->getData();

		if (isset($data->to)) {
			try {
				\Opake\Helper\Mailer::send($data, $this->logged());
			} catch (\Exception $e) {
				$this->logSystemError($e);
				throw new \Opake\Exception\Ajax($e->getMessage());
			}
		}
	}

	public function actionLanguages()
	{
		$model = $this->orm->get('Language');
		$this->result = [];

		foreach ($model->getList() as $item) {
			$this->result[] = $item->toArray();
		}
	}

	public function actionIcds()
	{
		$result = [];
		$q = $this->request->get('query');
		$yearAdding = $this->request->get('year_adding');
		$usedCodesIds = explode(',', $this->request->get('used_codes_ids'));
		$icd = $this->orm->get('ICD');
		if ($q !== null) {
			$icd->where([
				['code', 'like', '%' . $q . '%'],
				['or', ['desc', 'like', '%' . $q . '%']]
			]);
		}

		if ($yearAdding) {

			if (!$icd->checkCodesExistForYear($yearAdding)) {
				$yearAdding = $icd->getLatestYearWithCodes();
			}

			$query = $icd->query;
			$query->fields('icd.*');

			$query->join('icd_to_icd_year', ['icd_to_icd_year.icd_id', 'icd.id'])
				->join('icd_year', ['icd_year.id', 'icd_to_icd_year.year_id'])
				->where('icd_to_icd_year.active', 1)
				->where('icd_year.year', $yearAdding);
		}

		if ($usedCodesIds and count($usedCodesIds)) {
			$icd->where('id', 'NOT IN', $this->pixie->db->arr($usedCodesIds));
		}

		$icd->order_by('code', 'asc')->limit(12);

		foreach ($icd->find_all() as $item) {
			$result[] = $item->toArray();
		}

		$this->result = $result;
	}

	public function actionCpts()
	{
		$result = [];
		$q = $this->request->get('query');
		$yearAdding = $this->request->get('year_adding');
		$usedCodesIds = explode(',', $this->request->get('used_codes_ids'));
		$cpt = $this->orm->get('CPT');
		if ($q !== null) {
			$cpt->where([
				['code', 'like', '%' . $q . '%'],
				['or', ['name', 'like', '%' . $q . '%']]
			]);
		}

		if ($yearAdding) {
			$query = $cpt->query;
			$query->fields('cpt.*');

			$query->join('cpt_to_cpt_year', ['cpt_to_cpt_year.cpt_id', 'cpt.id'])
				->join('cpt_year', ['cpt_year.id', 'cpt_to_cpt_year.year_id'])
				->where('cpt_to_cpt_year.active', 1)
				->where('cpt_year.year', $yearAdding);
		}

		if ($usedCodesIds and count($usedCodesIds)) {
			$cpt->where('id', 'NOT IN', $this->pixie->db->arr($usedCodesIds));
		}

		$cpt->order_by('code', 'asc')->limit(12);

		foreach ($cpt->find_all() as $item) {
			$result[] = $item->toArray();
		}

		$this->result = $result;
	}

	public function actionLastYearCpts()
	{
		$lastCptYearQuery = $this->pixie->db->query('select')
			->table('cpt_year')
			->fields('year')
			->order_by('year', 'desc')
			->limit(1)
			->execute()
			->current();

		$lastCptYear = null;
		if ($lastCptYearQuery) {
			$lastCptYear = $lastCptYearQuery->year;
		}

		$result = [];
		$q = $this->request->get('query');
		$cpt = $this->orm->get('CPT');
		if ($q !== null) {
			$cpt->where([
				['code', 'like', '%' . $q . '%'],
				['or', ['name', 'like', '%' . $q . '%']]
			]);
		}

		$query = $cpt->query;
		$query->fields('cpt.*');

		$query->join('cpt_to_cpt_year', ['cpt_to_cpt_year.cpt_id', 'cpt.id'])
			->join('cpt_year', ['cpt_year.id', 'cpt_to_cpt_year.year_id'])
			->where('cpt_year.year', $lastCptYear);

		$cpt->order_by('code', 'asc')->limit(12);

		foreach ($cpt->find_all() as $item) {
			$result[] = $item->toArray();
		}

		$this->result = $result;
	}

	public function actionDischargeStatusCodes()
	{
		$result = [];
		$q = $this->request->get('query');
		$icd = $this->orm->get('DischargeStatusCode');
		if ($q !== null) {
			$icd->where([
				['code', 'like', '%' . $q . '%'],
				['or', ['verbiage', 'like', '%' . $q . '%']]
			]);
			$icd->order_by('code', 'asc')->limit(12);

			foreach ($icd->find_all() as $item) {
				$result[] = $item->toArray();
			}
		} else {
			foreach ($icd->limit(12)->find_all() as $item) {
				$result[] = $item->toArray();
			}
		}

		$this->result = $result;
	}

	public function actionConditionCodes()
	{
		$result = [];
		$q = $this->request->get('query');
		$icd = $this->orm->get('ConditionCode');
		if ($q !== null) {
			$icd->where([
				['code', 'like', '%' . $q . '%'],
				['or', ['description', 'like', '%' . $q . '%']]
			]);
			$icd->order_by('code', 'asc')->limit(12);

			foreach ($icd->find_all() as $item) {
				$result[] = $item->toArray();
			}
		} else {
			foreach ($icd->limit(12)->find_all() as $item) {
				$result[] = $item->toArray();
			}
		}

		$this->result = $result;
	}

	public function actionOccurrenceCodes()
	{
		$result = [];
		$q = $this->request->get('query');
		$usedCodesIds = explode(',', $this->request->get('used_codes_ids'));
		$occurrenceCode = $this->orm->get('OccurrenceCode');
		if ($q !== null) {
			$occurrenceCode->where([
				['code', 'like', '%' . $q . '%'],
				['or', ['description', 'like', '%' . $q . '%']]
			]);
			$occurrenceCode->order_by('code', 'asc');
		}

		if ($usedCodesIds and count($usedCodesIds)) {
			$occurrenceCode->where('id', 'NOT IN', $this->pixie->db->arr($usedCodesIds));
		}

		foreach ($occurrenceCode->limit(12)->find_all() as $item) {
			$result[] = $item->toArray();
		}

		$this->result = $result;
	}

	public function actionValueCodes()
	{
		$result = [];
		$q = $this->request->get('query');
		$usedCodesIds = explode(',', $this->request->get('used_codes_ids'));
		$valueCode = $this->orm->get('ValueCode');
		if ($q !== null) {
			$valueCode->where([
				['code', 'like', '%' . $q . '%'],
				['or', ['description', 'like', '%' . $q . '%']]
			]);
			$valueCode->order_by('code', 'asc');
		}

		if ($usedCodesIds and count($usedCodesIds)) {
			$valueCode->where('id', 'NOT IN', $this->pixie->db->arr($usedCodesIds));
		}

		foreach ($valueCode->limit(12)->find_all() as $item) {
			$result[] = $item->toArray();
		}

		$this->result = $result;
	}
}

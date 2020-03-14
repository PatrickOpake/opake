<?php

namespace Opake\Service\Cases;

use Opake\Model\Cases\Coding\Supply;

class Coding extends \Opake\Service\AbstractService
{

	protected $base_model = 'Cases_Coding';

	public function getSupplies($case, $org_id)
	{
		$supplies = [];
		$items = $case->getUsedItems();
		$charge_service = $this->pixie->services->get('master_charges');

		foreach ($items as $key => $item) {
			if ($item->inventory->hcpcs) {
				$charge = $charge_service->getChargeByCPT($item->inventory->hcpcs, $org_id);
				$modifier1 = $this->getModifierByCode($charge->cpt_modifier1);
				if ($modifier1->loaded()) {
					$supplies[$key]['modifier1'] = $modifier1->toArray();
				}

				$modifier2 = $this->getModifierByCode($charge->cpt_modifier2);
				if ($modifier2->loaded()) {
					$supplies[$key]['modifier2'] = $modifier2->toArray();
				}
			}
			$supplies[$key]['hcpcs'] = $item->inventory->toArray();
			$supplies[$key]['type_id'] = (string)Supply::TYPE_UNITS;
			$supplies[$key]['cost'] = $item->inventory->price * (int)$item->quantity;
			$supplies[$key]['qty'] = (int)$item->quantity;

		}

		return $supplies;
	}

	public function getCoding($case, $org)
	{
		$coding = [];
		$registration = $case->registration;
		if ($case->registration->loaded()) {
			$coding['admission_type'] = $registration->admission_type;
			$coding['pre_auth'] = $registration->id;
			$coding['facility_name'] = $this->getUser()->organization->name;

			$data['admit_diagnosis'] = [];
			foreach ($registration->admitting_diagnosis->find_all() as $diagnosis) {
				$data['admit_diagnosis'][] = $diagnosis->toArray();
			}
		}
		$coding['procedures'] = [];
		$coding['supplies'] = $this->getSupplies($case, $org);
		return $coding;
	}

	public function getModifierByCode($code)
	{
		return $this->pixie->orm->get('Modifier')->where('code', $code)->find();
	}

	public function updateProcedures($model, $data)
	{
		$oldModels = $model->procedures->find_all();
		$actualModelIds = [];

		if (isset($data->procedures) && $data->procedures) {
			foreach ($data->procedures as $procedure) {
				$procedure_model = $this->orm->get('Cases_Coding_Procedure', isset($procedure->id) ? $procedure->id : null);
				$procedure_model->coding_id = $model->id;
				try {
					$procedure_model->fill($procedure);
					$procedure_model->save();
					$actualModelIds[] = $procedure_model->id();
				} catch (\Exception $e) {
					throw new \Exception($e->getMessage());

				}
			}
		}

		foreach ($oldModels as $oldModel) {
			if (!in_array($oldModel->id(), $actualModelIds)) {
				$oldModel->delete();
			}
		}

	}

	public function updateSupplies($model, $data)
	{
		$oldModels = $model->supplies->find_all();
		$actualModelIds = [];

		if (isset($data->supplies) && $data->supplies) {
			foreach ($data->supplies as $supply) {
				$supply_model = $this->orm->get('Cases_Coding_Supply', isset($supply->id) ? $supply->id : null);
				$supply_model->coding_id = $model->id;
				try {
					$supply_model->fill($supply);
					$supply_model->save();
					$actualModelIds[] = $supply_model->id();
				} catch (\Exception $e) {
					throw new \Exception($e->getMessage());
				}
			}
		}

		foreach ($oldModels as $oldModel) {
			if (!in_array($oldModel->id(), $actualModelIds)) {
				$oldModel->delete();
			}
		}

	}

	public function updateOccurrences($model, $data)
	{
		$oldModels = $model->occurences->find_all();
		$actualModelIds = [];

		if (isset($data->occurrences) && $data->occurrences) {
			foreach ($data->occurrences as $occurrence) {
				if (isset($occurrence->cond_code) && $occurrence->cond_code) {
					$occurrence_model = $this->orm->get('Cases_Coding_Occurence', isset($occurrence->id) ? $occurrence->id : null);
					$occurrence_model->coding_id = $model->id;
					try {
						$occurrence_model->fill($occurrence);
						$occurrence_model->save();
						$actualModelIds[] = $occurrence_model->id();
					} catch (\Exception $e) {
						throw new \Exception($e->getMessage());
					}
				}
			}
		}

		foreach ($oldModels as $oldModel) {
			if (!in_array($oldModel->id(), $actualModelIds)) {
				$oldModel->delete();
			}
		}

	}

	public function updateNotes($model, $data)
	{
		$oldModels = $model->notes->find_all();
		$actualModelIds = [];

		if (isset($data->notes) && $data->notes) {
			foreach ($data->notes as $note) {
				$note_model = $this->orm->get('Cases_Coding_Note', isset($note->id) ? $note->id : null);
				$note_model->coding_id = $model->id;
				try {
					$note_model->fill($note);
					$note_model->save();
					$actualModelIds[] = $note_model->id();
				} catch (\Exception $e) {
					throw new \Exception($e->getMessage());
				}
			}
		}

		foreach ($oldModels as $oldModel) {
			if (!in_array($oldModel->id(), $actualModelIds)) {
				$oldModel->delete();
			}
		}

	}

}

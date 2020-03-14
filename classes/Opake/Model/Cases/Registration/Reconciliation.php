<?php

namespace Opake\Model\Cases\Registration;

use Opake\Model\AbstractModel;
use Opake\Model\Cases\Item;
use Opake\Model\Patient\Appointment\Form\PreOperative;


/**
 * Class Reconciliation
 * @package Opake\Model\Cases\Registration
 */
class Reconciliation extends AbstractModel {

	const ANESTHESIA_DRUGS_ULTANE = 0;
	const ANESTHESIA_DRUGS_FENTANYL = 1;
	const ANESTHESIA_DRUGS_PROPOFOL = 2;
	const ANESTHESIA_DRUGS_MIDAZOLAM = 3;
	const ANESTHESIA_DRUGS_OTHER = 4;

	public $id_field = 'id';
	public $table = 'case_registration_reconciliation';
	protected $_row = [
		'id' => null,
		'registration_id' => null,
		'no_known_allergies' => 0,
		'copy_given_to_patient' => 0,
		'patient_denies' => 0,
		'pre_op_call' => 0,
		'admission' => 0,
		'anesthesia_type' => Item::ANESTHESIA_TYPE_NOT_SPECIFIED,
		'anesthesia_drugs' => null,
		'anesthesia_drugs_other' => ''
	];

	protected $belongs_to = [
		'case_registration' => [
			'model' => 'Cases_Registration',
			'key' => 'registration_id',
		]
	];

	protected $has_many = [
		'allergies' => [
			'model' => 'Cases_Registration_Reconciliation_Allergy',
			'key' => 'reconciliation_id',
			'cascade_delete' => true
		],
		'medications' => [
			'model' => 'Cases_Registration_Reconciliation_Medication',
			'key' => 'reconciliation_id',
			'cascade_delete' => true
		],
		'visit_updates' => [
			'model' => 'Cases_Registration_Reconciliation_VisitUpdate',
			'key' => 'reconciliation_id',
			'cascade_delete' => true
		],
	];

	public function toArray()
	{
		$data = parent::toArray();
		$data['no_known_allergies'] = (bool) $this->no_known_allergies;
		$data['copy_given_to_patient'] = (bool) $this->copy_given_to_patient;
		$data['patient_denies'] = (bool) $this->patient_denies;
		$data['pre_op_call'] = (bool) $this->pre_op_call;
		$data['admission'] = (bool) $this->admission;
		$data['case_anesthesia_type'] = $this->case_registration->case->anesthesia_type;

		$allergies = [];
		foreach ($this->allergies->find_all() as $allergy) {
			$allergies[] = $allergy->toArray();
		}
		$data['allergies'] = $allergies;

		$medications = [];
		foreach ($this->medications->find_all() as $medication) {
			$medications[] = $medication->toArray();
		}
		$data['medications'] = $medications;

		$visitUpdates = [];
		foreach ($this->visit_updates->find_all() as $visitUpdate) {
			$visitUpdates[] = $visitUpdate->toArray();
		}
		$data['visit_updates'] = $visitUpdates;

		return $data;
	}

	public function updateMultipleFields($data)
	{
		$this->allergies->delete_all();
		if (count($data->allergies)) {
			foreach ($data->allergies as $allergyData) {
				$allergyModel = $this->pixie->orm->get('Cases_Registration_Reconciliation_Allergy', isset($allergyData->id) ? $allergyData->id : null);
				$allergyData->reconciliation_id = $this->id;
				$allergyModel->fill($allergyData);
				$allergyModel->save();
			}
		}

		$this->medications->delete_all();
		if (count($data->medications)) {
			foreach ($data->medications as $medicationData) {
				$medicationModel = $this->pixie->orm->get('Cases_Registration_Reconciliation_Medication', isset($medicationData->id) ? $medicationData->id : null);
				$medicationData->reconciliation_id = $this->id;
				$medicationModel->fill($medicationData);
				$medicationModel->save();
			}
		}

		$this->visit_updates->delete_all();
		if (count($data->visit_updates)) {
			foreach ($data->visit_updates as $visitData) {
				$visitModel = $this->pixie->orm->get('Cases_Registration_Reconciliation_VisitUpdate', isset($visitData->id) ? $visitData->id : null);
				$visitData->reconciliation_id = $this->id;
				$visitModel->fill($visitData);
				$visitModel->save();
			}
		}
	}
	
	/**
	 * @param PreOperative $preOp
	 */
	public function updateFromPreOpForm($preOp)
	{
		$allergies = unserialize($preOp->allergies);
		$i = 0;
		foreach ($this->allergies->find_all()->as_array() as $allergy) {
			if (isset($allergies[$i]) && isset($allergies[$i]['name'])) {
				$allergy->name = $allergies[$i]['name'];
				$allergy->save();
			}
			$i++;
		}

		$medications = unserialize($preOp->medications);
		$i = 0;
		foreach ($this->medications->find_all()->as_array() as $medication) {
			if (isset($medications[$i]) && isset($medications[$i]['name'])) {
				$medication->name = $medications[$i]['name'];
				$medication->save();
			}
			$i++;
		}
	}

	public static function getAnesthesiaDrugsList()
	{
		return [
			self::ANESTHESIA_DRUGS_ULTANE => 'Ultane',
			self::ANESTHESIA_DRUGS_FENTANYL => 'Fentanyl',
			self::ANESTHESIA_DRUGS_PROPOFOL => 'Propofol',
			self::ANESTHESIA_DRUGS_MIDAZOLAM => 'Midazolam',
			self::ANESTHESIA_DRUGS_OTHER => 'Other'
		];
	}
}

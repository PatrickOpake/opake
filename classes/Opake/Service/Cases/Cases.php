<?php

namespace Opake\Service\Cases;

use Opake\Model\Cases\OperativeReport;
use Opake\Helper\TimeFormat;

class Cases extends \Opake\Service\AbstractService
{

	protected $base_model = 'Cases_Item';

	public function fillBlockingItems($blocking)
	{
		$now = new \DateTime();
		$futureExistItems = $blocking->items->where('start', '>', TimeFormat::formatToDBDatetime($now))
			->find_all()->as_array();

		$blockItems = [];
		$items = $blocking->transformBlocks();
		foreach ($items as $item) {
			if ($item->getStart() > $now) {
				if (sizeof($futureExistItems)) {
					$blockingItem = array_shift($futureExistItems);
				} else {
					$blockingItem = $this->pixie->orm->get('Cases_Blocking_Item');
					$blockingItem->organization_id = $blocking->organization_id;
					$blockingItem->blocking_id = $blocking->id;
				}
				$blockingItem->start = TimeFormat::formatToDBDatetime($item->getStart());
				$blockingItem->end = TimeFormat::formatToDBDatetime($blocking->getEndTime($item->getStart()));
				$blockingItem->color = $blocking->color;
				$blockingItem->location_id = $blocking->location_id;
				$blockingItem->doctor_id = $blocking->doctor_id;
				$blockingItem->practice_id = $blocking->practice_id;
				$blockingItem->description = $blocking->description;
				$blockingItem->overwrite = $blocking->overwrite;
				$blockItems[] = $blockingItem;
			}
		}
		foreach ($futureExistItems as $item) {
			$item->delete();
		}
		return $blockItems;
	}

	public function updateClaim($case, $org)
	{
		if (!$case->claim->loaded()) {
			$claim = $this->orm->get('Cases_Claim');
			$site = $case->location->site;
			$claim->case_id = $case->id;
			$claim->fromSite($site);
			$claim->fromOrg($org);
			$claim->save();
		}
	}

	public function createReport($case, $type = OperativeReport::TYPE_SURGEON, $surgeon_id = null)
	{
		/** @var OperativeReport $report */
		$report = $this->orm->get('Cases_OperativeReport');
		$report->operation_time = $case->time_start . ' / ' . $case->time_end;
		$report->status = OperativeReport::STATUS_OPEN;
		$report->type = $type;
		$report->surgeon_id = $surgeon_id;
		$report->case_id = $case->id;
		$report->save();
		return $report->id();
	}

	public function createReports($case)
	{
		$reports = [];
		$surgeonsTypes = [
			'Co-Surgeon' => OperativeReport::TYPE_CO_SURGEON,
			'Anesthesiologist' => OperativeReport::TYPE_ANESTHESIOLOGIST,
			'Supervising Surgeon' => OperativeReport::TYPE_SUPERVISING_SURGEON,
			'First Assistant Surgeon' => OperativeReport::TYPE_FIRST_ASSISTANT_SURGEON,
			'Assistant' => OperativeReport::TYPE_ASSISTANT,
			'Dictated by' => OperativeReport::TYPE_DICTATED_BY,
			'Other Staff' => OperativeReport::TYPE_OTHER_STAFF,
		];

		foreach (OperativeReport::getTypeSurgeons() as $typeSurgeon) {
			$reports[$typeSurgeon] = [];
		}

		$this->beginTransaction();
		try {
			foreach ($case->getUsers() as $surgeon) {
				$reports[OperativeReport::TYPE_SURGEON][] = $surgeon->id;
			}
			foreach ($case->getSurgeonsArray() as $typeName => $surgeonArray) {
				foreach ($surgeonArray as $surgeon) {
					if($surgeon->isDoctor()) {
						$reports[$surgeonsTypes[$typeName]][] = $surgeon->id;
					}
				}
			}

			foreach ($reports as $typeId => $surgeonIds) {
				$this->deleteReports($case, $surgeonIds, $typeId);
				foreach ($surgeonIds as $surgeonId) {
					$existedReport = $this->orm->get('Cases_OperativeReport')->where(
						['case_id', $case->id()],
						['surgeon_id', $surgeonId],
						['type',  $typeId]
					)->find();
					if (!$existedReport->loaded()) {
						$this->createReport($case, $typeId, $surgeonId);
					}
				}
			}

		} catch (\Exception $e) {
			$this->rollback();
			throw new \Exception($e->getMessage());
		}
		$this->commit();
	}

	public function getUsersRelations($casesIds = [])
	{
		$caseUserRelations = $this->db
			->query('select')
			->table('case_user')
			->where('case_id', 'IN', $this->pixie->db->expr("(" . implode(',', $casesIds) . ")"))
			->execute()
			->as_array();

		$caseOtherStaffRelations = $this->db
			->query('select')
			->table('case_other_staff')
			->where('case_id', 'IN', $this->pixie->db->expr("(" . implode(',', $casesIds) . ")"))
			->execute()
			->as_array();

		$caseSurgeonAssistantRelations = $this->db
			->query('select')
			->table('case_assistant')
			->where('case_id', 'IN', $this->pixie->db->expr("(" . implode(',', $casesIds) . ")"))
			->execute()
			->as_array();

		return array_merge($caseUserRelations, $caseOtherStaffRelations, $caseSurgeonAssistantRelations);
	}

	public function getExistedDocs($case, $doc_type)
	{
		$model = $this->pixie->orm->get('Cases_Registration_Document')->with('file');
		$model->query
			->join(['case_registration', 'cr'], [$model->table . '.case_registration_id', 'cr.id'])
			->join(['case', 'cs'], ['cs.id', 'cr.case_id'])
			->where('cs.type_id', $case->type_id)
			->where($model->table . '.document_type', $doc_type)
			->where('cr.patient_id', $case->registration->patient_id)
			->where('cr.id', '<>', $case->registration->id())
			->where($model->table . '.uploaded_date', '<>', 'NULL')
			->where($model->table . '.uploaded_file_id', '<>', 'NULL')
			->order_by($model->table . '.uploaded_date', 'desc');
		return $model->find_all();
	}

	/**
	 * @param $case
	 * @param $cpts
	 * @deprecated
	 */
	public function updateCpts($case, $cpts)
	{
		$this->db->query('delete')->table('case_cpt')->where('case_id', $case->id())->execute();
		foreach ($cpts as $cpt) {
			$this->db->query('insert')
				->table('case_cpt')
				->data(['case_id' => $case->id(), 'cpt_id' => $cpt->id])
				->execute();
		}
	}

	protected function deleteReports($case, $surgeonIds, $typeId)
	{
		if($surgeonIds) {
			$this->orm->get('Cases_OperativeReport')->where(
				['case_id', $case->id()],
				['surgeon_id', 'NOT IN',  $this->pixie->db->expr("(" . implode(',', $surgeonIds) . ")")],
				['type', $typeId]
			)->delete_all();
		} else {
			$this->orm->get('Cases_OperativeReport')->where(
				['case_id', $case->id()],
				['type', $typeId]
			)->delete_all();
		}
	}
}

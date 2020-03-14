<?php

namespace OpakeAdmin\Helper\Analytics\Reports;

use Opake\Helper\TimeFormat;
use Opake\Model\Profession;
use Opake\Model\Role;

class RoomUtilizationGenerator
{

	const TYPE_ROOM_UTILIZATION = 5;

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var int
	 */
	protected $organizationId;

	/**
	 * @var \DateTime
	 */
	protected $dateFrom;

	/**
	 * @var \DateTime
	 */
	protected $dateTo;

	/**
	 * @var array
	 */
	protected $surgeons;

	/**
	 * @var array
	 */
	protected $practiceGroups;

	/**
	 * @var array
	 */
	protected $locations;

	/**
	 * @var string
	 */
	protected $delimiter = ',';


	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}

	/**
	 * @return int
	 */
	public function getOrganizationId()
	{
		return $this->organizationId;
	}

	/**
	 * @param int $organizationId
	 */
	public function setOrganizationId($organizationId)
	{
		$this->organizationId = $organizationId;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateFrom()
	{
		return $this->dateFrom;
	}

	/**
	 * @param \DateTime $dateFrom
	 */
	public function setDateFrom($dateFrom)
	{
		$this->dateFrom = $dateFrom;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateTo()
	{
		return $this->dateTo;
	}

	/**
	 * @param \DateTime $dateTo
	 */
	public function setDateTo($dateTo)
	{
		$this->dateTo = $dateTo;
	}

	/**
	 * @return array
	 */
	public function getSurgeons()
	{
		return $this->surgeons;
	}

	/**
	 * @param array $surgeons
	 */
	public function setSurgeons($surgeons)
	{
		$this->surgeons = $surgeons;
	}

	/**
	 * @return array
	 */
	public function getPracticeGroups()
	{
		return $this->practiceGroups;
	}

	/**
	 * @param array $practiceGroups
	 */
	public function setPracticeGroups($practiceGroups)
	{
		$this->practiceGroups = $practiceGroups;
	}

	/**
	 * @return array
	 */
	public function getLocations()
	{
		return $this->locations;
	}

	/**
	 * @param array $locations
	 */
	public function setLocations($locations)
	{
		$this->locations = $locations;
	}

	public function generate()
	{
		$excel = new \PHPExcel();
		$excel->getProperties()
			->setCreator('Opake')
			->setLastModifiedBy('Opake')
			->setTitle('Analytics_Report')
			->setSubject('Analytics_Report');

		$sheet = $excel->getSheet(0);
		$sheet->setTitle('Analytics Report');

		$datesRow = [
			'Report for dates',
		    TimeFormat::getDate($this->dateFrom),
		    'to',
		    TimeFormat::getDate($this->dateTo)
		];

		$rowIndex = 2;
		foreach ($datesRow as $index => $column) {
			$sheet->setCellValueByColumnAndRow($index, $rowIndex, $column);
		}

		$rowIndex++;
		$sheet->setCellValueByColumnAndRow(0, $rowIndex, 'Room Utilization');
		$rowIndex++;
		$sheet->setCellValueByColumnAndRow(0, $rowIndex, 'Locations');
		$sheet->setCellValueByColumnAndRow(1, $rowIndex, 'Utilized Hours');

		$rowIndex++;
		foreach ($this->getCaseRoomsSummary() as $name => $hours) {
			$sheet->setCellValueByColumnAndRow(0, $rowIndex, $name);
			$sheet->setCellValueByColumnAndRow(1, $rowIndex, $hours);
			$rowIndex++;
		}

		$rowIndex++;
		$sheet->setCellValueByColumnAndRow(0, $rowIndex, 'Case Block Utilization');

		$rowIndex++;

		foreach ([
				 'Surgeon / Practice Name',
				 'Scheduled Hours',
				 'Utilized Hours',
				 'Utilization Rate',
				 'Location'
			 ] as $index => $column) {
			$sheet->setCellValueByColumnAndRow($index, $rowIndex, $column);
		}

		$rowIndex++;
		foreach ($this->getCaseBlockingSummary() as $summary) {
			foreach ($summary as $index => $column) {
				$sheet->setCellValueByColumnAndRow($index, $rowIndex, $column);
			}
			$rowIndex++;
		}

		$highestColumn = $sheet->getHighestColumn();
		for ($col = ord('a'); $col <= ord(strtolower($highestColumn)); $col++) {
			$sheet->getColumnDimension(chr($col))->setAutoSize(true);
		}

		$writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$tmpPath = tempnam(sys_get_temp_dir(), 'opk');
		$writer->save($tmpPath);

		if (is_file($tmpPath)) {
			/** @var \Opake\Model\UploadedFile $uploadedFile */
			$uploadedFile = $this->pixie->orm->get('UploadedFile');
			$uploadedFile->storeContent($this->getOutFileName(), file_get_contents($tmpPath), [
				'is_protected' => true,
				'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
			]);
			$uploadedFile->save();

			unlink($tmpPath);

			$model = $this->pixie->orm->get('Analytics_Reports_GeneratedReport');
			$model->file_id = $uploadedFile->id();
			$model->generateAccessKey();

			$model->save();

			return $model;
		}

		return null;
	}

	protected function getCaseRoomsSummary()
	{
		$cases = $this->getCases();
		$summary = [];
		foreach ($cases as $case) {
			if ($case->location->loaded()) {
				$name = $case->location->name;
				if (!isset($summary[$name])) {
					$summary[$name] = 0;
				}

				$dateStart = $case->time_start_in_fact;
				$dateEnd = $case->time_end_in_fact;
				if (!($dateStart && $dateEnd)) {
					$dateStart = $case->time_start;
					$dateEnd = $case->time_end;
				}

				if ($dateStart && $dateEnd) {
					$dateStart = TimeFormat::fromDBDatetime($dateStart);
					$dateEnd = TimeFormat::fromDBDatetime($dateEnd);
					$secondsDiff = $dateEnd->getTimestamp() - $dateStart->getTimestamp();

					if ($secondsDiff > 0) {
						$summary[$name] += $secondsDiff;
					}
				}

			}
		}

		foreach ($summary as $name => $value) {
			$summary[$name] = $this->formatHours($value);
		}

		return $summary;
	}

	protected function getCaseBlockingSummary()
	{
		$caseBlocks = $this->getCaseBlockingItems();
		$summary = [];
		$casesTimeRanges = $this->getAllCasesTimeRanges();

		$allOrganizationRooms = $this->getAllOrganizationRooms();
		$practiceGroupsForTable = $this->getPracticeGroupsForTable();
		$surgeonsForTable = $this->getUsersForTable();

		foreach ($surgeonsForTable as $surgeon) {
			foreach ($allOrganizationRooms as $room) {
				$key = 'u-' . $surgeon->id() . '-' . $room->id();
				$summary[$key] = [
					'title' => $surgeon->getFullName(),
					'location' => $room->name,
					'scheduled' => 0,
					'utilized' => 0,
					'rate' => 0
				];
			}
		}

		foreach ($practiceGroupsForTable as $practiceGroup) {
			foreach ($allOrganizationRooms as $room) {
				$key = 'pg-' . $practiceGroup->id() . '-' . $room->id();
				$summary[$key] = [
					'title' => $practiceGroup->name,
					'location' => $room->name,
					'scheduled' => 0,
					'utilized' => 0,
					'rate' => 0
				];
			}
		}

		foreach ($caseBlocks as $blockItem) {
			if ($blockItem->doctor_id) {
				$key = 'u-' . $blockItem->doctor_id . '-' . $blockItem->location_id;
			} else {
				$key = 'pg-' . $blockItem->practice_id . '-' . $blockItem->location_id;
			}

			if (!isset($summary[$key])) {
				if ($blockItem->doctor_id) {
					$title = $blockItem->doctor->getFullName();
				} else {
					$title = $blockItem->practice->name;
				}

				$summary[$key] = [
					'title' => $title,
				    'location' => $blockItem->location->name,
				    'scheduled' => 0,
				    'utilized' => 0,
				    'rate' => 0
				];
			}

			if ($blockItem->start && $blockItem->end) {
				$dateStart = TimeFormat::fromDBDatetime($blockItem->start);
				$dateEnd = TimeFormat::fromDBDatetime($blockItem->end);

				$scheduledTime = $dateEnd->getTimestamp() - $dateStart->getTimestamp();
				$utilizedTime = 0;

				$caseBlockTime = [$dateStart->getTimestamp(), $dateEnd->getTimestamp()];

				foreach ($casesTimeRanges as $caseTime) {
					if ($caseTime[2] == $blockItem->location_id) {
						if ($caseTime[0] >= $caseBlockTime[0] && $caseTime[1] <= $caseBlockTime[1]) {
							$utilizedTime += ($caseTime[1] - $caseTime[0]);
						} elseif ($caseTime[0] < $caseBlockTime[0] && $caseTime[1] > $caseBlockTime[0] && $caseTime[1] <= $caseBlockTime[1]) {
							$utilizedTime += ($caseTime[1] - $caseBlockTime[0]);
						} elseif ($caseTime[1] > $caseBlockTime[1] && $caseTime[0] < $caseBlockTime[1] && $caseTime[0] >= $caseBlockTime[0]) {
							$utilizedTime += ($caseBlockTime[1] - $caseTime[0]);
						} elseif ($caseTime[0] < $caseBlockTime[0] && $caseTime[1] > $caseBlockTime[1]) {
							$utilizedTime = $scheduledTime;
							break;
						}
					}
				}

				if ($utilizedTime > $scheduledTime) {
					$utilizedTime = $scheduledTime;
				}

				$summary[$key]['scheduled'] += $scheduledTime;
				$summary[$key]['utilized'] += $utilizedTime;
			}
		}

		$summaryResult = [];
		foreach ($summary as $key => $caseBlockSummary) {

			if ($caseBlockSummary['scheduled'] > 0) {
				$percent = round(($caseBlockSummary['utilized'] / $caseBlockSummary['scheduled']) * 100);
			} else {
				$percent = 0;
			}

			$summary[$key]['rate'] = $percent . '%';

			$summary[$key]['utilized'] = $this->formatHours($summary[$key]['utilized']);
			$summary[$key]['scheduled'] = $this->formatHours($summary[$key]['scheduled']);

			$summaryResult[] = [
				$summary[$key]['title'],
			    $summary[$key]['scheduled'],
			    $summary[$key]['utilized'],
			    $summary[$key]['rate'],
			    $summary[$key]['location']
			];
		}

		return $summaryResult;
	}

	protected function getAllOrganizationRooms()
	{
		$model = $this->pixie->orm->get('Organization', $this->organizationId);
		if (!$model->loaded()) {
			return [];
		}

		$rooms = [];
		$sites = $model->sites
			->where('active', 1)
			->find_all();

		foreach ($sites as $site) {
			$locations = $site->locations;
			if ($this->locations) {
				$locations->where('id', 'IN', $this->pixie->db->arr($this->locations));
			}
			foreach ($locations->find_all() as $room) {
				$rooms[] = $room;
			}
		}

		return $rooms;
	}

	protected function getUsersForTable()
	{
		if ($this->practiceGroups && !$this->surgeons) {
			return [];
		}

		$model = $this->pixie->orm->get('User');
		$query = $model->query;
		$query->where('organization_id', $this->organizationId);
		$query->where('status', 'active');
		$query->where('role_id', Role::Doctor);

		if ($this->surgeons) {
			$query->where('id', 'IN', $this->pixie->db->arr($this->surgeons));
		}

		return $model->find_all();
	}

	protected function getPracticeGroupsForTable()
	{
		if ($this->surgeons && !$this->practiceGroups) {
			return [];
		}

		$model = $this->pixie->orm->get('Organization', $this->organizationId);
		if (!$model->loaded()) {
			return [];
		}

		$practiceGroups = $model->practice_groups;
		$practiceGroups->where('active', 1);
		if ($this->practiceGroups) {
			$practiceGroups->query->where('id', 'IN', $this->pixie->db->arr($this->practiceGroups));
		}

		return $practiceGroups->find_all();
	}

	protected function getCases()
	{
		$model = $this->pixie->orm->get('Cases_Item');

		$query = $model->query;
		$query->fields('case.*');
		$query->where('organization_id', $this->organizationId);
		$query->group_by('case.id');

		if ($this->dateFrom) {
			$query->where($this->pixie->db->expr('DATE(case.time_start)'), '>=', TimeFormat::formatToDB($this->dateFrom) . ' 00:00:00');
		}

		if ($this->dateTo) {
			$query->where($this->pixie->db->expr('DATE(case.time_start)'), '<=', TimeFormat::formatToDB($this->dateTo) . ' 23:59:59');
		}

		if ($this->practiceGroups || $this->surgeons) {
			$query->join('case_user', ['case_user.case_id', 'case.id'], 'left');
		}

		if ($this->practiceGroups && $this->surgeons) {
			$query->join('user_practice_groups', ['case_user.user_id', 'user_practice_groups.user_id'], 'left');
			$query->join('practice_groups', ['user_practice_groups.practice_group_id', 'practice_groups.id'], 'left');
			$query->where([
				['case_user.user_id', 'IN', $this->pixie->db->arr($this->surgeons)],
			    ['or', [
				    ['practice_groups.active', 1],
				    ['user_practice_groups.practice_group_id', 'IN', $this->pixie->db->arr($this->practiceGroups)]
			    ]]
			]);

		} else {
			if ($this->practiceGroups) {
				$query->join('user_practice_groups', ['case_user.user_id', 'user_practice_groups.user_id'], 'inner');
				$query->join('practice_groups', ['user_practice_groups.practice_group_id', 'practice_groups.id'], 'inner');
				$query->where('user_practice_groups.practice_group_id' ,'IN', $this->pixie->db->arr($this->practiceGroups));
				$query->where('practice_groups.active', 1);
			}

			if ($this->surgeons) {
				$query->where('case_user.user_id', 'IN', $this->pixie->db->arr($this->surgeons));
			}
		}

		if ($this->locations) {
			$query->where('case.location_id', 'IN', $this->pixie->db->arr($this->locations));
		}

		$model->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED);
		$model->order_by('case.time_start', 'DESC');

		return $model->find_all();
	}


	protected function getCaseBlockingItems()
	{
		$model = $this->pixie->orm->get('Cases_Blocking_Item');

		$query = $model->query;
		$query->fields('case_blocking_item.*');
		$query->where('case_blocking_item.organization_id', $this->organizationId);
		$query->group_by('case_blocking_item.id');
		$model->order_by('case_blocking_item.start', 'DESC');

		if ($this->dateFrom) {
			$query->where($this->pixie->db->expr('DATE(case_blocking_item.start)'), '>=', TimeFormat::formatToDB($this->dateFrom) . ' 00:00:00');
		}

		if ($this->dateTo) {
			$query->where($this->pixie->db->expr('DATE(case_blocking_item.end)'), '<=', TimeFormat::formatToDB($this->dateTo) . ' 23:59:59');
		}

		if ($this->practiceGroups && $this->surgeons) {

			$query->where([
					['case_blocking_item.doctor_id', 'IN', $this->pixie->db->arr($this->surgeons)],
					['or', ['case_blocking_item.practice_id', 'IN', $this->pixie->db->arr($this->practiceGroups)]]
			]);

		} else {
			if ($this->surgeons) {
				$query->where('case_blocking_item.doctor_id', 'IN', $this->pixie->db->arr($this->surgeons));
			}

			if ($this->practiceGroups) {
				$query->where('case_blocking_item.practice_id', 'IN', $this->pixie->db->arr($this->practiceGroups));
			}
		}

		if ($this->locations) {
			$query->where('case_blocking_item.location_id', 'IN', $this->pixie->db->arr($this->locations));
		}

		return $model->find_all();
	}

	protected function getAllCasesTimeRanges()
	{
		$query = $this->pixie->db->query('select');
		$query->table('case');
		$query->fields('time_start', 'time_end', 'time_start_in_fact', 'time_end_in_fact', 'location_id');
		$query->where('organization_id', $this->organizationId);
		$query->group_by('case.id');

		if ($this->dateFrom) {
			$query->where($this->pixie->db->expr('DATE(case.time_start)'), '>=', TimeFormat::formatToDB($this->dateFrom) . ' 00:00:00');
		}

		if ($this->dateTo) {
			$query->where($this->pixie->db->expr('DATE(case.time_end)'), '<=', TimeFormat::formatToDB($this->dateTo) . ' 23:59:59');
		}

		$query->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED);
		$query->order_by('case.time_start', 'DESC');

		$rows = $query->execute();

		$result = [];
		foreach ($rows as $row) {
			$dateStart = $row->time_start_in_fact;
			$dateEnd = $row->time_end_in_fact;
			if (!($dateStart && $dateEnd)) {
				$dateStart = $row->time_start;
				$dateEnd = $row->time_end;
			}

			if ($dateStart && $dateEnd) {
				$dateStart = TimeFormat::fromDBDatetime($dateStart);
				$dateEnd = TimeFormat::fromDBDatetime($dateEnd);

				$result[] = [$dateStart->getTimestamp(), $dateEnd->getTimestamp(), (int) $row->location_id];
			}
		}

		return $result;
	}

	/**
	 * @param $seconds
	 * @return string
	 */
	protected function formatHours($seconds)
	{
		$hours = round($seconds / 3600, 1);
		return (string) $hours;
	}

	protected function getOutFileName()
	{
		$nameParts[] = 'Case_Block_Utilization';

		if ($this->dateFrom) {
			$nameParts[] = $this->dateFrom->format('dMY');
		}

		if ($this->dateTo) {
			$nameParts[] = $this->dateTo->format('dMY');
		}

		return implode('_', $nameParts) . '.xlsx';
	}

}
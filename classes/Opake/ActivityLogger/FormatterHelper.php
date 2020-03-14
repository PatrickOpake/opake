<?php

namespace Opake\ActivityLogger;

use Opake\ActivityLogger\Formatter\ArrayRowFormatter;
use Opake\Helper\TimeFormat;
use Opake\Model\Patient;

class FormatterHelper
{

	public static function formatCountryName($pixie, $countryId)
	{
		if ($countryId) {
			$row = $pixie->db->query('select')
				->table('country')
				->fields('name')
				->where('id', $countryId)
				->execute()->current();

			if ($row) {
				return $row->name;
			}
		}

		return $countryId;
	}

	public static function formatUserStatus($value)
	{
		if ($value === 'active') {
			return 'Active';
		}

		if ($value === 'inactive') {
			return 'Inactive';
		}

		return 'Unknown';
	}

	public static function formatUserProfession($pixie, $professionId)
	{
		if ($professionId) {
			$row = $pixie->db->query('select')
				->table('profession')
				->fields('name')
				->where('id', $professionId)
				->execute()->current();

			if ($row) {
				return $row->name;
			}
		}

		return $professionId;
	}

	public static function formatUserRole($pixie, $roleId)
	{
		if ($roleId) {
			$row = $pixie->db->query('select')
				->table('role')
				->fields('name')
				->where('id', $roleId)
				->execute()->current();

			if ($row) {
				return $row->name;
			}
		}

		return $roleId;
	}

	public static function formatSitesList($pixie, $sites)
	{
		if ($sites && is_array($sites)) {
			$rows = $pixie->db->query('select')
				->table('site')
				->fields('name')
				->where('id', 'IN', $pixie->db->expr('(' . implode(',', $sites) . ')'))
				->execute();

			$result = [];
			foreach ($rows as $row) {
				$result[] = $row->name;
			}

			return implode(', ', $result);
		}

		return '';
	}

	public static function formatDepartmentsList($pixie, $departments)
	{
		if (is_array($departments)) {
			$rows = $pixie->db->query('select')
				->table('department')
				->fields('name')
				->where('id', 'IN', $pixie->db->expr('(' . implode(',', $departments) . ')'))
				->execute();

			$result = [];
			foreach ($rows as $row) {
				$result[] = $row->name;
			}

			return implode(', ', $result);
		}

		return '';
	}

	public static function formatLocationsList($pixie, $locations)
	{
		if (is_array($locations)) {
			$rows = $pixie->db->query('select')
				->table('location')
				->fields('name')
				->where('id', 'IN', $pixie->db->expr('(' . implode(',', $locations) . ')'))
				->execute();

			$result = [];
			foreach ($rows as $row) {
				$result[] = $row->name;
			}

			return implode(', ', $result);
		}

		return '';
	}

	public static function formatUser($pixie, $userId)
	{
		if ($userId) {
			$row = $pixie->db->query('select')
				->table('user')
				->fields('first_name', 'last_name', 'id')
				->where('id', $userId)
				->execute()->current();

			if ($row) {
				return $row->first_name . ' ' . $row->last_name;
			}
		}

		return $userId;
	}

	public static function formatOrganization($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->table('organization')
				->fields('name')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				return $row->name;
			}
		}

		return $id;
	}


	public static function formatUsersList($pixie, $value)
	{
		if ($value) {
			$rows = $pixie->db->query('select')
				->table('user')
				->fields('first_name', 'last_name', 'id')
				->where('id', 'IN', $pixie->db->expr('(' . implode(',', $value) . ')'))
				->execute();

			$names = [];
			foreach ($rows as $row) {
				$names[] = $row->first_name . ' ' . $row->last_name;
			}

			return implode(', ', $names);
		}

		return '';
	}

	public static function formatProcedure($pixie, $procedureId)
	{
		if ($procedureId) {
			$row = $pixie->db->query('select')
				->table('case_type')
				->fields('name', 'code')
				->where('id', $procedureId)
				->execute()->current();

			if ($row) {
				return $row->code . ' - ' . $row->name;
			}
		}

		return $procedureId;
	}

	public static function formatProceduresList($pixie, $ids)
	{
		if ($ids) {
			$rows = $pixie->db->query('select')
				->table('case_type')
				->fields('name', 'code')
				->where('id', 'IN', $pixie->db->expr('(' . implode(',', $ids) . ')'))
				->execute();

			$result = [];
			if ($rows) {
				foreach ($rows as $row) {
					$result[] = $row->code . ' - ' . $row->name;
				}
			}

			return implode(', ', $result);
		}

		return $ids;
	}

	public static function formatCondition($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->table('condition')
				->fields('desc', 'code')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				return $row->code . ' - ' . $row->desc;
			}
		}

		return $id;
	}

	public static function formatModifier($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->table('modifier')
				->fields('desc', 'code')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				return $row->code . ' - ' . $row->desc;
			}
		}

		return $id;
	}

	public static function formatVendor($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->table('vendor')
				->fields('name')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				return $row->name;
			}
		}

		return $id;
	}

	public static function formatCPT($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->table('cpt')
				->fields('name', 'code')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				return $row->code . ' - ' . $row->name;
			}
		}

		return $id;
	}

	public static function formatCPTList($pixie, $cpts)
	{

		if ($cpts) {
			$rows = $pixie->db->query('select')
				->table('cpt')
				->fields('name', 'code')
				->where('id', 'IN', $pixie->db->expr('(' . implode(',', $cpts) . ')'))
				->execute();

			$result = [];
			if ($rows) {
				foreach ($rows as $row) {
					$result[] = $row->code . ' - ' . $row->name;
				}
			}

			return implode(', ', $result);
		}

		return '';
	}

	public static function formatPlaceOfService($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->table('place_service')
				->fields('name', 'code')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				return $row->code . ' - ' . $row->name;
			}
		}

		return $id;
	}

	public static function formatDischarge($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->table('discharge')
				->fields('desc', 'code')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				return $row->code . ' - ' . $row->desc;
			}
		}

		return $id;
	}

	public static function formatICD($pixie, $icdId)
	{
		if ($icdId) {
			$row = $pixie->db->query('select')
				->table('icd')
				->fields('desc', 'code')
				->where('id', $icdId)
				->execute()->current();

			if ($row) {
				return $row->code . ' - ' . $row->desc;
			}
		}

		return $icdId;
	}

	public static function formatICDList($pixie, $icdIds)
	{
		if ($icdIds) {
			$rows = $pixie->db->query('select')
				->table('icd')
				->fields('desc', 'code')
				->where('id', 'IN', $pixie->db->expr('(' . implode(',', $icdIds) . ')'))
				->execute();
			$result = [];

			if ($rows) {
				foreach ($rows as $row) {
					$result[] = $row->code . ' - ' . $row->desc;
				}
			}

			return implode(', ', $result);
		}

		return '';
	}

	public static function formatDRGList($pixie, $ids)
	{
		if ($ids) {
			$rows = $pixie->db->query('select')
				->table('drg')
				->fields('title', 'code')
				->where('id', 'IN', $pixie->db->expr('(' . implode(',', $ids) . ')'))
				->execute();
			$result = [];

			if ($rows) {
				foreach ($rows as $row) {
					$result[] = $row->code . ' - ' . $row->title;
				}
			}

			return implode(', ', $result);
		}

		return '';
	}

	public static function formatAPCList($pixie, $ids)
	{
		if ($ids) {
			$rows = $pixie->db->query('select')
				->table('apc')
				->fields('title', 'code')
				->where('id', 'IN', $pixie->db->expr('(' . implode(',', $ids) . ')'))
				->execute();
			$result = [];

			if ($rows) {
				foreach ($rows as $row) {
					$result[] = $row->code . ' - ' . $row->title;
				}
			}

			return implode(', ', $result);
		}

		return '';
	}

	public static function formatOnOff($value)
	{
		if ($value) {
			return 'On';
		}

		return 'Off';
	}

	public static function formatYesNo($value)
	{
		if ($value) {
			return 'Yes';
		}

		return 'No';
	}

	public static function formatCaseColors($pixie, $value)
	{
		if ($value) {
			$colors = \Opake\Model\Cases\Setting::getColors();
			$rows = $pixie->db->query('select')
				->table('user')
				->fields('first_name', 'last_name', 'id')
				->where('id', 'IN', $pixie->db->expr('(' . implode(',', array_keys($value)) . ')'))
				->execute();

			$names = [];
			foreach ($rows as $row) {
				$names[$row->id] = $row->first_name . ' ' . $row->last_name;
			}
			$labels = [];
			foreach ($value as $userId => $colorCode) {
				$labels[] = ((isset($names[$userId])) ? $names[$userId] : $userId) . ': ' . (isset($colors[$colorCode]) ? $colors[$colorCode] : $colorCode);
			}

			return implode(', ', $labels);
		}

		return '';
	}

	public static function formatCaseBlockTiming($value)
	{
		$timings = \Opake\Model\Cases\Setting::getBlockTimings();
		if (isset($timings[$value])) {
			return $timings[$value];
		}

		return $value;
	}

	public static function formatDateAndTime($value)
	{
		$dt = TimeFormat::fromDBDatetime($value);
		return (string)TimeFormat::getDateTime($dt);
	}

	public static function formatDate($value)
	{
		$dt = TimeFormat::fromDBDate($value);
		return (string)TimeFormat::getDate($dt);
	}

	public static function formatTime($value)
	{
		$dt = TimeFormat::fromDBTime($value);
		return (string)TimeFormat::getTime($dt);
	}

	public static function formatPatientName($pixie, $value)
	{
		if ($value) {
			$row = $pixie->db->query('select')
				->table('patient')
				->fields('first_name', 'last_name', 'id')
				->where('id', $value)
				->execute()->current();

			if ($row) {
				return $row->first_name . ' ' . $row->last_name;
			}
		}

		return $value;
	}

	public static function formatLocation($pixie, $locationId)
	{
		if ($locationId) {
			$row = $pixie->db->query('select')
				->table('location')
				->fields('name')
				->where('id', $locationId)
				->execute()->current();

			if ($row) {
				return $row->name;
			}
		}

		return $locationId;
	}

	public static function formatColor($value)
	{
		$colors = \Opake\Model\Cases\Setting::getColors();
		if (isset($colors[$value])) {
			return $colors[$value];
		}

		return $value;
	}

	public static function formatRecurrenceWeekNumber($value)
	{
		if ($value == 5) {
			return 'Last';
		}

		return $value;
	}

	public static function formatRecurrenceMonthlyDay($value)
	{
		if ($value == 0) {
			return '';
		}

		return $value;
	}

	public static function formatRecurrenceWeekDays($value)
	{
		$days = [
			1 => 'Mo',
			2 => 'Tu',
			3 => 'We',
			4 => 'Th',
			5 => 'Fr',
			6 => 'Sa',
			7 => 'Su'
		];

		if (!is_array($value)) {
			if (is_string($value) && $value) {
				$value = unserialize($value);
			} else {
				return '';
			}
		}

		$result = [];
		foreach ($value as $index) {
			if (isset($days[$index])) {
				$result[] = $days[$index];
			}
		}

		return implode(', ', $result);
	}

	public static function formatRecurrenceFrequency($value)
	{
		$freq = [
			1 => 'Every week',
			2 => 'Every month',
			3 => 'Custom'
		];

		if (isset($freq[$value])) {
			return $freq[$value];
		}

		return $value;
	}

	public static function formatLanguage($pixie, $languageId)
	{
		if ($languageId) {
			$row = $pixie->db->query('select')
				->table('language')
				->fields('name')
				->where('id', $languageId)
				->execute()->current();

			if ($row) {
				return $row->name;
			}
		}

		return $languageId;
	}

	public static function formatKeyValueSource($value, $source)
	{
		if (isset($source[$value])) {
			return $source[$value];
		}

		if (!$value) {
			return '';
		}

		return $value;
	}

	public static function formatKeyValueSourceMultiple($value, $source)
	{
		if (!is_array($value)) {
			return '';
		}

		$labels = [];
		foreach ($value as $id) {
			if (isset($source[$id])) {
				$labels[] = $source[$id];
			}
		}

		return implode(', ', $labels);
	}

	public static function formatStateName($pixie, $id)
	{
		if (!is_numeric($id)) {
			return $id;
		}


		if ($id) {
			$row = $pixie->db->query('select')
				->table('geo_state')
				->fields('name')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				return $row->name;
			}
		}

		return $id;
	}

	public static function formatCityName($pixie, $id)
	{
		if (!is_numeric($id)) {
			return $id;
		}

		if ($id) {
			$row = $pixie->db->query('select')
				->table('geo_city')
				->fields('name')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				return $row->name;
			}
		}

		return $id;
	}

	public static function formatInsuranceCompanyName($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->table('insurance_payor')
				->fields('name')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				return $row->name;
			}
		}

		return $id;
	}

	public static function formatFormUploadStatus($value)
	{
		$statuses = [
			0 => 'Not Completed',
			1 => 'Completed',
		];

		if (isset($statuses[$value])) {
			return $statuses[$value];
		}

		return $value;
	}

	public static function formatInventoryHCPCSName($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->table('inventory')
				->fields('name', 'hcpcs')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				return $row->hcpcs . ' - ' . $row->name;
			}
		}

		return $id;
	}

	public static function formatInventoryItemName($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->table('inventory')
				->fields('name')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				return $row->name;
			}
		}

		return $id;
	}

	public static function formatArrayOfChanges($pixie, $data, $formatterClass)
	{
		if ($data) {
			$result = [];
			$result['type'] = 'changes';
			$result['data'] = [];
			foreach ($data as $row) {
				/** @var ArrayRowFormatter $formatter */
				$formatter = new $formatterClass($pixie, $row);
				if (!($formatter instanceof ArrayRowFormatter)) {
					throw new \Exception('Passed row formatter is not an instance of ArrayRowFormatter');
				}

				$label = $formatter->getFullLabel();
				$changes = $formatter->getFormattedData();

				$result['data'][] = ['label' => $label, 'data' => $changes];
			}

			return $result;
		}

		return '';
	}

	public static function formatKeyValue($pixie, $data, $formatterClass)
	{
		$formatter = new $formatterClass($pixie, $data);
		return [
			'type' => 'keyValue',
			'data' => $formatter->getFormattedData()
		];
	}

	public static function formatInventoryType($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->table('inventory_type')
				->fields('name')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				return $row->name;
			}
		}

		return $id;
	}

	public static function formatChartGroupList($pixie, $chartGroups)
	{
		if ($chartGroups && is_array($chartGroups)) {
			$rows = $pixie->db->query('select')
				->table('forms_chart_group')
				->fields('name')
				->where('id', 'IN', $pixie->db->arr($chartGroups))
				->execute();

			$result = [];
			foreach ($rows as $row) {
				$result[] = $row->name;
			}

			return implode(', ', $result);
		}

		return '';
	}

	public static function formatChartGroupName($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->table('forms_chart_group')
				->fields('name')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				return $row->name;
			}
		}

		return $id;
	}

	public static function formatChartsList($pixie, $charts)
	{
		if ($charts && is_array($charts)) {
			$rows = $pixie->db
				->query('select')
				->table('forms_document')
				->fields('name')
				->where('id', 'IN', $pixie->db->arr($charts))
				->execute();

			$result = [];
			foreach ($rows as $row) {
				$result[] = $row->name;
			}

			return implode(', ', $result);
		}

		return '';
	}

	public static function formatInventoryItemNameList($pixie, $inventoryItemIds)
	{
		if ($inventoryItemIds && is_array($inventoryItemIds)) {

			$rows = $pixie->db->query('select')
				->table('inventory')
				->fields('name', 'item_number')
				->where('id', 'IN', $pixie->db->arr($inventoryItemIds))
				->execute();

			$result = [];
			foreach ($rows as $row) {

				$nameParts = [];
				if ($row->item_number) {
					$nameParts[] = $row->item_number;
				}
				if ($row->name) {
					$nameParts[] = $row->name;
				}

				$result[] = implode(' - ', $nameParts);
			}

			return implode(', ', $result);
		}

		return '';
	}

	public static function formatPhone($value)
	{
		if(!$value) {
			return '';
		}
		return substr($value, 0, 3) . '-' . substr($value, 3, 3) . '-' . substr($value, 6, 4);
	}

}
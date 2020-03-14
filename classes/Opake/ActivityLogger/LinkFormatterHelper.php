<?php

namespace Opake\ActivityLogger;

class LinkFormatterHelper
{
	public static function formatLink($title, $url)
	{
		return [
			'type' => 'link',
			'title' => $title,
			'url' => $url
		];
	}

	public static function formatLinksList($links)
	{
		return [
			'type' => 'link_list',
		    'links' => $links
		];
	}

	public static function formatUserLink($pixie, $userId)
	{
		if ($userId) {
			$row = $pixie->db->query('select')
				->table('user')
				->fields('first_name', 'last_name', 'id', 'organization_id')
				->where('id', $userId)
				->execute()->current();

			if ($row) {
				$title = $row->first_name . ' ' . $row->last_name;
				$url = '/clients/users/' . $row->organization_id . '/view/' . $userId;
				return static::formatLink($title, $url);
			}
		}

		return $userId;
	}

	public static function formatUploadedFileLink($pixie, $fileId)
	{
		if ($fileId) {
			$file = $pixie->orm->get('UploadedFile', $fileId);
			if ($file->loaded()) {
				return static::formatLink('File', $file->getWebPath());
			}
		}

		return $fileId;
	}

	public static function formatReportLink($pixie, $reportId)
	{
		if ($reportId) {
			$row = $pixie->db->query('select')
				->fields('case.organization_id')
				->table('case_op_report')
				->join('case', ['case_op_report.case_id', 'case.id'], 'inner')
				->where('case_op_report.id', $reportId)
				->execute()->current();

			if ($row) {
				$url = '/operative-reports/my/' . $row->organization_id . '/view/' . $reportId;
				return static::formatLink($reportId, $url);
			}
		}

		return $reportId;
	}

	public static function formatCaseLink($pixie, $caseId)
	{
		if ($caseId) {
			$row = $pixie->db->query('select')
				->fields('case.organization_id')
				->table('case')
				->where('case.id', $caseId)
				->execute()->current();
			if ($row) {
				$url = '/cases/' . $row->organization_id . '/cm/' . $caseId;
				return static::formatLink($caseId, $url);
			}
		}

		return $caseId;
	}

	public static function formatCasesLinkList($pixie, $caseIds)
	{
		if ($caseIds) {

			$rows = $pixie->db->query('select')
				->fields('case.id', 'case.organization_id')
				->table('case')
				->where('case.id', 'IN', $pixie->db->arr($caseIds))
				->execute();

			$links = [];
			$orgs = [];

			foreach ($rows as $row) {
				$orgs[$row->id] = $row->organization_id;
			}

			foreach ($caseIds as $caseId) {
				$link = [];
				$link['title'] = $caseId;
				if (isset($orgs[$caseId])) {
					$link['url'] = '/cases/' . $orgs[$caseId] . '/cm/' . $caseId;
				}
				$links[] = $link;
			}

			return static::formatLinksList($links);
		}

		return '';
	}

	public static function formatBookingLink($pixie, $bookingId)
	{
		if ($bookingId) {
			$row = $pixie->db->query('select')
				->fields('booking_sheet.organization_id')
				->table('booking_sheet')
				->where('booking_sheet.id', $bookingId)
				->execute()->current();
			if ($row) {
				$url = '/booking/' . $row->organization_id . '/view/' . $bookingId;
				return static::formatLink($bookingId, $url);
			}
		}

		return $bookingId;
	}

	public static function formatBookingsLinkList($pixie, $bookingIds)
	{
		if ($bookingIds) {

			$rows = $pixie->db->query('select')
				->fields('booking_sheet.id', 'booking_sheet.organization_id')
				->table('booking_sheet')
				->where('booking_sheet.id', 'IN', $pixie->db->arr($bookingIds))
				->execute();

			$links = [];
			$orgs = [];

			foreach ($rows as $row) {
				$orgs[$row->id] = $row->organization_id;
			}

			foreach ($bookingIds as $bookingId) {
				$link = [];
				$link['title'] = $bookingId;
				if (isset($orgs[$bookingId])) {
					$link['url'] = '/booking/' . $orgs[$bookingId] . '/view/' . $bookingId;
				}
				$links[] = $link;
			}

			return static::formatLinksList($links);
		}

		return '';
	}

	public static function formatPatientLink($pixie, $patientId)
	{
		if ($patientId) {
			$row = $pixie->db->query('select')
				->fields('patient.organization_id', 'patient.first_name', 'patient.last_name')
				->table('patient')
				->where('patient.id', $patientId)
				->execute()->current();

			if ($row) {
				$parts = [];
				if ($row->first_name) {
					$parts[] = $row->first_name;
				}
				if ($row->last_name) {
					$parts[] = $row->last_name;
				}
				$name = (!$parts) ? $patientId : implode(' ', $parts);
				$url = '/patients/' . $row->organization_id . '/view/' . $patientId;
				return static::formatLink($name, $url);
			}
		}

		return $patientId;
	}

	public static function formatOrganizationLink($id)
	{
		return static::formatLink($id, '/clients/sites/' . $id);
	}

	public static function formatPrefCardLink($pixie, $id, $card)
	{
		$row = $pixie->db->query('select')
			->fields('user.organization_id')
			->table('pref_card_staff')
			->join('user', ['user.id', 'pref_card_staff.user_id'])
			->where('pref_card_staff.id', $id)
			->execute()->current();

		if ($row) {
			$url = '/cards/staff/' . $row->organization_id . '/view/' . $id;
			return static::formatLink($id, $url);
		}

		return $id;
	}

	public static function formatInventoryItemLink($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->fields('inventory.organization_id')
				->table('inventory')
				->where('inventory.id', $id)
				->execute()->current();

			if ($row) {
				$url = '/inventory/' . $row->organization_id . '/view/' . $id;
				return static::formatLink($id, $url);
			}
		}

		return $id;
	}

	public static function formatOutgoingOrderLink($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->fields('order_outgoing.organization_id')
				->table('order_outgoing')
				->where('order_outgoing.id', $id)
				->execute()->current();

			if ($row) {
				$url = '/orders/outgoing/' . $row->organization_id . '/view/' . $id;
				return static::formatLink($id, $url);
			}
		}

		return $id;
	}

	public static function formatOrderLink($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->fields('order.organization_id')
				->table('order')
				->where('order.id', $id)
				->execute()->current();

			if ($row) {
				$url = '/orders/' . $row->organization_id . '/view/' . $id;
				return static::formatLink($id, $url);
			}
		}

		return $id;
	}

	public static function formatOpReportTemplateLink($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->fields('organization_id', 'name')
				->table('case_op_report_future')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				$url = '/operative-reports/' . $row->organization_id . '/view/' . $id;
				return static::formatLink($row->name, $url);
			}
		}

		return $id;
	}

	public static function formatSiteLink($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->fields('organization_id')
				->table('site')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				$url = '/clients/sites/' . $row->organization_id . '/view/' . $id;
				return static::formatLink($id, $url);
			}
		}

		return $id;
	}

	public static function formatCaseChartFileLink($pixie, $caseChartId)
	{
		if ($caseChartId) {
			$row = $pixie->db
				->query('select')
				->table('case_chart')
				->fields('uploaded_file_id', 'name')
				->where('id', $caseChartId)
				->execute()
				->current();

			if ($row) {
				$file = $pixie->orm->get('UploadedFile', $row->uploaded_file_id);
				if ($file->loaded()) {
					return static::formatLink($row->name, $file->getWebPath());
				} else {
					return $row->uploaded_file_id;
				}
			}
		}

		return $caseChartId;
	}

	public static function formatChartLink($pixie, $id)
	{
		if ($id) {
			$row = $pixie->db->query('select')
				->fields('name', 'organization_id', 'uploaded_file_id')
				->table('forms_document')
				->where('id', $id)
				->execute()->current();

			if ($row) {
				if ($row->uploaded_file_id) {
					$model = $pixie->orm->get('UploadedFile', $row->uploaded_file_id);
					if (!$model->loaded()) {
						return $row->name;
					}

					return static::formatLink($row->name, $model->getWebPath());
				}

				$url = '/settings/forms/charts/' . $row->organization_id . '/view/' . $id;
				return static::formatLink($row->name, $url);

			}
		}

		return $id;
	}

	public static function formatVerificationLink($pixie, $registrationId)
	{
		if ($registrationId) {
			$row = $pixie->db->query('select')
				->fields('case_registration.organization_id')
				->table('case_registration')
				->where('case_registration.id', $registrationId)
				->execute()->current();
			if ($row) {
				$url = '/verification/' . $row->organization_id . '/view/' . $registrationId;
				return static::formatLink($registrationId, $url);
			}
		}

		return $registrationId;
	}
}


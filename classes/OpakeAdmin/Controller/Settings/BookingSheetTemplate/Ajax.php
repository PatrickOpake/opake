<?php

namespace OpakeAdmin\Controller\Settings\BookingSheetTemplate;

use Opake\Exception\BadRequest;
use Opake\Exception\PageNotFound;
use Opake\Exception\ValidationError;
use Opake\Model\BookingSheetTemplate;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));
	}

	public function actionList()
	{
		$this->checkAccess('booking_sheet_template', 'view');

		$templates = $this->orm->get('BookingSheetTemplate')
			->where('organization_id', $this->org->id())
			->order_by('type')
			->find_all();

		$templateModels = [];
		$hasDefault = false;
		foreach ($templates as $templateModel) {
			$templateModels[] = $templateModel;
			if ($templateModel->type == BookingSheetTemplate::TYPE_DEFAULT) {
				$hasDefault = true;
			}
		}

		if (!$hasDefault) {
			array_unshift(
				$templateModels,
				BookingSheetTemplate::createDefaultBookingSheetTemplate()
			);
		}

		$result = [];
		foreach ($templateModels as $model) {
			$result[] = $model->getFormatter('List')->toArray();
		}

		$this->result = $result;
	}

	public function actionUpdate()
	{
		$this->checkAccess('booking_sheet_template', 'edit');

		try {

			$type = $this->request->post('type');
			$data = $this->getData();

			if (!isset($data->id)) {
				if ($data->type != BookingSheetTemplate::TYPE_DEFAULT) {
					throw new PageNotFound('Unknown template');
				}

				$template = $this->orm->get('BookingSheetTemplate')
					->where('organization_id', $this->org->id())
					->where('type', BookingSheetTemplate::TYPE_DEFAULT)
					->find();

				if (!$template->loaded()) {
					$template = BookingSheetTemplate::createDefaultBookingSheetTemplate();
					$template->organization_id = $this->org->id();
					$template->save();
					$template->createDefaultFields();
				}

			} else {
				$template = $this->orm->get('BookingSheetTemplate')
					->where('organization_id', $this->org->id())
					->where('id', $data->id)
					->find();

				if (!$template->loaded()) {
					throw new PageNotFound('Template is not found');
				}
			}

			if ($type === 'rename') {

				$form = new \OpakeAdmin\Form\Settings\BookingSheetTemplate\RenameForm($this->pixie, $template);
				$form->load($data);
				if (!$form->isValid()) {
					$this->result = [
						'success' => false,
					    'errors' => [$form->getFirstErrorMessage()]
					];
					return;
				}
				$form->save();

			} else if ($type === 'assign') {

				$form = new \OpakeAdmin\Form\Settings\BookingSheetTemplate\AssignForm($this->pixie, $template);
				$form->load($data);
				if (!$form->isValid()) {
					$this->result = [
						'success' => false,
						'errors' => [$form->getFirstErrorMessage()]
					];
					return;
				}
				$form->save();

			}

			$this->result = [
				'success' => true,
			    'id' => $template->id()
			];

		} catch (\Exception $e) {
			$this->result = [
				'success' => false,
			    'errors' => [$e->getMessage()]
			];
		}
	}

	public function actionDelete()
	{
		$this->checkAccess('booking_sheet_template', 'delete');

		$id = $this->request->param('subid');
		$template = $this->orm->get('BookingSheetTemplate')
			->where('organization_id', $this->org->id())
			->where('id', $id)
			->find();
		if ($template->loaded()) {
			$template->delete();
		}

		$this->result = [
			'success' => true
		];
	}

	public function actionGetDefaultFields()
	{
		$this->checkAccess('booking_sheet_template', 'view');

		$this->result = BookingSheetTemplate::getDefaultFieldsConfig();
	}

	public function actionGetTemplate()
	{
		$this->checkAccess('booking_sheet_template', 'view');

		$template = $this->loadTemplate($this->request->param('subid'));
		if (!$template) {
			throw new PageNotFound('Unknown template');
		}

		$this->result = $template->getFormatter('Template')
			->toArray();
	}

	public function actionSaveTemplate()
	{
		$this->checkAccess('booking_sheet_template', 'edit');

		try {

			$data = $this->getData();

			$templateId = $this->request->param('subid');

			if ($templateId) {
				$template = $this->loadTemplate($templateId);
				if (!$template) {
					throw new PageNotFound('Unknown template');
				}
			} else {
				$template = $this->orm->get('BookingSheetTemplate');
				$template->organization_id = $this->org->id();
				$template->type = BookingSheetTemplate::TYPE_CUSTOM;
				$template->is_all_sites = 1;
			}

			if (!$data->name) {
				throw new ValidationError('You must specify name');
			}

			$template->name = $data->name;
			$template->save();
			$template->updateFields($data->fields);

			$this->result = [
				'success' => true
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);

			$this->result = [
				'success' => false,
			    'errors' => [$e->getMessage()]
			];
		}
	}

	protected function loadTemplate($id)
	{
		$this->checkAccess('booking_sheet_template', 'view');

		if ($id === 'default') {
			$template = $this->orm->get('BookingSheetTemplate')
				->where('organization_id', $this->org->id())
				->where('type', BookingSheetTemplate::TYPE_DEFAULT)
				->find();
			if (!$template->loaded()) {
				$template = BookingSheetTemplate::createDefaultBookingSheetTemplate();
				$template->organization_id = $this->org->id();
			}

			return $template;
		}

		$template = $this->orm->get('BookingSheetTemplate')
			->where('organization_id', $this->org->id())
			->where('id', $id)
			->find();

		if ($template->loaded()) {
			return $template;
		}

		return null;
	}
}
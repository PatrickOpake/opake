<?php

namespace Opake\Model\Patient;

use Opake\Model\AbstractModel;

class Portal extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'patient_portal';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'title' => null,
		'alias' => null,
		'active' => null,
		'icon_file_id' => null,
	];

	protected $belongs_to = [
		'organization' => [
			'model' => 'Organization',
			'key' => 'organization_id',
		],
		'icon_file' => [
			'model' => 'UploadedFile_Image',
			'key' => 'icon_file_id',
			'cascade_delete' => true
		]
	];

	public function getFullUrl()
	{
		$portalBaseUrl = rtrim($this->pixie->config->get('app.patient_portal_web'), '/');
		$portalOrgUrl = $portalBaseUrl . '/' . $this->alias;

		return $portalOrgUrl;
	}

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('title')->rule('filled')->rule('min_length', 2)->error('Invalid Name');
		$validator->field('alias')->rule('filled')->rule('min_length', 2)->error('Invalid URL Alias');

		return $validator;
	}

	public function getIcon($size = NULL)
	{
		if ($photo = $this->getIconModel()) {
			return $photo->getThumbnailWebPath($size);
		}

		if ($size) {
			return '/common/i/opake_logo_' . $size . '.png';
		}

		return '/common/i/opake_logo.png';
	}

	public function toArray()
	{
		$portalBaseUrl = rtrim($this->pixie->config->get('app.patient_portal_web'), '/');
		$portalOrgUrl = $portalBaseUrl . '/' . $this->alias;

		return [
			'id' => $this->id(),
			'organization_id' => $this->organization_id,
			'title' => $this->title,
			'alias' => $this->alias,
			'portal_base_url' => $portalBaseUrl,
			'portal_org_url' => $portalOrgUrl,
			'active' => (bool) $this->active,
			'icon' => $this->getIcon('default'),
			'icon_file_id' => $this->icon_file_id
		];
	}


	/**
	 * @return \Opake\Model\UploadedFile\Image
	 */
	protected function getIconModel()
	{
		if ($this->icon_file_id) {
			if ($this->icon_file->loaded()) {
				return $this->icon_file;
			}
			if (!$this->icon_file->loaded() && $this->icon_file_id) {
				$model = $this->pixie->orm->get('UploadedFile_Image', $this->icon_file_id);
				if ($model->loaded()) {
					return $model;
				}
			}
		}


		return null;
	}

	public function isPublished()
	{
		if ($this->loaded() && $this->organization && $this->organization->loaded()) {
			if ($this->active) {
				$organizationPermissions = new \Opake\Permissions\Organization\OrganizationLevel($this->organization);
				$permissions = $organizationPermissions->getOrganizationPermissions();

				if (!empty($permissions['patient_portal.login'])) {
					return true;
				}
			}
		}

		return false;
	}

}
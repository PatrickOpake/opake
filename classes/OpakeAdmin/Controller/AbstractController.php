<?php

namespace OpakeAdmin\Controller;

abstract class AbstractController extends \Opake\Controller\AbstractController
{

	/**
	 * Current organization
	 *
	 * @var \Opake\Model\Organization
	 */
	protected $org;

	/**
	 * @param $section
	 * @param $action
	 * @param null $model
	 * @return mixed
	 * @throws \Opake\Exception\Forbidden
	 */
	protected function checkAccess($section, $action, $model = null)
	{
		if ($access = $this->pixie->permissions->checkAccess($section, $action, $model)) {
			return $access;
		}
		throw new \Opake\Exception\Forbidden();
	}

	/**
	 * @param string $section
	 * @param string $action
	 * @return \Opake\Permissions\AccessLevel
	 */
	protected function getAccessLevel($section, $action)
	{
		return $this->pixie->permissions->getAccessLevel($section, $action);
	}

	protected function iniOrganization($id)
	{
		$this->org = $this->orm->get('organization', $id);
		if ($this->org->loaded()) {
			$user = $this->logged();
			if (!$user) {
				throw new \Opake\Exception\Forbidden('Not authorized');
			}
			if ($this->org->id != $user->organization_id && !$user->isInternal()) {
				throw new \Opake\Exception\Forbidden();
			}
		} else {
			throw new \Opake\Exception\PageNotFound();
		}
	}
}

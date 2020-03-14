<?php

namespace OpakeAdmin\Service\ASCX12\E837I\Request\Headers;

class GSHeader extends \OpakeAdmin\Service\ASCX12\General\Request\GSHeader
{
	/**
	 * @return string
	 */
	protected function getVersion()
	{
		return '005010X223A1';
	}
}
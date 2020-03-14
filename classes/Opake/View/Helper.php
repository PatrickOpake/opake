<?php

namespace Opake\View;


class Helper extends \PHPixie\View\Helper
{

	/**
	 * Constructs the view helper
	 * @param \Opake\Application $pixie Pixie dependency container
	 */
	public function __construct($pixie)
	{
		parent::__construct($pixie);

		$this->aliases = array_merge($this->aliases, [

		]);
	}
}

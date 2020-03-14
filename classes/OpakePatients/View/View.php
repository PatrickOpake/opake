<?php

namespace OpakePatients\View;

use Opake\Extentions\Less\Compiler;

class View extends \Opake\View\View {

	public function setDefaultJsCss()
	{
		parent::setDefaultJsCss();

		$less = new Compiler($this->pixie, [
			$this->pixie->root_dir . 'apps/common/public/vendors/bootstrap/less/' => 'bootstrap',
			$this->pixie->root_dir . 'apps/common/assets/less/' => 'common'
		]);
		$less->force_compile = $this->forceCompileAndMinify;
		$less->compileFile('/assets/less/site.less', 'site.css');

		$this->addCSSList([
			// Compiled less
			'/css/site.css',
		]);

		$this->addJsList([
			// Vendors
			'/vendors/angular/angular-ui-router.min.js' => true,
		]);

		$this->addJSFromFolder('/js/app', false);
	}

}

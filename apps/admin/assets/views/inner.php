<?php include('_partial/header.php'); ?>

	<div class="site-body" sticky-scroll>
		<div alert-flash ng-animate message=""></div>
		<div class="container-fluid">
			<div class="inner-grid" ng-class="{'tablet': view.isTablet(), 'desktop': view.isPC()}" ng-cloak>
				<left-menu
						active-menu="leftMenuConfig.active"
						items="leftMenuConfig.items"
						menu-type="leftMenuConfig.type"
						show-params="leftMenuConfig.displayingParams"></left-menu>
				<div show-loading="showLeftMenu" without-spinner="true">
					<?php include(__DIR__ . '/_partial/top-menu.php'); ?>
					<div <?= $this->wrapContent ? ' class="content-wrap"' : '' ?>>
						<?= $this->getErrorsHtml() ?>
						<?= $this->getMessageHtml() ?>
						<?php if (isset($subview)): ?>
							<?php include(__DIR__ . sprintf('/%s.php', $subview)) ?>
						<?php endif ?>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php include('_partial/footer.php'); ?>
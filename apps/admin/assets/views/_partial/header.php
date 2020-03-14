<?php
/* @var $this \Opake\View\View */
/* @var $user \Opake\Model\User */
?>
<!DOCTYPE html>
<html lang="en" ng-app="opake">
<head>
	<title>Opake</title>
	<link rel="shortcut icon" href="/common/i/favicon.ico">
	<link rel="icon" type="image/png" href="/common/i/logo-blue-inverse.png" sizes="40x40">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?= $this->getCssHtml() ?>
	<?= $this->getJsHtml(true) ?>
</head>
<body>
<div class='site-header'>
	<div class='container-fluid'>
		<nav class="navbar" role="navigation">
			<menu-open-icon></menu-open-icon>
			<a class="navbar-brand" href="/?mainpage=1">
				<img src="<?= $_prepare_version_tag_url('/common/i/logo.png') ?>"/>
				<div class="logo-title">OPAKE</div>
			</a>
			<?php if (isset($loggedUser)) { ?>
				<?php if ($loggedUser->isInternal()) { ?>
					<div class="collapse navbar-collapse">
						<ul class="nav navbar-nav text-big">
							<?php if (!isset($topMenuActive)) $topMenuActive = null ?>
							<li <?= ($topMenuActive == 'clients') ? 'class="active"' : '' ?>><a
									href='/clients/'>Clients</a></li>
							<li <?= ($topMenuActive == 'inventory' ? 'class="active"' : '') ?>><a
									href='/inventory/internal'>Master Inventory</a></li>
							<li <?= ($topMenuActive == 'orders' ? 'class="active"' : '') ?>><a href='/orders/internal'>Purchase
									Orders</a></li>
							<li <?= ($topMenuActive == 'vendors' ? 'class="active"' : '') ?>><a
									href='/vendors/internal'>Vendors</a></li>
							<li <?= ($topMenuActive == 'analytics' ? 'class="active"' : '') ?>><a
									href='/analytics/internal/'>Analytics</a></li>
							<li <?= ($topMenuActive == 'patient-users' ? 'class="active"' : '') ?>><a
									href='/patient-users/internal/'>User Database</a></li>
							<li <?= ($topMenuActive == 'settings' ? 'class="active"' : '') ?>><a
									href='/settings/fields'>Settings</a></li>
						</ul>
					</div>
				<?php } ?>

				<div class="credentials">
					<div class="messaging-header reminder-header-button" uib-tooltip="Reminder" tooltip-class="white" tooltip-placement="bottom">
						<i class="icon-big-bell-white" ng-click="reminderWidgetService.toggleShowWidget()"></i>
						<span class="messages-badge" ng-if="reminderWidgetService.getUnreadSum()" ng-bind="reminderWidgetService.getUnreadSum()" ng-cloak></span>
					</div>
					<div class="calculator-header" ng-if="!view.isPhone()" calculator-icon>
						<i class="icon-calculator" uib-tooltip="Calculator" tooltip-class="white" tooltip-placement="bottom"></i>
					</div>
					<?php if ($_check_access('chat', 'messaging')): ?>
					<div ng-if="!view.isPhone()" class="messaging-header" uib-tooltip="{{ messaging.isShowWidget() ? 'Hide Messages' : 'Show Messages' }}" tooltip-class="white" tooltip-placement="bottom">
						<i class="icon-messaging" ng-click="messaging.toggleShowWidget()"></i>
						<span class="messages-badge" ng-if="messaging.getUnreadSum()" ng-bind="messaging.getUnreadSum()" ng-cloak></span>
					</div>
					<?php endif ?>
					<?php if ($_check_access('efax', 'view')): ?>
						<div ng-if="!view.isPhone()" class="messaging-header efax-header-button" uib-tooltip="eFax" tooltip-class="white" tooltip-placement="bottom">
							<i class="icon-fax-white" ng-click="efaxWidgetService.toggleShowWidget()"></i>
						</div>
					<?php endif ?>
					<span class="dropdown" uib-dropdown>
						<a href="#" class="dropdown-toggle" type="button" id="dropdown-account-menu"
						   uib-dropdown-toggle>
							<?php if ($loggedUser->photo_id): ?>
								<img class="user-photo" src="<?= $loggedUser->getPhoto('tiny') ?>" alt=""/>
							<?php else: ?>
								<span class="default-user-photo"></span>
							<?php endif ?>
							<span class="account">Hello, <?= $loggedUser->getFirstName() ?></span> <span
								class="caret"></span>
						</a>
						<ul class="dropdown-menu" aria-labelledby="dropdown-account-menu">
							<?php if ($loggedUser->isInternal()) : ?>
								<li><a href='/profiles/clients/view/<?= $loggedUser->organization_id ?>'>Profile</a>
								</li>
							<?php else: ?>
								<li>
									<a class="<?php echo $loggedUser->hasExpiredCredentials() ? 'invalid' : ''?> " href='/profiles/users/<?= $loggedUser->organization_id ?>/view/<?= $loggedUser->id ?>'>Profile</a>
								</li>
							<?php endif ?>
							<li><a href='/auth/logout/'>Sign out</a></li>
						</ul>
					</span>
					<?php if($loggedUser->hasExpiredCredentials()):?>
						<span class="icon"><i class="icon-red-warning"></i></span>
					<?php endif;?>

				</div>
			<?php } ?>
		</nav>
	</div>
	<reminder-widget></reminder-widget>

	<?php if ($_check_access('chat', 'messaging')): ?>
		<messaging-widget ng-if="messaging.isLoaded() && messaging.isShowWidget()"></messaging-widget>
	<?php endif ?>

	<calculator-widget ng-if="showCalculatorWidget"></calculator-widget>

	<?php if ($_check_access('efax', 'view')): ?>
	<efax-widget></efax-widget>
	<?php endif ?>
	<?= $this->getBreadcrumbs(); ?>
</div>

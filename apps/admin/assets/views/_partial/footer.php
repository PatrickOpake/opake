<?php if (empty($disableSiteFooter)): ?>
<div class='site-footer'>
	<div class='container-fluid'>
		<?php if (isset($loggedUser)) { ?>
			<div class='pull-left'>
				<a href='/page/contact'>Contact Us</a>
			</div>
		<?php } ?>
		<div class="pull-right text-right" ng-cloak>
			<div>Copyright &copy;{{::app.year}} OPAKE. All rights reserved.</div>
			<div>CPT copyright {{::app.year}} American Medical Association. All rights reserved.</div>
			<small>Version: {{::app.version}}</small>
		</div>
	</div>
</div>
<?php endif ?>

<?= $this->getJSHtml(false) ?>

<?php

$appInitData = [
	'debugmode' => \Opake\Helper\Config::get('app.debugmode'),
	'version' => $this->pixie->version,
	'versionTag' => $this->pixie->version_tag,
	'year' => date('Y'),
	'orgId' => (isset($org) && $org->id) ? $org->id : null,
	'loggedUser' => (isset($loggedUser)) ? $loggedUser->toArray() : null,
	'viewState' => (isset($loggedUser)) ? $loggedUser->getViewState() : null,
	'loggedUserPermissions' => $this->pixie->permissions->getPermissionConfig(),
	'inactivityReminder' => [
		'enabled' => $this->pixie->config->get('app.inactivity_reminder.enabled'),
		'logoutTime' => $this->pixie->config->get('app.inactivity_reminder.logoutTime')
	],
	'passwordChangeReminder' => [
		'daysCount' => $this->pixie->config->get('app.password_change_reminder.days_since_last_change')
	],
];

if (isset($loggedUser)) {
	$appInitData['leftMenuConfig'] = [
		'active' => $this->getActiveMenu(),
		'items' => $this->getJsMenuConfig(isset($org) ? $org : 0),
		'type' => $this->getMenu()->getMenuType(),
		'displayingParams' => [
			'showSideCalendar' => (!empty($this->showSideCalendar)) ? $this->showSideCalendar : 0,
			'showCaseListSideCalendar' => (!empty($this->showCaseListSideCalendar)) ? $this->showCaseListSideCalendar : 0,
			'showScheduleLegend' => (!empty($this->showScheduleLegend)) ? $this->showScheduleLegend : 0,
		]
	];
} else {
	$appInitData['leftMenuConfig'] = null;
}

?>
<script type="text/javascript">
	(function () {
		angular.module('opake').value('appInitData', <?= json_encode($appInitData) ?>);
	}());

	<?php if (isset($dictationEnabled)): ?>
	function NUSA_configure()
	{
		NUSA_enableAll = false;
		NUSA_applicationName = "<?= $dragonApplicationName; ?>";
		NUSA_userId = "<?= $dragonUserId; ?>";
		
	}
	<?php endif ?>
</script>
</body>
</html>
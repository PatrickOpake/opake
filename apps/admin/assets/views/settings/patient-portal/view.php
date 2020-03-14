<div ng-controller="PatientPortalSettingsCtrl as settingsVm" ng-init="settingsVm.init(<?= $org->id() ?>)">

	<ng-include ng-if="settingsVm.portalSettings" src="settingsVm.getView()"></ng-include>

</div>

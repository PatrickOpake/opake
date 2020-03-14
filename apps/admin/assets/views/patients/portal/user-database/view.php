<div ng-controller="PatientsPortalUserDatabaseViewCtrl as userVm" ng-init="userVm.init(<?= $user->id() ?>)">

	<ng-include ng-if="userVm.user" src="userVm.getView()"></ng-include>

</div>

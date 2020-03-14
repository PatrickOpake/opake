<div ng-controller="ClientProfileCtrl as profileVm" ng-init="profileVm.init()">

	<ng-include ng-if="profileVm.org" src="profileVm.getView()"></ng-include>

</div>
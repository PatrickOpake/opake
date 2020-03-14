<div ng-controller="ClientSiteCtrl as siteVm" ng-init="siteVm.init()">

	<ng-include ng-if="siteVm.site" src="siteVm.getView()"></ng-include>

</div>
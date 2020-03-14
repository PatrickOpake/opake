<div ng-controller="ClientSiteCtrl as siteVm" ng-init="siteVm.init(<?= $site->id() ?>)">

	<ng-include ng-if="siteVm.site" src="siteVm.getView()"></ng-include>

</div>

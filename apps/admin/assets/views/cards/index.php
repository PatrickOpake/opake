<div ng-controller="CardStaffListCrtl as listVm" ng-init="listVm.init()" class="content-block card-list" ng-cloak>
	<div class="card-list" ng-include="listVm.getView()"></div>
</div>
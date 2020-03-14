<div ng-controller="VendorCrtl as vendorVm" ng-init="vendorVm.init(<?= $vendor->id ?>)" class="vendor-profile">

	<ng-include ng-if="vendorVm.vendor" src="vendorVm.getView()"></ng-include>

</div>
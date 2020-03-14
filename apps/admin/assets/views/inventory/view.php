<div ng-controller="InventoryCrtl as invVm" ng-init="invVm.init(<?= $inventory->id ?>)" class="inventory-profile">

	<ng-include ng-if="invVm.inventory" src="invVm.getView()"></ng-include>

</div>

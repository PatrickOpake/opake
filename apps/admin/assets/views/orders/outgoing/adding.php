<div class="content-block order-outgoing-adding" ng-controller="OrderOutgoingAddingCrtl as addingVm" ng-init="addingVm.init(<?= $order->id ?>)" ng-cloak>
	<h3 class="title">Select Items:</h3>
	<ng-include src="view.get('inventory/filters.html')" onLoad="listVm = addingVm;"></ng-include>
	<div class="list-control">
		<a class="btn btn-primary" href="" ng-click="addingVm.save()">Next ></a>
	</div>

	<ng-include class="order-outgoing--selection" src="view.get('orders/selection.html')" onLoad="ctrl = addingVm;"></ng-include>
</div>
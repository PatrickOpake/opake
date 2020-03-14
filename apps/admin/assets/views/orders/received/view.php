<?php
/* @var $this \Opake\View\View */
?>
<div class="search-results order-received-view" ng-controller="OrderReceivedCrtl as orderVm" ng-init="orderVm.init(<?= $order->id ?>)" ng-cloak>
	<div class="data-row">
		<div>
			<h3 class="title">Order Info:</h3>
			<table>
				<tr>
					<td><label>Order ID:</label> {{ ::orderVm.order.id }}</td>
				</tr>
				<tr>
					<td><label>Date:</label> {{ ::orderVm.order.date }}</td>
					<td><label>Vendor:</label> {{ ::orderVm.order.vendor}}</td>
				</tr>
				<tr>
					<td><label># of Unique Items:</label> {{ orderVm.order.items.length }}</td>
					<td><label>Total # of Items:</label> {{ orderVm.order.getTotalItems() }}</td>
				</tr>
			</table>
		</div>

		<div ng-if="orderVm.order.items.length" class="control">
			<a ng-if="orderVm.canSelectOrder()" class="btn btn-danger place-order"  href="" ng-click="orderVm.selectOrder()">Select Order</a>
			<a ng-if="orderVm.canFinishOrder()" class="btn btn-success place-order" href="" ng-click="orderVm.finished()" ng-disabled="orderVm.order.isComplete()">Finished</a>
		</div>
	</div>

	<ng-include src="view.get('orders/received/inventory.html')" onLoad="ctrl = orderVm;"></ng-include>
</div>
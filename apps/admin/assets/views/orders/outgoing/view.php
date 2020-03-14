<div class="content-block order-outgoing-view" ng-controller="OrderOutgoingCrtl as orderVm" ng-init="orderVm.init(<?= $order->id ?>)" ng-cloak>
	<div class="data-row">
		<div>
			<h3 class="title">Order Info:</h3>
			<table>
				<tr>
					<td><label>Date:</label> {{ orderVm.order.date ? orderVm.order.date : 'Ready to be Placed' }}</td>
					<td><label>Vendors:</label> <span ng-repeat="group in orderVm.order.groups">{{ ::group.vendor.name }}{{ $last ? '' : ', ' }}</span></td>
				</tr>
				<tr>
					<td><label># of Unique Items:</label> {{ orderVm.order.getUniqueItems() }}</td>
					<td><label>Total # of Items:</label> {{ orderVm.order.getTotalItems() }}</td>
				</tr>
			</table>
		</div>
		<div class="control" ng-if="!orderVm.order.date">
			<a class="btn btn-success place-order" href="" ng-click="orderVm.place()">Place Order</a>
			<a class="btn btn-primary add-items" href="/orders/outgoing/{{ ::org_id }}/adding/{{ ::orderVm.order.id }}">+Add Items</a>
		</div>
	</div>

	<div class="headline" ng-repeat-start="group in orderVm.order.groups" ng-if="orderVm.order.groups.length > 0">{{ ::group.vendor.name }} <span ng-if="group.received_order_id" class="order-id">Order ID: {{::group.received_order_id}}</span></div>
	<table class="opake">
		<thead>
			<tr>
				<th></th>
				<th>Item</th>
				<th>Description</th>
				<th class="text-center">Par Min</th>
				<th class="text-center">In Stock</th>
				<th class="text-center">Ordering</th>
				<?php if ($order->isActive()) { ?>
					<th></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="item in group.items | orderBy: '-id'">
				<td><img src="{{ ::item.inventory.image }}" /></td>
				<td><a href="/inventory/{{ ::org_id }}/view/{{ ::item.inventory.id }}">{{ ::item.inventory.name }}</a></td>
				<td>{{ ::item.inventory.desc }}</td>
				<td class="text-center">{{ ::item.inventory.min_level }}</td>
				<td class="text-center">{{ ::item.inventory.stock }}</td>
				<?php if ($order->isActive()) { ?>
					<td class="text-center">
						<a href="#" editable-number="item.count" e-required e-min="1" onbeforesave="orderVm.updateCount(item, $data)">{{ item.count }}</a>
					</td>
					<td class="text-center">
						<a href="" class="remove" ng-click="orderVm.delete(item, group.items)"><i class="icon-remove"></i></a>
					</td>
				<?php } else { ?>
					<td class="text-center">{{ ::item.count }}</td>
				<?php } ?>
			</tr>
		</tbody>
	</table>
	<div ng-if="group.items.length === 0">No items found</div>
	<div class="text-right" ng-repeat-end>
		<button type="button" class="btn btn-success" ng-click="orderVm.export(group.vendor.id)">
			<i class="glyphicon glyphicon-export"></i> Export
		</button>
	</div>
	<div ng-if="orderVm.order.groups.length === 0">No items found</div>
</div>
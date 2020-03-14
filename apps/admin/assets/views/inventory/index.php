<?php
/* @var $this \Opake\View\View */
?>
<div ng-controller="InventoryListCrtl as listVm" ng-cloak>
	<div class="opk-tabs inventory-search--tabs">
		<ul class="nav nav-tabs nav-justified">
			<li ng-class="{active: listVm.alert === ''}">
				<a ng-click="listVm.setAlert('')" href=""><!--<i class="icon-inventory-all"></i>-->All Inventory</a>
			</li>
			<!--<li /><a update-param="alert" href="">Missing Items</a></li>-->
			<li ng-class="{active: listVm.alert === 3}">
				<a ng-click="listVm.setAlert(3)" href="">
					<!--<i class="icon-inventory-all"></i>-->New Items
					<span class="badge" ng-if="listVm.alert_counts[3]">{{ listVm.alert_counts[3] }}</span>
				</a>
			</li>
			<li ng-class="{active: listVm.alert === 1}">
				<a ng-click="listVm.setAlert(1)" href="">
					<!--<i class="icon-inventory-all"></i>-->Expiring
					<span class="badge" ng-if="listVm.alert_counts[1]">{{ listVm.alert_counts[1] }}</span>
				</a>
			</li>
			<li ng-class="{active: listVm.alert === 0}">
				<a ng-click="listVm.setAlert(0)" href="">
					<!--<i class="icon-inventory-all"></i>-->Running Low
					<span class="badge" ng-if="listVm.alert_counts[0]">{{ listVm.alert_counts[0] }}</span>
				</a>
			</li>
		</ul>
	</div>
	<div ng-if="listVm.alert == 3">
		<div class="content-block" ng-if="!listVm.selection">
			<ng-include src="view.get('inventory/new-items/filters.html')"></ng-include>
			<?php if ($_check_access('inventory', 'create')): ?>
				<div class="list-control">
					<a ng-if="listVm.alert !== 0" class='btn btn-success' href='/inventory/{{ ::org_id }}/create/'>
						Create New Item
					</a>
				</div>
			<?php endif ?>
			<div class="headline">New Items</div>
			<table class='opake' ng-if="listVm.search_items.length">
				<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th sort="number" class="width-25">Item Number</th>
					<th sort="name" class="width-25">Item Name</th>
					<th sort="type">Type</th>
					<th sort="manufacturer">Manufacturer</th>
					<th sort="date_created" class='text-center'>Date Entered</th>
					<th sort="complete_status" class='text-center'>Status</th>
				</tr>
				</thead>
				<tbody>
				<tr ng-repeat="item in listVm.search_items">
					<td><a href='/inventory/{{ ::org_id }}/view/{{ ::item.id }}' read-more>{{ ::item.number }}</a></td>
					<td><a href='/inventory/{{ ::org_id }}/view/{{ ::item.id }}' read-more>{{ ::item.name }}</a></td>
					<td><a href='/inventory/{{ ::org_id }}/view/{{ ::item.id }}' read-more>{{ ::item.type }}</a></td>
					<td><a href='/inventory/{{ ::org_id }}/view/{{ ::item.id }}' read-more>{{ ::item.manufacturer }}</a></td>
					<td class='text-center'>{{ ::item.date_created }}</td>
					<td class='text-center'>{{ ::listVm.inventoryConst.COMPLETE_STATUS[item.complete_status] }}</td>
				</tr>
				</tbody>
			</table>
			<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
			<h4 ng-if="listVm.search_items && listVm.search_items.length === 0"><span >No items available.</span></h4>
		</div>
	</div>
	<div ng-if="listVm.alert != 3">
		<div class="content-block" ng-if="!listVm.selection">
			<ng-include src="view.get('inventory/filters.html')"></ng-include>

			<?php if ($_check_access('inventory', 'create')): ?>
				<div class="list-control">
					<a ng-if="listVm.alert !== 0" class='btn btn-success' href='/inventory/{{ ::org_id }}/create/'>
						New Item
					</a>
					<a ng-if="listVm.alert === 0 && listVm.search_items.length" class='btn btn-success'
					   ng-click="listVm.order()" href="">Add to Order</a>
				</div>
			<?php endif ?>

			<div class="headline">Search Results</div>
			<table class='opake' ng-if="listVm.search_items.length">
				<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th></th>
					<th sort="name" class="width-25">Item</th>
					<th sort="type">Type</th>
					<th sort="manufacturer">Manufacturer</th>
					<th sort="quantity" class='text-center'>Quantity</th>
					<th sort="min_level" class='text-center'>Par</th>
				</tr>
				</thead>
				<tbody>
				<tr ng-repeat="item in listVm.search_items">
					<td><img ng-src="{{ ::item.image }}"/></td>
					<td><a href='/inventory/{{ ::org_id }}/view/{{ ::item.id }}' read-more>{{ ::item.name }}</a></td>
					<td>{{ ::item.type }}</td>
					<td>{{ ::item.manufacturer }}</td>
					<td class='text-center'>{{ ::item.total_units }}</td>
					<td class='text-center'>{{ ::item.min_level }}</td>
				</tr>
				</tbody>
			</table>
			<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
			       callback="listVm.search()"></pages>
			<h4 ng-if="listVm.search_items && listVm.search_items.length === 0">
			<span ng-if="listVm.alert === ''">
				No items available.
				<?php if ($_check_access('inventory', 'create')): ?>
					Click "create item" or upload item master to populate inventory
				<?php endif ?>
			</span>
				<span ng-if="!listVm.clicked_search &&listVm.alert === 3">All item info complete</span>
				<span ng-if="!listVm.clicked_search &&listVm.alert === 1">No items at risk of expiring</span>
				<span ng-if="!listVm.clicked_search &&listVm.alert === 0">All inventory in stock, nothing running low</span>
				<span ng-if="listVm.clicked_search && listVm.alert !== ''">No items found</span>
			</h4>
		</div>

		<!-- Ordering -->
		<div class="content-block" ng-if="listVm.selection">
			<ng-include src="view.get('inventory/filters.html')"></ng-include>

			<div class="list-control">
				<a class="btn btn-danger" ng-click="listVm.orderSelected()" href="">Add Items</a>
			</div>

			<ng-include class="order-outgoing--selection" ng-if="listVm.selection" src="view.get('orders/selection.html')"
			            onLoad="ctrl = listVm;"></ng-include>
		</div>
	</div>

	<script type="text/ng-template" id="inventory/order.html">
		<div class="modal-header">
			<h4 class="modal-title">Add Items</h4>
		</div>
		<div class="modal-body">
			<h4>Would you like to:</h4>
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" ng-click="listVm.orderAll()">Add All</button>
			<button class="btn btn-primary" ng-click="listVm.orderSelection()" type="button">Select Items</button>
			<div class="text-right"><a ng-click="cancel()" href="">Cancel</a></div>
		</div>
	</script>

	<script type="text/ng-template" id="inventory/order_added.html">
		<div class="modal-body">
			<h4>Items Added to Bulk Order:</h4>
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" ng-click="ok()">Go to Order</button>
			<div class="text-right"><a ng-click="cancel()" href="">No thanks</a></div>
		</div>
	</script>

</div>
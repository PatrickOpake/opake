<div ng-controller="OrderListCrtl as listVm" ng-cloak>
	<div class="opk-tabs">
		<ul class="nav nav-tabs">
			<li ng-class="{active: listVm.type === 'outgoing'}">
				<a ng-click="listVm.setType('outgoing')" href="">Outgoing</a>
			</li>
			<li ng-class="{active: listVm.type === 'received'}">
				<a ng-click="listVm.setType('received')" href="">Incoming</a>
			</li>
		</ul>
	</div>
	<div class="content-block">
		<ng-include src="view.get('orders/' + listVm.type + '/filters.html')"
					onLoad="internal = <?= isset($internal) ? 'true' : 'false'; ?>;"></ng-include>
		<div ng-include="view.get('/orders/' + listVm.type + '/list.html')"></div>
	</div>
</div>
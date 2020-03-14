<div class="content-block order-received-adding" ng-controller="OrderReceivedAddingCrtl as addingVm" ng-cloak>
	<errors src="addingVm.errors"></errors>
 	<div ng-if="addingVm.step < 3" ng-include src="view.get('orders/received/adding/choose_vendor.html')"></div>
	<div ng-if="addingVm.step === 2" ng-include src="view.get('orders/received/adding/add_items.html')"></div>
	<div ng-if="addingVm.step === 3" ng-include src="view.get('orders/received/adding/order_info.html')"></div>
</div>
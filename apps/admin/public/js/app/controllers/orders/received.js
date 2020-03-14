// Outgoing Order
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('OrderReceivedCrtl', [
		'$scope',
		'$http',
		'$window',
		'$location',
		'OrderReceived',
		'Orders',
		'View',
		'config',
		function ($scope, $http, $window, $location, OrderReceived, Orders, View, config) {

			var vm = this;
			vm.order = {items: []};
			vm.modal;
			vm.order_status;
			vm.total_count = 0;
			vm.search_params = {p: 0, l: config.pagination.limit};
			vm.is_selected_order = false;

			$scope.$on("$locationChangeSuccess", function () {
				var params = $location.search();
				vm.action = angular.isDefined(params.action) ? params.action : null;
			});

			vm.init = function (id) {
				return $http.get('/orders/ajax/received/' + $scope.org_id + '/order/' + id).then(function (resp) {
					vm.order = new OrderReceived(resp.data);
					vm.order_status  = vm.order.getStatusName();
					if(vm.action === 'receive') {
						vm.selectOrder();
					}
				});
			};

			vm.searchItems = function () {
				$http.get('/orders/ajax/received/' + $scope.org_id + '/searchItems/' + vm.order.id, {params: angular.extend(vm.search_params, {org_id: $scope.org_id, order_id: vm.order.id})}).then(function (response) {
					var data = response.data;
					vm.order.items = data.items;
				});
			};

			vm.isAnyItemFieldFilled = function(item) {
				return !(item.received || item.damaged);
			};

			vm.toggleItemStatus = function($event, item) {
				var checkbox = $event.target;
				if(checkbox.value === 'incomplete') {
					var resetItem = function (item_id) {
						Orders.getReceivedOrderItem(item_id).then(function(data){
							angular.forEach(data, function(val, key) {
								item[key] = val;
							});
							item.status = 0;
						});
					};

					if(checkbox.checked) {
						vm.item = item;
						vm.modal = $scope.dialog(View.get('/orders/received/incomplete.html'), $scope, {windowClass: 'alert', size: 'md'});
						vm.modal.result.then(function(){

						}, function() {
							checkbox.checked = false;
							resetItem(item.id);
						});
					} else {
						resetItem(item.id);
					}
				}
			};

			vm.finished = function() {
				vm.modal = $scope.dialog(View.get('/orders/received/finish.html'), $scope, {windowClass: 'alert', size: 'md'});
				vm.modal.result.then(function(){
					vm.order.updateStatus();

					var saveOrder = function() {
						vm.order.save(function(resp){
							if (resp.data.id) {
								$window.location = '/orders/' + $scope.org_id + '/view/' + resp.data.id;
							}
						});
					};

					if (vm.order.status == 2) {
						$http.post('/orders/ajax/received/' + $scope.org_id + '/orderReceived/' + vm.order.id).finally(function() {
							saveOrder();
						});
					} else {
						saveOrder();
					}

				});
			};

			vm.selectOrder = function() {
				vm.modal = $scope.dialog(View.get('/orders/received/po.html'), $scope, {windowClass: 'alert', size: 'md'});
				vm.modal.result.then(function(){
					vm.is_selected_order = true;
				});
			};

			vm.canSelectOrder = function() {
				return !vm.order.po_id || vm.order.isIncomplete() && !vm.is_selected_order;
			};

			vm.canFinishOrder = function() {
				return vm.order.po_id && !vm.order.isIncomplete() || vm.is_selected_order;
			};

			vm.savePoId = function(po_id) {
				if(po_id) {
					$http.get('/orders/ajax/received/' + $scope.org_id + '/isPOUnique/' + vm.order.id, {params:  {po_id: po_id }}).then(function (response) {
						var data = response.data;
						if(data.exist) {
							$scope.errors = ['Order with the entered P.O.# already exists'];
						} else {
							vm.order.po_id = po_id;
							vm.modal.close();
						}
					});
				} else {
					$scope.errors = ['P.O.# field is required'];
				}
			};
		}]);

})(opakeApp, angular);

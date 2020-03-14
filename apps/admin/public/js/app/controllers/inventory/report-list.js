// Inventory list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('InventoryReportListCrtl', [
		'$scope',
		'$http',
		'$controller',
		'$filter',
		'Case',
		'Inventory',

		function ($scope, $http, $controller, $filter, Case, Inventory) {

			var vm = this;
			vm.isShowLoading = false;

			$controller('ListCrtl', {vm: vm});

			vm.search = function () {
				vm.table = 'case';
				vm.selectAll = false;
				vm.toSelected = [];
				vm.isShowLoading = true;
				var requestParams = prepareFilterParams();
				$http.get('/cases/ajax/' + $scope.org_id + '/cards/', {params: requestParams}).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new Case(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
					vm.isShowLoading = false;
				});
			};
			vm.search();

			vm.isCaseSearchEnable = function () {
				return vm.search_params.start && vm.search_params.end;
			};

			vm.searchInventories = function () {
				vm.table = 'inventory';
				vm.isShowLoading = true;
				var data = prepareFilterParams();
				data.selected_cases = [];
				angular.forEach(vm.toSelected, function (caseItem) {
					data.selected_cases.push(caseItem.id);
				});
				if (data.selected_cases) {
					data.selected_cases = JSON.stringify(data.selected_cases);
					$http.get('/inventory-report/ajax/' + $scope.org_id + '/list/', {params: data}).then(function (response) {
						var items = [];
						angular.forEach(response.data.items, function (data) {
							items.push(new Inventory(data));
						});
						vm.inventories = items;
						vm.inventories_total_count = response.data.total_count;
						vm.isShowLoading = false;
					});
				}
			};

			vm.export = function () {
				var selectedCases = [];
				angular.forEach(vm.toSelected, function (caseItem) {
					selectedCases.push(caseItem.id);
				});
				var requestParams = angular.copy(vm.search_params);
				if (requestParams.start) {
					requestParams.start = $filter('date')(requestParams.start, 'M/d/yyyy');
				}
				if (requestParams.end) {
					requestParams.end = $filter('date')(requestParams.end, 'M/d/yyyy');
				}
				if (requestParams.inventory_manf) {
					requestParams.inventory_manf = requestParams.inventory_manf.name;
				}
				if (requestParams.doctor) {
					requestParams.doctor = requestParams.doctor.fullname;
				}
				if (requestParams.procedure) {
					requestParams.procedure = requestParams.procedure.full_name;
				}
				if (requestParams.inventory) {
					requestParams.inventory = requestParams.inventory.full_name;
				}
				var data = {
					selected_cases: JSON.stringify(selectedCases),
					filter_values: JSON.stringify(requestParams)
				};
				window.location = '/inventory-report/' + $scope.org_id + '/export?' + $.param(data);
			};

			function prepareFilterParams() {
				var requestParams = angular.copy(vm.search_params);
				if (requestParams.start) {
					requestParams.start = moment(requestParams.start).format('YYYY-MM-DD');
				}
				if (requestParams.end) {
					requestParams.end = moment(requestParams.end).format('YYYY-MM-DD');
				}
				if (requestParams.inventory_manf) {
					requestParams.inventory_manf = requestParams.inventory_manf.name;
				}
				if (requestParams.doctor) {
					requestParams.doctor = requestParams.doctor.id;
				}
				if (requestParams.procedure) {
					requestParams.procedure = requestParams.procedure.id;
				}
				if (requestParams.inventory) {
					requestParams.inventory_id = requestParams.inventory.id;
					delete requestParams.inventory;
				}

				return requestParams;
			}

		}]);

})(opakeApp, angular);

(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ItemMasterListCtrl', [
		'$scope',
		'$http',
		'$controller',
		'Inventory',
		function ($scope, $http, $controller, Inventory) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});
			vm.action = 'view';

			vm.search = function () {
				var data = vm.search_params;

				$http.get('/master/ajax/' + $scope.org_id, {params: data }).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new Inventory(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
				});
			};
			vm.search();

			vm.edit = function(){
				vm.original_items = angular.copy(vm.items);
				vm.action = 'edit';
			};

			vm.cancel = function () {
				vm.items = vm.original_items;
				vm.action = 'view';
			};

			vm.save = function() {
				$http.post('/master/ajax/' + $scope.org_id + '/save/', $.param({data: JSON.stringify(vm.items)})).then(function (result) {
					vm.errors = null;
					if (result.data.result == 'ok') {
						vm.action = 'view';
					} else if (result.data.errors) {
						vm.errors = result.data.errors.split(';');
					}
				});
			};

			vm.downloadItemMaster = function () {
				window.location = '/master/inventory/' + $scope.org_id + '/downloadItemMaster';
			};

		}]);

})(opakeApp, angular);

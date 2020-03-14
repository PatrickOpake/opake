// Insurance list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('InsuranceListCrtl', [
		'$scope',
		'$http',
		'$window',
		'config',
		'Insurance',
		'InsuranceConst',
		function ($scope, $http, $window, config, Insurance, InsuranceConst) {
			$scope.insurance_const = InsuranceConst;

			var vm = this;

			vm.items = [];
			vm.total_count = 0;
			vm.search_params = {p: 0, l: config.pagination.limit, status_active: true, status_inactive: false};

			vm.search = function (reset_page) {
				vm.search_params.p = reset_page ? 0 : vm.search_params.p;
				$http.get('/insurances/ajax/', {params: vm.search_params}).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function(data){
						items.push(new Insurance(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;

				});
			};
			vm.search();

			vm.reset = function () {
				for (var key in vm.search_params) {
					delete vm.search_params[key];
				}
				vm.search_params.p = 0;
				vm.search_params.l = config.pagination.limit;
				vm.search_params.status_active = true;
				vm.search_params.status_inactive = false;
				vm.search();
			};

			vm.edit = function (id) {
				$window.location = '/settings/insurances/edit/' + id;
			};
		}]);

})(opakeApp, angular);

// Order list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('OrderListCrtl', [
		'$scope',
		'$http',
		'$location',
		'config',

		function ($scope, $http, $location, config) {

			var vm = this;

			vm.search_params = {p: 0, l: config.pagination.limit};

			$scope.$on("$locationChangeSuccess", function () {
				var params = $location.search();
				vm.type = angular.isDefined(params.type) ? params.type : 'outgoing';
				vm.reset();
			});

			vm.setType = function(type) {
				$location.search('type', type);
			};

			vm.reset = function() {
				for (var key in vm.search_params) {
					delete vm.search_params[key];
				}
				vm.search_params.p = 0;
				vm.search_params.l = config.pagination.limit;
				if(vm.type === 'received') {
					vm.search_params.type = 'open_orders';
				}
				vm.search();
			};

			vm.search = function () {
				var data = angular.copy(vm.search_params);
				if(data.date_from) {
					data.date_from = moment(data.date_from).format('YYYY-MM-DD');
				}
				if(data.date_to) {
					data.date_to = moment(data.date_to).format('YYYY-MM-DD');
				}

				return $http.get('/orders/ajax/' + vm.type + '/' + $scope.org_id + '/index', {params: data}).then(function (response) {
					var data = response.data;
					vm.items = data.items;
					vm.total_count = data.total_count;
				});
			};

			vm.delete = function (item) {
				if (confirm('Are you sure?')) {
					return $http.get('/orders/ajax/' + vm.type + '/' + $scope.org_id + '/delete/' + item.id).then(function (response) {
						vm.search();
					});
				}
			};

		}]);

})(opakeApp, angular);

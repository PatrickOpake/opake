(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('UserListCrtl', [
		'$scope',
		'$http',
		'$controller',
		'User',
		function ($scope, $http, $controller, User) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.siteId = null;

			vm.init = function(siteId) {
				vm.siteId = siteId;
				vm.search();
			};

			vm.search = function () {
				var data = angular.copy(vm.search_params);

				data.site_id = vm.siteId;

				if (data.time_first_login) {
					data.time_first_login = moment(data.time_first_login).format('YYYY-MM-DD');
				}

				if (data.time_last_login) {
					data.time_last_login = moment(data.time_last_login).format('YYYY-MM-DD');
				}

				$http.get('/users/ajax/' + $scope.org_id + '/', {params: data }).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new User(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
				});
			};

		}]);

})(opakeApp, angular);

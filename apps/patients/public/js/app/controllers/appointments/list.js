(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('AppointmentsListCtrl', [
		'$scope',
		'$http',
		'CaseRegistrationConst',
		'CaseRegistration',
		function ($scope, $http, CaseRegistrationConst, CaseRegistration) {

			var vm = this;
			vm.appointments = [];
			vm.searchParams = null;
			vm.totalCount = null;
			vm.isLoaded = false;

			$scope.caseRegistrationConst = CaseRegistrationConst;

			vm.init = function () {
				vm.reset();
				vm.load();
			};

			vm.load = function () {
				$http.get('/api/appointments/myAppointments', {params: vm.searchParams}).then(function (res) {
					var items = [];
					angular.forEach(res.data.items, function (data) {
						items.push(new CaseRegistration(data));
					});
					vm.appointments = items;

					vm.totalCount = res.data.total_count;
					vm.isLoaded = true;
				});
			};

			vm.reset = function () {
				vm.searchParams = {
					p: 0,
					l: 50,
					sort_by: 'dos',
					sort_order: 'DESC'
				};
			};


			vm.init();

		}]);

})(opakeApp, angular);

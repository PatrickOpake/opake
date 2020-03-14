(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseCanceledListCrtl', [
		'$scope',
		'$http',
		'$window',
		'$controller',
		'$location',
		'CaseCancellation',
		'CaseRegistrationConst',
		'Tools',
		'View',
		function ($scope, $http, $window, $controller, $location, CaseCancellation, CaseRegistrationConst, Tools, View) {

			$scope.caseRegistrationConst = CaseRegistrationConst;

			var vm = this;
			vm.isShowLoading = false;

			$controller('ListCrtl', {vm: vm});


			vm.search = function () {
				vm.isShowLoading = true;

				var params = $location.search();
				if (params.first_name && params.last_name && params.dos) {
					vm.search_params.patient_first_name = params.first_name;
					vm.search_params.patient_last_name = params.last_name;
					vm.search_params.dos =  moment(params.dos).toDate();
					$window.location.hash = '';
				}

				var data = prepareData(vm.search_params);
				$http.get('/cases/ajax/' + $scope.org_id + '/searchCanceledCases/', {params: data }).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new CaseCancellation(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
					vm.isShowLoading = false;
				});
			};
			vm.search();

			vm.rescheduleCase = function(cancellationItem) {
				$window.location = '/cases/' + $scope.org_id + '#?case=' + cancellationItem.case_id + '&cancellation=' + cancellationItem.id;
			};

			vm.export = function() {
				var data = prepareData(vm.search_params);
				$http.get('/cases/ajax/' + $scope.org_id + '/exportCancellations/', {params: data }).then(function (response) {
					if (response.data.success) {
						Tools.export(response.data.url);
					}
				});
			};

			vm.cancelAppointment = function (cancellation) {
				$scope.dialog(View.get('cases/confirm_cancel_appointment.html'), $scope, {
					controller: 'CaseCancellationCtrl',
					controllerAs: 'cancellationVm',
					resolve: {
						caseCancellation: cancellation,
						updateCancellation: true
					},
					windowClass: 'alert'
				});
			};

			function prepareData(searchParams) {
				var is_filters_enabled = isFiltersEnabled();
				if (!is_filters_enabled) {
					vm.search_params.cancel_date_from = searchParams.cancel_date_from = moment().subtract(30, 'days').toDate();
				}
				var data = angular.copy(searchParams);
				if (data.dos) {
					data.dos = moment(data.dos).format('YYYY-MM-DD');
				}
				if (data.cancel_date) {
					data.cancel_date = moment(data.cancel_date).format('YYYY-MM-DD');
				}
				if (data.cancel_date_from) {
					data.cancel_date_from = moment(data.cancel_date_from).format('YYYY-MM-DD');
				}
				if (data.cancel_date_to) {
					data.cancel_date_to = moment(data.cancel_date_to).format('YYYY-MM-DD');
				}

				return data;
			};

			function isFiltersEnabled() {
				if (vm.search_params.dos || vm.search_params.cancel_date || vm.search_params.cancel_date_from || vm.search_params.cancel_date_to
				|| vm.search_params.patient_last_name || vm.search_params.patient_first_name || vm.search_params.cancel_status) {
					return true;
				}

				return false;
			};
		}]);

})(opakeApp, angular);

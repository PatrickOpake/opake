(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('AnalyticsUserActivityCtrl', [
		'$rootScope',
		'$scope',
		'$http',
		'$location',
		'ActivityRecord',

		function ($rootScope, $scope, $http, $location, ActivityRecord) {

			var vm = this;
			vm.search_params = {};
			vm.totalCount = null;
			vm.items = [];
			vm.initParams = null;

			vm.isOrgColumnHidden = false;
			vm.isUserColumnHidden = false;

			vm.isInternal = true;
			vm.isDataLoaded = false;
			vm.isLoading = false;

			vm.init = function(params) {
				vm.isInternal = params.isInternal;
				if (!vm.isInternal) {
					vm.isOrgColumnHidden = true;
				}

				var initParams = {};
				var locationSearchParams = $location.search();
				if (locationSearchParams.case) {
					initParams.case = locationSearchParams.case;
				}

				vm.initParams = initParams;
				vm.reset();
				vm.initParams = null;
			};

			vm.search = function() {
				vm.isLoading = true;
				$http.get('/analytics/ajax/userActivity', {params: getFiltersRequestParams()}).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new ActivityRecord(data));
					});
					vm.items = items;
					vm.totalCount = response.data.total_count;
					vm.isDataLoaded = true;
				}).finally(function() {
					vm.isLoading = false;
				});
			};

			vm.exportToExcel = function() {
				if (vm.isInternal) {
					window.location = '/analytics/internal/exportUserActivity?' + $.param(getFiltersRequestParams());
				} else {
					window.location = '/analytics/' + $rootScope.org_id + '/exportUserActivity?' + $.param(getFiltersRequestParams());
				}

			};

			vm.reset = function() {

				vm.search_params = {
					p: 0,
					l: 50,
					sort_by: 'date',
					sort_order: 'DESC'
				};

				if (!vm.isInternal) {
					vm.search_params.organization = $rootScope.org_id;
				}

				if (vm.initParams) {
					vm.search_params = angular.extend(vm.search_params, vm.initParams);
				}

				vm.search();
			};

			vm.selectOrganization = function(item) {
				vm.search_params.organization = item.user_org_id;
				vm.search();
			};

			vm.selectUser = function(item) {
				vm.search_params.user = item.user_id;
				vm.search();
			};

			vm.getCurrentColumnsCount = function() {
				var count = 7;

				if (vm.isOrgColumnHidden) {
					count--;
				}
				if (vm.isUserColumnHidden) {
					count--;
				}

				return count;
			};

			function getFiltersRequestParams() {

				var requestParams = angular.copy(vm.search_params);
				if (requestParams.date_from) {
					requestParams.date_from = moment(requestParams.date_from).format('YYYY-MM-DD');
				}
				if (requestParams.date_to) {
					requestParams.date_to = moment(requestParams.date_to).format('YYYY-MM-DD');
				}
				if (requestParams.patient) {
					requestParams.patient = requestParams.patient.id;
				}

				return requestParams;
			}

		}]);

})(opakeApp, angular);

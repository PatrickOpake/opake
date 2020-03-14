// H&P docs
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseUserActivityAuditCtrl', [
		'$scope',
		'$http',
		'ActivityRecord',
		function ($scope, $http, ActivityRecord) {

			var vm = this;
			vm.caseId = null;
			vm.searchParams = {};
			vm.totalCount = null;
			vm.items = [];
			vm.isLoading = false;
			vm.isDataLoaded = false;

			vm.init = function(caseId) {
				vm.caseId = caseId;
				if (caseId) {
					vm.reset();
				}
			};

			vm.search = function() {
				vm.isLoading = true;
				$http.get('/analytics/ajax/userActivity', {params: vm.searchParams}).then(function (response) {
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

			vm.reset = function() {

				vm.searchParams = {
					p: 0,
					l: 50,
					sort_by: 'date',
					sort_order: 'DESC',
					'case': vm.caseId
				};

				vm.search();
			};

		}]);

})(opakeApp, angular);

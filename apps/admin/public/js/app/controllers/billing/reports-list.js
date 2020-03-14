// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BillingReportsListCtrl', [
		'$scope',
		'$http',
		'$controller',
		'$window',
		'$q',
		'BillingCaseReport',
		'BillingProcedureReport',
		'BillingConst',
		'Tools',
		function ($scope, $http, $controller, $window, $q, BillingCaseReport, BillingProcedureReport, BillingConst, Tools) {
			$scope.billingConst = BillingConst;

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.activeTab = 0;
			vm.showData = false;
			vm.tabs = [
				{tabKey: 'cases'},
				{tabKey: 'procedures'}
			];

			vm.search = function () {
				var data = prepareData(vm.search_params);

				if (vm.activeTab == 0) {
					$http.get('/billings/reports/ajax/' + $scope.org_id + '/cases/', {params: data}).then(function (response) {
						var items = [];
						angular.forEach(response.data.items, function (data) {
							items.push(new BillingCaseReport(data));
						});
						vm.items = items;
						vm.total_count = response.data.total_count;
						vm.showData = true;
					});
				}

				if (vm.activeTab == 1) {
					$http.get('/billings/reports/ajax/' + $scope.org_id + '/procedures/', {params: data}).then(function (response) {
						var items = [];
						angular.forEach(response.data.items, function (data) {
							items.push(new BillingProcedureReport(data));
						});
						vm.items = items;
						vm.total_count = response.data.total_count;
						vm.showData = true;
					});
				}
				
			};
			vm.search();

			vm.deselectTab = function (event, index, tabName) {
				vm.search_params.p = 0;
				vm.showData = false;
				angular.forEach(vm.tabs, function(v, k) {
					if (v.tabKey == tabName) {
						vm.activeTab = k;
					}
				});
				vm.search();
			};

			vm.saveCaseReport = function (item) {
				var def = $q.defer();
				$http.post('/billings/reports/ajax/' + $scope.org_id + '/saveCaseReport/', $.param({
					data: JSON.stringify(item)
				})).then(function () {
					def.resolve();
				});

				return def.promise;
			};

			vm.saveProcedureReport = function (item) {
				var def = $q.defer();
				$http.post('/billings/reports/ajax/' + $scope.org_id + '/saveProcedureReport/', $.param({
					data: JSON.stringify(item)
				})).then(function () {
					def.resolve();
				});

				return def.promise;
			};

			vm.removeProcedureReport = function (item) {
				$http.post('/billings/reports/ajax/' + $scope.org_id + '/removeProcedureReport/' + item.id).then(function () {
					var idx = vm.items.indexOf(item);
					vm.items.splice(idx, 1);
				});
			};

			vm.removeCaseReport = function (item) {
				$http.post('/billings/reports/ajax/' + $scope.org_id + '/removeCaseReport/' + item.id).then(function () {
					var idx = vm.items.indexOf(item);
					vm.items.splice(idx, 1);
				});
			};

			vm.addNew = function() {
				var newReport;
				if (vm.activeTab == 0) {
					newReport = new BillingCaseReport();
					newReport.organization_id = $scope.org_id;
					newReport.at_top = true;
					vm.saveCaseReport(newReport).then( function() {
						vm.search_params.p = 0;
						vm.search();
					});
				}
				if (vm.activeTab == 1) {
					newReport = new BillingProcedureReport();
					newReport.organization_id = $scope.org_id;
					newReport.at_top = true;
					vm.saveProcedureReport(newReport).then( function() {
						vm.search_params.p = 0;
						vm.search();
					});
				}
				
			};

			vm.export = function() {
				var data = prepareData(vm.search_params);
				if (vm.activeTab == 0) {
					$http.get('/billings/reports/ajax/' + $scope.org_id + '/exportCasesReports/', {params: data}).then(function (response) {
						if (response.data.success) {
							Tools.export(response.data.url);
						}
					});
				}
				if (vm.activeTab == 1) {
					$http.get('/billings/reports/ajax/' + $scope.org_id + '/exportProceduresReports/', {params: data}).then(function (response) {
						if (response.data.success) {
							Tools.export(response.data.url);
						}
					});
				}
			};

			vm.isExportAvailable = function() {
				return vm.items && vm.items.length && vm.search_params.dateFrom && vm.search_params.dateTo;
			};

			function prepareData(searchParams) {
				var data = angular.copy(searchParams);
				if (data.dateFrom) {
					data.dateFrom = moment(data.dateFrom).format('YYYY-MM-DD');
				}
				if (data.dateTo) {
					data.dateTo = moment(data.dateTo).format('YYYY-MM-DD');
				}
				if (data.surgeons) {
					data.surgeons = JSON.stringify(data.surgeons);
				}
				if (data.practices) {
					data.practices = JSON.stringify(data.practices);
				}

				return data;
			};

		}]);

})(opakeApp, angular);

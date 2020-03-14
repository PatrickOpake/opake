// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BillingCollectionsListCtrl', [
		'$scope',
		'$http',
		'$controller',
		'$window',
		'$q',
		'$filter',
		'BillingCollectionItem',
		'BillingNotes',
		'BillingConst',
		'PatientConst',
		'FeeScheduleConst',
		'Tools',
		'Permissions',
		function ($scope, $http, $controller, $window, $q, $filter, BillingCollectionItem, BillingNotes, BillingConst, PatientConst, FeeScheduleConst, Tools, Permissions) {
			$scope.billingConst = BillingConst;
			$scope.patientConst = PatientConst;
			$scope.feeScheduleConst = FeeScheduleConst;

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.isDataLoaded = false;
			vm.isLoading = false;
			vm.isExportGenerating = false;
			vm.isSaveBillingStatuses = false;
			vm.isResponsibilityShown = false;
			vm.isPrimaryTypeShown = false;
			vm.changedBillingStatuses = [];

			if(Permissions.user.isDoctor()) {
				vm.search_params.surgeon = Permissions.user.id;
			}

			vm.search = function () {
				vm.isLoading = true;
				var data = prepareData(vm.search_params);

				$http.get('/billings/collections/ajax/' + $scope.org_id, {params: data}).then(function (response) {
					var items = [];
					var caseIds = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new BillingCollectionItem(data));
						caseIds.push(data.case_id);
					});
					vm.items = items;
					vm.originalItems = angular.copy(vm.items);
					vm.total_count = response.data.total_count;
					BillingNotes.getUnreadNotes(caseIds);
					vm.isDataLoaded = true;
				}).finally(function() {
					vm.isLoading = false;
				});
			};
			vm.search();

			vm.export = function () {
				vm.isExportGenerating = true;
				var data = prepareData(vm.search_params);
				$http.get('/billings/collections/ajax/' + $scope.org_id + '/exportCollection/', {params: data }).then(function (response) {
					if (response.data.success) {
						Tools.export(response.data.url);
						vm.isExportGenerating = false;
					}
				});
			};

			vm.saveBillingStatuses = function () {
				vm.isSaveBillingStatuses = true;
				$http.post('/billings/collections/ajax/' + $scope.org_id + '/saveBillingStatuses/', $.param({
					data: JSON.stringify(vm.changedBillingStatuses)
				})).then(function () {
					vm.isSaveBillingStatuses = false;
					vm.changedBillingStatuses = [];
					vm.search();
				});

			};

			vm.isChangedBillingStatuses = function () {
				return vm.changedBillingStatuses.length;
			};

			vm.changeBillingStatus = function (item) {
				var itemBillingStatus = {'case_id': item.case_id, 'billing_status': item.billing_status};
				var items = $filter('filter')(vm.changedBillingStatuses, {case_id: item.case_id});
				if(items.length) {
					var idx = vm.changedBillingStatuses.indexOf(items[0]);
					if (idx > -1) {
						vm.changedBillingStatuses.splice(idx, 1);
					}
				}

				if(!angular.equals(vm.items, vm.originalItems)) {
					vm.changedBillingStatuses.push(itemBillingStatus);
				}
			};

			vm.toggleDisplayResponsibility = function() {
				vm.isResponsibilityShown = !vm.isResponsibilityShown;
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
				if (data.payer_name) {
					data.payer_name = data.payer_name.id;
				}
				if (data.charge) {
					var charges = [];
					angular.forEach(data.charge, function (item) {
						charges.push(item.id);
					});
					data.charge = JSON.stringify(charges);
				}

				return data;
			}

		}]);

})(opakeApp, angular);

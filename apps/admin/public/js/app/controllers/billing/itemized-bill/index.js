// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ItemizedBillListCtrl', [
		'$scope',
		'$http',
		'$controller',
		'Tools',
		function ($scope, $http, $controller, Tools) {
			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.statementForm = {};
			vm.isInitLoading = true;
			vm.isDocumentsLoading = false;
			vm.isPrintLoading = false;

			vm.search = function () {
				vm.selectAll = false;
				vm.toSelected = [];
				var data = angular.copy(vm.search_params);
				if (data.original_dos) {
					data.original_dos = moment(data.original_dos).format('YYYY-MM-DD');
				}
				$http.get('/billings/itemized-bill/ajax/' + $scope.org_id + '/', {params: data}).then(function (response) {
					vm.items = [];
					angular.forEach(response.data.items, function (item) {
						item.original_dos = moment(item.original_dos).toDate();
						vm.items.push(item);
					});
					vm.total_count = response.data.total_count;
					vm.statement_comment_options = response.data.statement_comment_options;
					vm.isInitLoading = false;

				});
			};

			vm.search();

			vm.openModalComment = function (item) {
				vm.typeOfStatement = 'individual';
				vm.selectedPatient = item;
				vm.modal = $scope.dialog('billing/itemized-bill/select-date-range.html', $scope, {
					size: 'md',
					windowClass:'billing-itemized-bill-date-range--modal',
					backdrop: 'static',
					keyboard: false
				});
			};

			vm.generateIndividualStatement = function () {
				vm.isPrintLoading = true;
				var data = angular.copy(vm.statementForm);
				if (data.dateRangeFrom) {
					data.dateRangeFrom = moment(data.dateRangeFrom).format('YYYY-MM-DD');
				}
				if (data.dateRangeTo) {
					data.dateRangeTo = moment(data.dateRangeTo).format('YYYY-MM-DD');
				}
				$http.post('/billings/itemized-bill/ajax/' + $scope.org_id + '/generatePatientStatement/' + vm.selectedPatient.id, $.param({
					data: angular.toJson(data)
				})).then(function (res) {
					vm.isPrintLoading = false;
					if (res.data.success) {
						vm.modal.close();
						vm.statementForm = {};
						Tools.print(location.protocol + '//' + location.host + res.data.url);
					}
				}, function () {
					vm.isPrintLoading = false;
				});
			};

			vm.multiplePrint = function () {
				vm.typeOfStatement = 'multiple';
				if (vm.toSelected && vm.toSelected.length) {
					vm.modal = $scope.dialog('billing/itemized-bill/select-date-range.html', $scope, {
						size: 'md',
						windowClass:'billing-itemized-bill-date-range--modal',
						backdrop: 'static',
						keyboard: false
					});
				}
			};

			vm.generateMultipleStatements = function () {
				if (vm.toSelected && vm.toSelected.length) {

					var patientIds = [];
					angular.forEach(vm.toSelected, function(patient_id) {
						patientIds.push(patient_id);
					});
					var data = angular.copy(vm.statementForm);
					if (data.dateRangeFrom) {
						data.dateRangeFrom = moment(data.dateRangeFrom).format('YYYY-MM-DD');
					}
					if (data.dateRangeTo) {
						data.dateRangeTo = moment(data.dateRangeTo).format('YYYY-MM-DD');
					}
					vm.isDocumentsLoading = true;

					$http.post('/billings/itemized-bill/ajax/' + $scope.org_id + '/compilePatientStatements/', $.param({
						patients: patientIds,
						data: angular.toJson(data)
					})).then(function(res) {
						vm.isDocumentsLoading = false;
						if (res.data.success) {
							vm.modal.close();
							vm.statementForm = {};
							Tools.print(location.protocol + '//' + location.host + res.data.url);
						}
					}, function() {
						vm.isDocumentsLoading = false;
					});

				}
			};

			vm.addToSelectedAll = function () {
				vm.toSelected = [];
				if (!vm.selectAll) {
					angular.forEach(vm.items, function (item) {
						vm.toSelected.push(item.id);
					});
				}
				vm.selectAll = !vm.selectAll;
			};


		}]);

})(opakeApp, angular);

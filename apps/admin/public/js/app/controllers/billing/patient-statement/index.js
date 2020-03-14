// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PatientStatementListCtrl', [
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
			vm.isPrintWithCommentLoading = false;

			vm.search = function () {
				vm.selectAll = false;
				vm.toSelected = [];
				var data = angular.copy(vm.search_params);
				if (data.original_dos) {
					data.original_dos = moment(data.original_dos).format('YYYY-MM-DD');
				}
				$http.get('/billings/patient-statement/ajax/' + $scope.org_id + '/', {params: data}).then(function (response) {
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
				vm.selectedPatient = item;
				vm.modal = $scope.dialog('billing/patient-statement/add-comment.html', $scope, {
					size: 'md',
					windowClass:'billing-patient-statement-comment--modal',
					backdrop: 'static',
					keyboard: false
				});
			};

			vm.generateIndividualStatement = function () {
				vm.isPrintWithCommentLoading = true;
				$http.post('/billings/patient-statement/ajax/' + $scope.org_id + '/generatePatientStatement/' + vm.selectedPatient.id, $.param({
					data: angular.toJson(vm.statementForm)
				})).then(function (res) {
					vm.isPrintWithCommentLoading = false;
					if (res.data.success) {
						vm.modal.close();
						vm.statementForm = {};
						Tools.print(location.protocol + '//' + location.host + res.data.url);
					}
				}, function () {
					vm.isPrintWithCommentLoading = false;
				});
			};

			vm.generateMultipleStatements = function () {
				if (vm.toSelected && vm.toSelected.length) {
					var patientIds = [];
					angular.forEach(vm.toSelected, function(patient_id) {
						patientIds.push(patient_id);
					});
					vm.isDocumentsLoading = true;
					$http.post('/billings/patient-statement/ajax/' + $scope.org_id + '/compilePatientStatements/', $.param({
						patients: patientIds
					})).then(function(res) {
						vm.isDocumentsLoading = false;
						if (res.data.success) {
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

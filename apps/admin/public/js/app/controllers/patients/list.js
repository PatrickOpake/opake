// Patient list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PatientListCrtl', [
		'$scope',
		'$http',
		'$window',
		'$controller',
		'Patient',
		'View',
		'PatientConst',

		function ($scope, $http, $window, $controller, Patient, View, PatientConst) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.createDisabled = true;
			vm.isShowLoading = false;

			vm.isCreateDisabled = function(){
				if (vm.items && vm.items.length < 10) {
					return false;
				}
				return vm.createDisabled;
			};

			vm.search = function () {
				vm.isShowLoading = true;
				var data = angular.copy(vm.search_params);
				if(data.dob) {
					data.dob = moment(data.dob).format('YYYY-MM-DD');
				}
				if (vm.isCreateDisabled()) {
					angular.forEach(data, function(val, key){
						if (val && key !== 'l' && key !== 'p') {
							vm.createDisabled = false;
						}
					});
				}
				if (data.surgeons) {
					data.surgeons = JSON.stringify(data.surgeons);
				}
				$http.get('/patients/ajax/' + $scope.org_id + '/', {params: data }).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new Patient(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
					vm.isShowLoading = false;
				});
			};
			vm.search();

			vm.openPatient = function(item) {
				$window.location = '/patients/' + $scope.org_id + '/view/' + item.id;
			};

			vm.sendPost = function (withCase, patient) {
				$http.post('/patients/ajax/' + $scope.org_id + '/removePatient/' + patient.id, $.param({withCase: withCase})).then(function () {
					vm.search();
				});
			};

			vm.removePatient = function (patient) {
				if(!patient.count_cases) {
					vm.modalDelete = $scope.dialog(View.get('patients/confirm_delete_zero.html'), $scope, {windowClass: 'alert'});
					vm.modalDelete.result.then(function () {
						vm.sendPost(0, patient);
					})
				} else {
					vm.selected_patient = patient;
					vm.modalDelete = $scope.dialog(View.get('patients/confirm_delete.html'), $scope, {windowClass: 'alert'});
				}
			};

			vm.remove = function () {
				vm.sendPost(1, vm.selected_patient);
				vm.modalDelete.close();
			};

			vm.archive = function () {
				$http.post('/patients/ajax/' + $scope.org_id + '/archivePatient/' + vm.selected_patient.id).then(function () {
					vm.search();
					vm.modalDelete.close();
				});
			};

			vm.archivePatient = function (patient) {
				var sendPost = function (status) {
					$http.post('/patients/ajax/' + $scope.org_id + '/archivePatient/' + patient.id, $.param({status: status})).then(function () {
						vm.search();
					});
				};
				if(patient.status === PatientConst.STATUS.ACTIVE) {
					$scope.dialog(View.get('patients/confirm_archive.html'), $scope, {windowClass: 'alert'}).result.then(function () {
						sendPost(PatientConst.STATUS.ARCHIVE);
					});
				} else {
					sendPost(PatientConst.STATUS.ACTIVE);
				}

			};

		}]);

})(opakeApp, angular);

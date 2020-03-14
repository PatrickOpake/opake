(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ProfileCtrl', [
		'$scope',
		'$http',
		'$q',
		'$state',
		'View',
		'Patient',
		'PatientConst',
		'CaseRegistrationConst',
		'Insurances',
		function ($scope, $http, $q, $state, View, Patient, PatientConst, CaseRegistrationConst, Insurances) {

			var vm = this;
			vm.isShowForm = false;
			vm.errors = null;

			$scope.patientConst = PatientConst;
			$scope.caseRegistrationConst = CaseRegistrationConst;
			vm.profileImageOptions = null;
			vm.profileImageControl = {};
			vm.isSaveFormSent = false;

			vm.init = function (PatientId) {
				var def = $q.defer();
				$http.get('/api/patients/patient/' + PatientId).then(function (result) {
					vm.profile = new Patient(result.data);
					vm.profileImageOptions = {
						imageSrc: vm.profile.photo_default
					};
					vm.validate();
					def.resolve(vm.profile);
				});

				return def.promise;
			};

			vm.edit = function() {
				vm.isShowForm = true;
				vm.originalProfile = angular.copy(vm.profile);
			};

			vm.cancel = function() {
				vm.profile = vm.originalProfile;
				vm.isShowForm = false;
				vm.errors = null;
			};

			vm.save = function() {

				if (!vm.isSaveFormSent) {
					vm.isSaveFormSent = true;
					vm.errors = null;

					vm.profileImageControl.saveImage({
						imageType: 'user'
					}).then(function(uploadedFile) {
						if (uploadedFile) {
							vm.profile.photo_id = uploadedFile.image_id;
						}

						Insurances.checkRelationship(vm.profile);
						$http.post('/api/patients/save/' + vm.profile.id, $.param({data: JSON.stringify(vm.profile)})).then(function (result) {
							vm.isSaveFormSent = false;
							if (result.data.id) {
								vm.init(result.data.id).then(function() {
									vm.isShowForm = false;
									$state.go('app.insurance', null, {
										reload: true
									});
								});

							} else if (result.data.errors) {
								vm.errors =  angular.fromJson(result.data.errors);
							}
						});
					}, function(errors) {
						vm.errors = errors;
						vm.isSaveFormSent = false;
					});

				}

			};

			vm.getView = function () {
				var view = 'app/profile/' + (vm.isShowForm ? 'form' : 'view') + '.html';

				return View.get(view);
			};

			vm.validate = function() {
				return $http.post('/api/patients/validate/' + vm.profile.id, $.param({data: JSON.stringify(vm.profile)})).then(function (result) {
					vm.validate_errors =  angular.fromJson(result.data.errors);
					if (vm.hasPatientValidationErrors()) {
						vm.edit();
					}
				});
			};

			vm.hasPatientValidationErrors = function() {
				if (vm.validate_errors && vm.validate_errors.patient) {
					return true;
				}
				
				return false;
			};

		}]);

})(opakeApp, angular);

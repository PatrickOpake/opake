// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('PatientsPortal', ['$q', '$http', 'View', function ($q, $http, View) {

		var self = this;

		this.openEmailWindow = function($scope, patientId, patient) {
			var def = $q.defer();
			$scope.dialog(View.get('/patients/portal/login-email.html'), $scope, {
				controller: ['$scope', '$uibModalInstance', function($scope, $uibModalInstance) {

					var modalVm = this;
					modalVm.loginEmail = null;
					modalVm.isRequestSended = false;

					modalVm.loadMail = function() {
						$http.get('/patients/ajax/' + $scope.org_id + '/preparePatientLoginMail/' + patientId).then(function (result) {
							if (result.data.success) {
								modalVm.loginEmail = result.data.mail;
							}
						});
					};

					modalVm.send = function() {
						if (!modalVm.isRequestSended) {
							modalVm.isRequestSended = true;
							$http.post('/patients/ajax/' + $scope.org_id + '/sendPatientLoginMail/' + patientId,  $.param({subject: modalVm.loginEmail.subject, body: modalVm.loginEmail.body})).then(function (result) {
								if (result.data.success) {
									def.resolve();
									if (patient) {
										patient.is_registered_on_portal = true;
									}
									$uibModalInstance.close();
								}
							});
						}
					};

					modalVm.cancel = function() {
						$http.post('/patients/ajax/' + $scope.org_id + '/cancelPatientLoginMail/' + patientId);
						def.reject();
						$uibModalInstance.dismiss('cancel');
					};

					modalVm.loadMail();
				}],
				controllerAs: 'ctrl',
				size: 'xl'
			});

			return def.promise;
		}
	}]);
})(opakeApp, angular);

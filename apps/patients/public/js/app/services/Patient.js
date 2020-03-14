(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('PatientService', [
		'$http',
		'$q',
		function ($http, $q) {

			this.isRedirectedToInsurance = function (patientId) {
				var deferred = $q.defer();
				$http.get('/api/patients/isRedirectedToInsurance/' + patientId).then(function (result) {
					deferred.resolve(result.data);
				});
				
				return deferred.promise;
			};

		}]);
})(opakeApp, angular);

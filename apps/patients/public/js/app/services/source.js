(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('Source', [
		'$http',
		'$q',

		function ($http, $q) {

			var data = {};

			var getData = function (src, params) {
				var deferred = $q.defer();
				var key = src;
				if (params) {
					key += JSON.stringify(params);
				}
				if (angular.isDefined(data[key])) {
					deferred.resolve(data[key]);
				} else {
					$http.get(src, {params: params}).then(function (result) {
						data[key] = result.data;
						deferred.resolve(result.data);
					});
				}
				return deferred.promise;
			};

			this.getCountries = function () {
				return getData('/api/geo/countries');
			};

			this.getStates = function () {
				return getData('/api/geo/states');
			};

			this.getCities = function (state_id) {
				return getData('/api/geo/cities', {state_id: state_id});
			};

			this.getLanguages = function () {
				return getData('/api/choice/languages/');
			};

			this.getInsurances = function (q) {
				return getData('/api/choice/insurances/', {query: q});
			};

		}]);
})(opakeApp, angular);

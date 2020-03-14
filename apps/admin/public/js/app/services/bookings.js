// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('Bookings', [
		'$http',
		'$rootScope',
		'$q',
		'$window',
		'Booking',

		function ($http, $rootScope, $q, $window, Booking) {

			this.get = function (bookingId) {
				var def = $q.defer();
				$http.get('/booking/ajax/' + $rootScope.org_id + '/booking/' + bookingId).then(function (result) {
					var booking = new Booking(result.data);
					def.resolve(result.data);
				}, function (error) {
					def.reject(error);
				});
				return def.promise;
			};

			this.save = function (data, callback) {
				return $http.post('/booking/ajax/' + $rootScope.org_id + '/save/', $.param({
					data: JSON.stringify(data)
				})).then(function (result) {
					if (callback) {
						callback(result);
					}
				});
			};

			this.schedule = function (data, callback) {
				this.save(data, function (result) {
					if(callback) {
						callback(result);
					}
				});
			};



		}]);
})(opakeApp, angular);

(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('MenuCounter', [
		'$http',
		'$rootScope',
		'$q',

		function ($http, $rootScope, $q) {

			var self = this;
			self.bookingCount = null;

			this.getCount = function(key, updateResults) {
				if (key == 'booking') {
					return self.getBookingCount(updateResults)
				}

				return null;
			};

			this.getBookingCount = function(updateResults) {
				var deferred = $q.defer();
				if (self.bookingCount && !updateResults) {
					deferred.resolve(self.bookingCount);
				} else if ($rootScope.loggedUser.isSatelliteOffice()) {
					self.bookingCount = 0;
					deferred.resolve(self.bookingCount);
				} else {
					$http.get('/booking/ajax/' + $rootScope.org_id + '/getUnscheduledCount/').then(function (response) {
						self.bookingCount = response.data;
						deferred.resolve(self.bookingCount);
					});
				}

				return deferred.promise;
			};

		}]);
})(opakeApp, angular);

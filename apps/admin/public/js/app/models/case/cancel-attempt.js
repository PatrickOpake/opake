(function (opakeApp, angular, $) {
	'use strict';

	opakeApp.factory('CaseCancelAttempt', [function () {

			var CaseCancelAttempt = function (data) {

				angular.extend(this, data);

				if (angular.isDefined(data)) {
					this.date_called = moment(data.date_called).toDate();
				}

			};

			return (CaseCancelAttempt);
		}]);
})(opakeApp, angular, $);
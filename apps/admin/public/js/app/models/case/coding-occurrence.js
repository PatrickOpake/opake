(function (opakeApp, angular, $) {
	'use strict';

	opakeApp.factory('CodingOccurrence', [function () {

			var CodingOccurrence = function (data) {

				angular.extend(this, data);

				if (angular.isDefined(data)) {
					this.date = moment(data.date).toDate();
				}

			};

			return (CodingOccurrence);
		}]);
})(opakeApp, angular, $);
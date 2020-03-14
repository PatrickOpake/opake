(function (opakeApp, angular, $) {
	'use strict';

	opakeApp.factory('CodingInsurance', [function () {

			var CodingInsurance = function (data) {

				angular.extend(this, data);

			};

			return (CodingInsurance);
		}]);
})(opakeApp, angular, $);
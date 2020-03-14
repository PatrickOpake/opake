(function (opakeApp, angular, $) {
	'use strict';

	opakeApp.factory('CodingValue', [function () {

			var CodingValue = function (data) {

				angular.extend(this, data);

			};

			return (CodingValue);
		}]);
})(opakeApp, angular, $);
(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('InsurancePayorAddress', [function () {

			var InsurancePayorAddress = function (data) {

				angular.extend(this, data);
			};

			return (InsurancePayorAddress);
		}]);
})(opakeApp, angular);
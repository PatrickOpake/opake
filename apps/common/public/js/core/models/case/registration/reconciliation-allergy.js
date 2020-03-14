(function (opakeCore, angular, $) {
	'use strict';

	opakeCore.factory('ReconciliationAllergy', [function () {

			var ReconciliationAllergy = function (data) {

				angular.extend(this, data);

			};

			return (ReconciliationAllergy);
		}]);
})(opakeCore, angular, $);
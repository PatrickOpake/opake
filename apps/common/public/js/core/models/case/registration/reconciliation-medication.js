(function (opakeCore, angular, $) {
	'use strict';

	opakeCore.factory('ReconciliationMedication', [function () {

			var ReconciliationMedication = function (data) {

				angular.extend(this, data);

			};

			return (ReconciliationMedication);
		}]);
})(opakeCore, angular, $);
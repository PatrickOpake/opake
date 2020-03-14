(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('CPT', [function () {

			var CPT = function (data) {

				angular.extend(this, data);

			};

			return (CPT);
		}]);
})(opakeApp, angular);
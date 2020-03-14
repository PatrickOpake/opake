(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('Department', [function () {

			var Department = function (data) {
				angular.extend(this, data);
			};

			return (Department);
		}]);
})(opakeApp, angular);
(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('Site', [function () {

			var Site = function (data) {
				angular.extend(this, data);
			};

			return (Site);
		}]);
})(opakeApp, angular);
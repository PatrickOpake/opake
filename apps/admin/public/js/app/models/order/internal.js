(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('OrderInternal', ['$filter', function ($filter) {

			var OrderInternal = function (data) {

				angular.extend(this, data);

			};

			return (OrderInternal);
		}]);
})(opakeApp, angular);
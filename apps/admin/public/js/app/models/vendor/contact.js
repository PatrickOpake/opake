(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('VendorContact', [function () {

			var VendorContact = function (data) {
				angular.extend(this, data);
			};

			return (VendorContact);
		}]);
})(opakeApp, angular);
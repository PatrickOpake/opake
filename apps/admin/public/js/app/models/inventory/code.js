(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('InventoryCode', [function () {

			var InventoryCode = function (data) {
				angular.extend(this, data);
			};

			return (InventoryCode);
		}]);
})(opakeApp, angular);
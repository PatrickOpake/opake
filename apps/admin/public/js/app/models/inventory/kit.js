(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('InventoryKit', [function () {

			var InventoryKit = function (data) {
				angular.extend(this, data);
			};

			return (InventoryKit);
		}]);
})(opakeApp, angular);
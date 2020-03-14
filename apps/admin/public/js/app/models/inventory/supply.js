(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('InventorySupply', [function () {

			var InventorySupply = function (data) {
				angular.extend(this, data);
			};

			return (InventorySupply);
		}]);
})(opakeApp, angular);
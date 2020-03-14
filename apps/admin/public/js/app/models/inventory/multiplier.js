(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('InventoryMultiplier', [function () {

			var InventoryMultiplier = function (data) {
				angular.extend(this, data);

				this.typeIsItemName = function() {
					return this.type == 0;
				};

				this.typeIsItemType = function() {
					return this.type == 1;
				};
			};

			return (InventoryMultiplier);
		}]);
})(opakeApp, angular);
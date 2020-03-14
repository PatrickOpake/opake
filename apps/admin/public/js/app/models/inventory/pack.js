(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('InventoryPack', [function () {

			var InventoryPack = function (data) {
				angular.extend(this, data);

				if (data.exp_date) {
					this.exp_date = moment(data.exp_date).toDate();
				}
			};

			return (InventoryPack);
		}]);
})(opakeApp, angular);
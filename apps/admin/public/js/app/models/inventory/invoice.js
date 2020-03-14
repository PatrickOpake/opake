(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('InventoryInvoice', [function () {

			var InventoryInvoice = function (data) {
				angular.extend(this, data);

				if (angular.isDefined(data)) {
					this.date = moment(data.date).toDate();
				} else {
					this.manufacturers = [];
					this.items = [];
				}

				this.getManufacturerNames = function () {
					return this.manufacturers.map(function (v) {
						return v.name;
					}).join(', ');
				};

				this.getItemNames = function () {
					return this.items.map(function (v) {
						return v.name;
					}).join(', ');
				};

				this.toJSON = function () {
					var copy = angular.copy(this);
					if (copy.date) {
						copy.date = moment(copy.date).format('YYYY-MM-DD');
					}
					copy.manufacturers = copy.manufacturers.map(function (v) {
						return v.id;
					});
					copy.items = copy.items.map(function (v) {
						return v.id;
					});
					return copy;
				};
			};

			return (InventoryInvoice);
		}]);
})(opakeApp, angular);
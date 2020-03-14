(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('Inventory', ['$rootScope', 'InventoryCode', 'InventoryPack', 'InventorySupply', 'InventoryKit', function ($rootScope, InventoryCode, InventoryPack, InventorySupply, InventoryKit) {

			var Inventory = function (data) {
				angular.extend(this, data);
				var self = this;

				this.codes = [];
				this.packs = [];
				this.supplies = [];
				this.kit_items = [];
				this.substitutes = [];
				if (data) {
					angular.forEach(data.codes, function (code) {
						self.codes.push(new InventoryCode(code));
					});

					angular.forEach(data.packs, function (pack) {
						self.packs.push(new InventoryPack(pack));
					});

					angular.forEach(data.supplies, function (supply) {
						self.supplies.push(new InventorySupply(supply));
					});

					angular.forEach(data.kit_items, function (item) {
						self.kit_items.push(new InventoryKit(item));
					});

					angular.forEach(data.substitutes, function (item) {
						self.substitutes.push(item);
					});

					if (data.time_create) {
						this.time_create = moment(data.time_create).toDate();
					}

					if (data.time_update) {
						this.time_update = moment(data.time_update).toDate();
					}
				}

				this.newManufacturer = function(name) {
					return {
						id: null,
						name: name,
						organization_id: $rootScope.org_id,
						time_create: moment().toDate(),
						is_dist: 0,
						is_manf: 1
					};
				};

				this.newUom = function(name) {
					return {
						id: null,
						name: name
					};
				};
			};

			return (Inventory);
		}]);
})(opakeApp, angular);
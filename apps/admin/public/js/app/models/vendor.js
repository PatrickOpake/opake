(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('Vendor', ['VendorContact', function (VendorContact) {

			var Vendor = function (data) {

				angular.extend(this, data);
				var self = this;

				this.contacts = [];
				if (data) {
					angular.forEach(data.contacts, function (contact) {
						self.contacts.push(new VendorContact(contact));
					});

					if (data.time_create) {
						this.time_create = moment(data.time_create).toDate();
					}
				}

				this.getTypes = function () {
					var types = [];
					if (this.is_dist) {
						types.push('Distributor');
					}
					if (this.is_manf) {
						types.push('Manufacturer');
					}
					return types.join(', ');
				};

			};

			return (Vendor);
		}]);
})(opakeApp, angular);
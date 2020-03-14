(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('InsurancePayor', ['InsurancePayorAddress', function (InsurancePayorAddress) {

			var InsurancePayor = function (data) {

				angular.extend(this, data);
				var self = this;

				this.addresses = [];
				if (data) {
					angular.forEach(data.addresses, function (address) {
						self.addresses.push(new InsurancePayorAddress(address));
					});
				}


				this.addAddress = function(address) {
					self.addresses.push(address);
				};

				this.deleteAddress = function(address) {
					let index = self.addresses.indexOf(address);
					self.addresses.splice(index, 1);
				};

				this.updateAddress = function(oldAddress, newAddress) {
					let index = self.addresses.indexOf(oldAddress);
					self.addresses[index] = newAddress;
				};
			};

			return (InsurancePayor);
		}]);
})(opakeApp, angular);
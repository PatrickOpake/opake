(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('CardStaff', ['Source', function (Source) {

			var CardStaff = function (data) {

				var self = this;

				angular.extend(this, data);

				if (!self.items) {
					self.items = [];
				}
				if (!self.notes) {
					self.notes = [];
				}

				this.getType = function () {
					return 'staff';
				};

			};

			return (CardStaff);
		}]);
})(opakeApp, angular);
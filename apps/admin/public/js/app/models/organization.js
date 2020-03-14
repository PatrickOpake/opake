(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('Organization', [function () {

			var Organization = function (data) {

				angular.extend(this, data);

				/*if (data.time_create) {
					this.time_create = moment(data.time_create).toDate();
				}*/

				this.is_active = (this.status == 'active');

			};

			return (Organization);
		}]);
})(opakeApp, angular);
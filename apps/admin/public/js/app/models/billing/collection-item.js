(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('BillingCollectionItem', ['$rootScope', function ($rootScope) {

			var BillingCollectionItem = function (data) {

				angular.extend(this, data);

				if (data) {
					if (data.time_start) {
						this.time_start = moment(data.time_start).toDate();
					}
				}
			};

			return (BillingCollectionItem);
		}]);
})(opakeApp, angular);
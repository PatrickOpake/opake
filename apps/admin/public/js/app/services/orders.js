// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('Orders', ['$http', '$rootScope', function ($http, $rootScope) {

			var self = this;

			this.getReceivedOrderItem = function (item_id) {
				return $http.get('/orders/ajax/received/' + $rootScope.org_id + '/orderItem/' + item_id).then(function (result) {
					return result.data;
				});
			};

		}]);
})(opakeApp, angular);

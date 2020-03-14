(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('Billing', ['$rootScope', function ($rootScope) {

			var Billing = function (data) {

				angular.extend(this, data);
				var self = this;

				if (data) {
					this.dos = moment(data.dos).toDate();
				}

				this.isReady = function () {
					return self.status === 'billing';
				};

				this.getStatus = function () {
					var status = 'Pending';
					if (self.status === 'coding') {
						status = '<a href="/cases/' + $rootScope.org_id + '/coding/' + self.id + '">Coding Required</a>';
					} else if (self.status === 'billing') {
						status = '<a href="/cases/' + $rootScope.org_id + '/claim/' + self.id + '">Ready To Bill</a>';
					}
					return status;
				};
			};

			return (Billing);
		}]);
})(opakeApp, angular);
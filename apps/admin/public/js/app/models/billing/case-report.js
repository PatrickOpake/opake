(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('BillingCaseReport', ['$rootScope', function ($rootScope) {

			var BillingCaseReport = function (data) {

				angular.extend(this, data);

				if (data) {
					this.dos = moment(data.dos).toDate();
					this.recent_payment = moment(data.recent_payment).toDate();
					if (data.case) {
						this.case.dos = moment(data.case.dos).toDate();
					}
				}
			};

			return (BillingCaseReport);
		}]);
})(opakeApp, angular);
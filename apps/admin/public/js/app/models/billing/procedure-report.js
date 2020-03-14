(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('BillingProcedureReport', ['$rootScope', function ($rootScope) {

			var BillingProcedureReport = function (data) {

				angular.extend(this, data);

				if (data) {
					this.dos = moment(data.dos).toDate();
					if (data.case) {
						this.case.dos = moment(data.case.dos).toDate();
					}
				}
			};

			return (BillingProcedureReport);
		}]);
})(opakeApp, angular);
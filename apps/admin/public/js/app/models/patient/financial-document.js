(function (opakeApp, angular, $) {
	'use strict';

	opakeApp.factory('PatientFinancialDocument', [function () {

			var PatientFinancialDocument = function (data) {

				angular.extend(this, data);

				if (angular.isDefined(data)) {
					this.uploaded_date = moment(data.uploaded_date).toDate();
				}

			};

			return (PatientFinancialDocument);
		}]);
})(opakeApp, angular, $);
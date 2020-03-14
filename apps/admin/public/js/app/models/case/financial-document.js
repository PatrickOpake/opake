(function (opakeApp, angular, $) {
	'use strict';

	opakeApp.factory('CaseFinancialDocument', [function () {

			var CaseFinancialDocument = function (data) {

				angular.extend(this, data);

				if (angular.isDefined(data)) {
					this.uploaded_date = moment(data.uploaded_date).toDate();
				}

			};

			return (CaseFinancialDocument);
		}]);
})(opakeApp, angular, $);
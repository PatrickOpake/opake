(function (opakeApp, angular, $) {
	'use strict';

	opakeApp.factory('CaseChart', [function () {

			var CaseChart = function (data) {

				angular.extend(this, data);

				if (angular.isDefined(data)) {
					this.uploaded_date = moment(data.uploaded_date).toDate();
				}

			};

			return (CaseChart);
		}]);
})(opakeApp, angular, $);
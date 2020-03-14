(function (opakeApp, angular, $) {
	'use strict';

	opakeApp.factory('PatientChart', [function () {

			var PatientChart = function (data) {

				angular.extend(this, data);

				if (angular.isDefined(data)) {
					this.uploaded_date = moment(data.uploaded_date).toDate();
				}

			};

			return (PatientChart);
		}]);
})(opakeApp, angular, $);
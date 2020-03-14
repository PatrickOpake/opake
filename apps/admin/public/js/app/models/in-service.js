(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('CaseInService', [function () {

			var CaseInService = function (data) {
				var self = this;

				angular.extend(this, data);

				if (!angular.isDefined(data)) {
					this.start = moment({hour: 12, minute: 0}).toDate();
					this.end = moment({hour: 12, minute: 15}).toDate();
				} else {
					this.start = moment(data.start).toDate();
					this.end = moment(data.end).toDate();
					this.time_start = moment(data.time_start).toDate();
					this.time_end = moment(data.time_end).toDate();
				}

			};

			return (CaseInService);
		}]);
})(opakeApp, angular);
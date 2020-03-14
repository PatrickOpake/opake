(function (opakeApp, angular, $) {
	'use strict';

	opakeApp.factory('CaseBlockingItem', [function () {

			var CaseBlockingItem = function (data) {

				angular.extend(this, data);

				if (angular.isDefined(data)) {
					this.start = moment(data.start).toDate();
					this.end = moment(data.end).toDate();
				}

			};

			return (CaseBlockingItem);
		}]);
})(opakeApp, angular, $);
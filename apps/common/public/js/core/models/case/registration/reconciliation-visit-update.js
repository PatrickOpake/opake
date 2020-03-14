(function (opakeCore, angular, $) {
	'use strict';

	opakeCore.factory('ReconciliationVisitUpdate', [function () {

			var ReconciliationVisitUpdate = function (data) {

				angular.extend(this, data);

				if (angular.isDefined(data)) {
					if (data.date) {
						this.date = moment(data.date).toDate();
					}
				}

				this.toJSON = function() {
					var copy = angular.copy(this);

					if (copy.date) {
						copy.date = moment(copy.date).format('YYYY-MM-DD');
					}

					return copy;
				};
			};

			return (ReconciliationVisitUpdate);
		}]);
})(opakeCore, angular, $);
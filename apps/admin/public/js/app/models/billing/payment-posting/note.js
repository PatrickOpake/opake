(function (opakeApp, angular, $) {
	'use strict';

	opakeApp.factory('BillingPaymentPostingNote', ['$filter', function ($filter) {

		var BillingPaymentPostingNote = function (data) {

			angular.extend(this, data);

			if (angular.isDefined(data)) {
				this.time_added = moment(data.time_added).toDate();
			}

			this.toJSON = function() {
				var copy = angular.copy(this);
				copy.time_added = moment(copy.time_added).format('YYYY-MM-DD HH:mm:ss');
				return copy;
			};

		};

		return (BillingPaymentPostingNote);
	}]);
})(opakeApp, angular, $);
(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('PaperClaim', ['$rootScope', function ($rootScope) {

			var PaperClaim = function (data) {

				angular.extend(this, data);

				if (data) {
					this.dos = moment(data.dos).toDate();
					this.dob = moment(data.dob).toDate();
				}
			};

			return (PaperClaim);
		}]);
})(opakeApp, angular);
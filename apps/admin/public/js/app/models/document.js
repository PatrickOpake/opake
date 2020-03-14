(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('Document', [function () {

			var Document = function (data) {

				angular.extend(this, data);

				this.uploaded = moment(data.uploaded).toDate();
			};

			return (Document);
		}]);
})(opakeApp, angular);
(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('ChatMessage', [function () {

			var ChatMessage = function (data) {
				angular.extend(this, data);

				if (angular.isDefined(data)) {
					this.date = moment(data.date).toDate();
				}
			};

			return (ChatMessage);
		}]);
})(opakeApp, angular);
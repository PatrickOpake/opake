(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('CaseTimeLog', [function () {

			var CaseTimeLog = function (data) {

				angular.extend(this, data);

				if (data.time) {
					this.time = moment(data.time, 'HH:mm:ss').toDate();
				}

				this.toJSON = function() {
					var copy = angular.copy(this);
					if (copy.time) {
						copy.time = moment(copy.time).format('HH:mm:ss');
					}
					return copy;
				};
			};

			return (CaseTimeLog);
		}]);
})(opakeApp, angular);
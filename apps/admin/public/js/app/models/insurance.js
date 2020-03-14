(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('Insurance', ['Insurances', function (Insurances) {

			var Insurance = function (data) {

				angular.extend(this, data);

				//TODO: Привести везде к одному типу
				this.status = !!parseInt(data.status);

				this.changeStatus = function(status) {
					this.status = status;
					Insurances.save(JSON.stringify(this));
				};
			};

			return (Insurance);
		}]);
})(opakeApp, angular);
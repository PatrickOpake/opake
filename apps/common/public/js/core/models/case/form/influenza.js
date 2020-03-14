(function (opakeCore, angular) {
	'use strict';

	opakeCore.factory('InfluenzaForm', [function () {

		var InfluenzaForm = function (data) {

			angular.extend(this, data);

			if (this.travel_outside_date) {
				this.travel_outside_date =  moment(this.travel_outside_date).toDate();
			}

			if (this.illnesses) {
				angular.forEach(this.illnesses, function(illness) {
					if (illness.date) {
						illness.date = moment(illness.date).toDate();
					}
				});
			}

			this.toJSON = function() {
				var copy = angular.copy(this);

				if (copy.travel_outside_date) {
					copy.travel_outside_date = moment(copy.travel_outside_date).format('YYYY-MM-DD');
				}
				if (copy.illnesses) {
					angular.forEach(copy.illnesses, function(illness) {
						if (illness.date) {
							illness.date = moment(illness.date).format('YYYY-MM-DD');
						}
					});
				}

				return copy;
			};

		};

		return (InfluenzaForm);
	}]);
})(opakeCore, angular);
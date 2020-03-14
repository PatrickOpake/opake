(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('CaseRegistration', [
		'PatientInsurance',
		function (PatientInsurance) {

		var CaseRegistration = function (data) {

			angular.extend(this, data);

			this.case.time_start = new Date(this.case.time_start);
			this.case.time_end = new Date(this.case.time_end);

			this.dob = new Date(this.dob);

			if (this.insurances) {
				var insurances = [];
				angular.forEach(this.insurances, function(item) {
					insurances.push(new PatientInsurance(item))
				});

				this.insurances = insurances;
			}

			this.getSurgeonNames = function() {
				return this.case.surgeons.map(function(v) {
					return v.full_name;
				}).join(', ');
			};

			this.addInsurance = function(insurance) {
				this.insurances.push(insurance);
			};

			this.toJSON = function() {
				var copy = angular.copy(this);
				if(copy.dob) {
					copy.dob = moment(copy.dob).format('YYYY-MM-DD');
				}
				if (copy.insurances) {
					angular.forEach(copy.insurances, function(ins) {
						if (ins.data.dob) {
							ins.data.dob = moment(ins.data.dob).format('YYYY-MM-DD');
						}
					});
				}
				return copy;
			};

		};

		return (CaseRegistration);
	}]);
})(opakeApp, angular);
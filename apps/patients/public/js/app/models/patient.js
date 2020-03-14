(function (opakeApp, angular) {
    'use strict';

    opakeApp.factory('Patient', [
        'PatientInsurance',
        function (PatientInsurance) {

            var Patient = function (data) {

                this.ec_relationship = "0";
                this.home_country_id = 235;

                angular.extend(this, data);
                var self = this;

                this.insurances = [];
                if (data) {
                    this.dob = moment(data.dob).toDate();

                    angular.forEach(data.insurances, function (insurance) {
                        self.insurances.push(new PatientInsurance(insurance));
                    });

                    if (data.documents) {
                        angular.forEach(data.documents, function(v) {
                            if (v.uploaded_date) {
                                v.uploaded_date =  moment(v.uploaded_date).toDate();
                            }
                            if(v.dos) {
                                v.dos =  moment(v.dos).toDate();
                            }
                        })
                    }
                }

                this.addInsurance = function(insurance) {
                    this.insurances.push(insurance);
                };

                this.toJSON = function() {
                    var copy = angular.copy(this);
	                if (copy.dob) {
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

            return (Patient);
        }]);
})(opakeApp, angular);
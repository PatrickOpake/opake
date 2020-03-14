(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('CaseRegistration', ['Patient', 'PatientInsurance', 'CaseRegistrationConst', function (Patient, PatientInsurance, CaseRegistrationConst) {

			var CaseRegistration = function (data) {
				this.auto_country = this.work_comp_country = {"id":"235","name":"USA"};

				angular.extend(this, data);

				var self = this;

				if (data.dob) {
					this.dob = moment(data.dob).toDate();
				}
				if (data.accident_date) {
					this.accident_date = moment(data.accident_date).toDate();
				}
				if (data.work_comp_accident_date) {
					this.work_comp_accident_date = moment(data.work_comp_accident_date).toDate();
				}
				if(data.time_start) {
					this.time_start = new Date(data.time_start);
				}
				if(data.time_end) {
					this.time_end = new Date(data.time_end);
				}
				if (data.patient) {
					this.patient = new Patient(data.patient);
				}

				if (data.insurances) {
					this.insurances = [];
					angular.forEach(data.insurances, function (insurance) {
						self.insurances.push(new PatientInsurance(insurance));
					});
				}

				this.isSelf = function() {
					return this.is_self_for_user;
				};

				this.addInsurance = function(insurance) {
					this.insurances.push(insurance);
				};

				this.isExistAnyDoc = function() {
					var result = [];
					angular.forEach(CaseRegistrationConst.DOCUMENTS, function(doc){
						if(!angular.isUndefined(data[doc.field]) && data[doc.field]) {
							result.push(doc.field);
						}
					});
					return result.length;
				};

				this.updateDocuments = function(documents) {
					this.documents = documents;
					angular.forEach(this.documents, function(v) {
						if (v.uploaded_date) {
							v.uploaded_date =  moment(v.uploaded_date).toDate()
						}
					})
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

				if (data.documents) {
					this.updateDocuments(data.documents);
				}

			};

			return (CaseRegistration);
		}]);
})(opakeApp, angular);
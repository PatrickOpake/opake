// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('Insurances', [
		'$http',
		'$q',
		'PatientInsurance',
		'PatientConst',
		'View',
		function ($http, $q, PatientInsurance, PatientConst, View) {

			var self = this;

			this.getPatientInsuranceMaster = function () {

				return new PatientInsurance({
					data: {
						carrier: {},
						member_id: '',
						effective: '',
						country: '',
						state: '',
						city: '',
						zip_code: '',
						address: '',
						oon_benefits: 0,
						pre_certification_required: 0,
						pre_certification_obtained: 0,
						self_funded: 0,
						is_oon_benefits_cap: 0,
						is_asc_benefits_cap: 0,
						is_pre_existing_clauses: 0,
						is_clauses_pertaining: 0,
						cpts: []
					}
				});
			};

			this.deleteInsurance = function(item, items, model, $scope) {
				$scope.dialog(View.get('app/insurance/confirm_delete.html'), $scope, {windowClass: 'alert'}).result.then(function () {
					var index = items.indexOf(item);
					if (index > -1) {
						items.splice(index, 1);
					}
					if (model && item.id) {
						if (!model.deleted_insurances) {
							model.deleted_insurances = [];
						}

						model.deleted_insurances.push(item.id);
					}
				});
			};

			this.checkRelationship = function(data) {
				if(data && data.insurances) {
					angular.forEach(data.insurances, function(insurance, key) {
						if (self.isSelfRelationshipToPatient(insurance)) {
							if (!insurance.data) {
								insurance.data = {};
							}
							insurance.data.suffix = data.suffix;
							insurance.data.first_name = data.first_name;
							insurance.data.middle_name = data.middle_name;
							insurance.data.last_name = data.last_name;
							insurance.data.gender = data.gender;
							insurance.data.dob = data.dob;
							insurance.data.country = data.home_country;
							insurance.data.state = data.home_state;
							insurance.data.city = data.home_city;
							insurance.data.zip_code = data.home_zip_code;
							insurance.data.address = data.home_address;
							insurance.data.apt_number = data.home_apt_number;
							insurance.data.phone = data.home_phone;
							insurance.data.ssn = data.ssn;
							insurance.data.custom_state = data.custom_home_state;
							insurance.data.custom_city = data.custom_home_city;
						}
					});
				}
			};

			this.relationshipToInsuredChange = function(insurance) {
				if(!self.isSelfRelationshipToPatient(insurance) ) {
					self.resetInsurancePatientInfo(insurance);
				}
			};

			this.resetInsurancePatientInfo = function(insurance) {
				insurance.suffix = null;
				insurance.first_name = null;
				insurance.middle_name = null;
				insurance.last_name = null;
				insurance.gender = null;
				insurance.dob = null;
				insurance.country = null;
				insurance.state = null;
				insurance.city = null;
				insurance.zip_code = null;
				insurance.address = null;
				insurance.apt_number = null;
				insurance.phone = null;
				insurance.ssn = null;
				insurance.custom_state = null;
				insurance.custom_city = null;
			};

			this.checkRelationshipToPatient = function(item, value) {
				return item.data && item.data.relationship_to_insured == value;
			};

			this.isSelfRelationshipToPatient = function(item) {
				return this.checkRelationshipToPatient(item, 0);
			};

			this.isDisplayAddInsurance = function(patient) {
				var insurancesCount = patient.insurances.length;
				var titlesCount = PatientConst.INSURANCE_TITLES.length;
				return (insurancesCount < titlesCount);
			};

			this.getCurrentInsuranceTitle = function(patient) {
				var insurancesCount = patient.insurances.length;
				return PatientConst.INSURANCE_TITLES[insurancesCount];
			};

			this.onRelationshipToInsuredChange = function(insurance, index, patient) {
				if (patient.insurances[index]) {
					self.relationshipToInsuredChange(insurance);
				}
			}
		}]);
})(opakeApp, angular);

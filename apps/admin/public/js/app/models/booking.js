(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('Booking', [
		'$rootScope',
		'Patient',
		'PatientInsurance',
		function ($rootScope, Patient, PatientInsurance) {

			var Booking = function (data) {

				var self = this;

				this.additional_cpts = [];
				this.admitting_diagnosis = [];
				this.pre_op_required_data = [];
				this.studies_ordered = [];
				this.users = [];
				this.insurances = [];

				angular.extend(this, data);

				if (angular.isDefined(data)) {
					this.time_start = moment(data.time_start).toDate();
					this.time_end = moment(data.time_end).toDate();
					this.patient = new Patient(data.patient);
					this.booking_patient = new Patient(data.booking_patient);

					if (this.date_of_injury) {
						this.date_of_injury = moment(data.date_of_injury).toDate();
					}
					if (this.unable_to_work_from) {
						this.unable_to_work_from = moment(data.unable_to_work_from).toDate();
					}
					if (this.unable_to_work_to) {
						this.unable_to_work_to = moment(data.unable_to_work_to).toDate();
					}

					if (data.insurances) {
						var insuranceModels = [];
						angular.forEach(this.insurances, function(insuranceData) {
							insuranceModels.push(new PatientInsurance(insuranceData));
						});
						this.insurances = insuranceModels;
					}
				} else {
					//Not specified
					this.patients_relations = '4';
					this.anesthesia_type = '6';
					this.admission_type = '3';
					this.location = '0';
					this.time_start = moment({hour: 12, minute: 0}).toDate();
					this.time_end = moment({hour: 12, minute: 15}).toDate();
					this.patient = new Patient();
					this.booking_patient = new Patient();
				}

				this.isSelf = function() {
					return this.is_self_for_user;
				};

				this.isRequiredFieldsForScheduleFilled = function () {
					return this.patient.last_name
						&& this.patient.first_name
						&& this.patient.dob
						&& this.patient.home_address
						&& this.patient.home_state
						&& this.patient.home_city
						&& this.patient.point_of_contact_phone
						&& this.patient.point_of_contact_phone_type
						&& this.patient.gender
						&& this.time_start
						&& this.time_end
						//&& this.additional_cpts && this.additional_cpts.length
						//&& this.admitting_diagnosis && this.admitting_diagnosis.length
						&& this.users && this.users.length;
				};

				this.toJSON = function() {
					var copy = angular.copy(this);
					if (copy.insurances) {
						angular.forEach(copy.insurances, function(ins) {
							if (ins.data.dob) {
								ins.data.dob = moment(ins.data.dob).format('YYYY-MM-DD');
							}
						});
					}

					return copy;
				};

				this.newEquipment = function(name) {
					return {
						id: null,
						name: name,
						full_name: name,
						type: 'Equipment',
						organization_id: $rootScope.org_id
					};
				};

				this.newImplant = function(name) {
					return {
						id: null,
						name: name,
						full_name: name,
						type: 'Implant',
						organization_id: $rootScope.org_id
					};
				};
			};

			return (Booking);
		}]);
})(opakeApp, angular);
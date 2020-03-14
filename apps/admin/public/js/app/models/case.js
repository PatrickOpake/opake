(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('Case', [
		'$rootScope',
		'$filter',
		'CaseNote',
		'CaseChart',
		'CaseFinancialDocument',
		'CaseRegistration',
		'CaseRegistrationConst',

		function ($rootScope, $filter, CaseNote, CaseChart, CaseFinancialDocument, CaseRegistration, CaseRegistrationConst) {

			var Case = function (data) {

				var self = this;

				angular.extend(this, data);

				this.notes = [];
				this.charts = [];
				this.financial_documents = [];
				if (angular.isDefined(data)) {
					this.time_start = moment(data.time_start).toDate();
					this.time_end = moment(data.time_end).toDate();
					this.time_check_in = moment(data.time_check_in).toDate();
					if (this.patient && this.patient.dob) {
						this.patient.dob = moment(data.patient.dob).toDate();
					}
					if (data.drivers_license) {
						this.drivers_license.uploaded_date = moment(data.drivers_license.uploaded_date).toDate();
					}
					if (data.insurance_card) {
						this.insurance_card.uploaded_date = moment(data.insurance_card.uploaded_date).toDate();
					}

					if (data.time_start_in_fact) {
						this.time_start_in_fact = moment(data.time_start_in_fact).toDate();
					}
					if (data.time_end_in_fact) {
						this.time_end_in_fact = moment(data.time_end_in_fact).toDate();
					}
					if (data.verification_completed_date) {
						this.verification_completed_date = moment(data.verification_completed_date).toDate();
					}
					if (this.date_of_injury) {
						this.date_of_injury = moment(data.date_of_injury).toDate();
					}
					if (this.unable_to_work_from) {
						this.unable_to_work_from = moment(data.unable_to_work_from).toDate();
					}
					if (this.unable_to_work_to) {
						this.unable_to_work_to = moment(data.unable_to_work_to).toDate();
					}

					angular.forEach(data.notes, function (note) {
						self.notes.push(new CaseNote(note));
					});

					if (data.charts) {
						angular.forEach(data.charts, function (chart) {
							self.charts.push(new CaseChart(chart));
						});
					}

					if (data.financial_documents) {
						angular.forEach(data.financial_documents, function (doc) {
							self.financial_documents.push(new CaseFinancialDocument(doc));
						});
					}

					if (data.registration) {
						self.registration = new CaseRegistration(data.registration)
					}
				} else {
					this.time_start = moment({hour: 12, minute: 0}).toDate();
					this.time_end = moment({hour: 12, minute: 15}).toDate();
					this.users = [];
					this.assistant = [];
				}

				this.getDate = function(){
					if ((new Date()).toDateString() === this.time_start.toDateString()) {
						return $filter('date')(this.time_start, 'h:mm a');
					} else {
						return $filter('date')(this.time_start, 'M/d/yyyy');
					}
				};

				this.isSelf = function() {
					return this.is_self_for_user;
				};

				this.changeType = function () {
					var cpts;

					if (!this.additional_cpts) {
						cpts = [];
					} else {
						cpts = angular.copy(this.additional_cpts);
					}

					var self = this;

					angular.forEach(cpts, function(cpt, i) {
						if (cpt.id == self.type.id) {
							cpts.splice(i, 1);
							return false;
						}
					});

					cpts.unshift(this.type);

					this.additional_cpts = cpts;
				};

				this.isAppointmentNew = function() {
					return this.appointment_status == CaseRegistrationConst.APPOINTMENT_STATUS.NEW;
				};

				this.isAppointmentCanceled = function() {
					return this.appointment_status == CaseRegistrationConst.APPOINTMENT_STATUS.CANCELED;
				};

				this.isAppointmentCompleted = function() {
					return this.appointment_status == CaseRegistrationConst.APPOINTMENT_STATUS.COMPLETED;
				};

				this.isVerificationBegin = function() {
					return this.verification_status == 0;
				};

				this.isVerificationContinue = function() {
					return this.verification_status == 1;
				};

				this.isVerificationCompleted = function() {
					return this.verification_status == 2;
				};

				this.isAppointmentStatusEditable = function () {
					if ((this.appointment_status == CaseRegistrationConst.APPOINTMENT_STATUS.CANCELED)
						|| (this.appointment_status == CaseRegistrationConst.APPOINTMENT_STATUS.COMPLETED)) {
						return false;
					}

					return true;
				};

				this.getSurgeonNames = function() {
					return this.users.map(function(v) {
						return v.fullname;
					}).join(', ');
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

			return (Case);
		}]);
})(opakeApp, angular);
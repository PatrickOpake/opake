(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('User', ['UserConst', function (UserConst) {

			var User = function (data) {

				angular.extend(this, data);

				if (data.time_first_login) {
					this.time_first_login = moment(data.time_first_login).toDate();
				}
				if (data.time_last_login) {
					this.time_last_login = moment(data.time_last_login).toDate();
				}
				if (data.dea_number_exp_date) {
					this.dea_number_exp_date = moment(data.dea_number_exp_date).toDate();
				}
				if (data.medical_licence_number_exp_date) {
					this.medical_licence_number_exp_date = moment(data.medical_licence_number_exp_date).toDate();
				}
				if (data.cds_number_exp_date) {
					this.cds_number_exp_date = moment(data.cds_number_exp_date).toDate();
				}


				this.is_active = (this.status == 'active');

				this.isSelf = function(user) {
					return (user.id == this.id);
				};

				this.isDoctor = function() {
					return this.role_id == UserConst.ROLES.DOCTOR;
				};

				this.isSatelliteOffice = function() {
					return this.role_id == UserConst.ROLES.SATELLITE_OFFICE;
				};

				this.isFullAdmin = function() {
					return this.role_id == UserConst.ROLES.FULL_ADMIN;
				};

				this.isBiller = function() {
					return this.role_id == UserConst.ROLES.BILLER;
				};

				this.isFullClinical = function() {
					return this.role_id == UserConst.ROLES.FULL_CLINICAL;
				};

				this.isAnesthesiologist = function () {
					return (this.profession_id == UserConst.PROFESSION.ANESTHESIOLOGIST)
				};

				this.isMedicalStaffType = function () {
					return ((this.profession_id == UserConst.PROFESSION.SURGEON)
						|| (this.profession_id == UserConst.PROFESSION.ANESTHESIOLOGIST)
						|| (this.profession_id == UserConst.PROFESSION.PHYSICIAN_ASSISTANT)
						|| (this.profession_id == UserConst.PROFESSION.NURSE_ANESTHETIST)
						|| (this.profession_id == UserConst.PROFESSION.NURSE_PRACTITIONER)
					);
				};

				this.canGenerateReports = function () {
					return this.is_enabled_op_report
						&& (!this.isDoctor() || this.isFullAdmin() || this.isSatelliteOffice());
				};

				this.toJSON = function() {
					var copy = angular.copy(this);

							if (copy.dea_number_exp_date) {
								copy.dea_number_exp_date = moment(copy.dea_number_exp_date).format('YYYY-MM-DD');
							}
							if (copy.medical_licence_number_exp_date) {
								copy.medical_licence_number_exp_date = moment(copy.medical_licence_number_exp_date).format('YYYY-MM-DD');
							}
							if (copy.cds_number_exp_date) {
								copy.cds_number_exp_date = moment(copy.cds_number_exp_date).format('YYYY-MM-DD');
							}


					return copy;
				};


			};

			return (User);
		}]);
})(opakeApp, angular);
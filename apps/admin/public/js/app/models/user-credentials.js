(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('UserCredentials', [function () {

			var UserCredentials = function (data) {

				angular.extend(this, data);

				if (angular.isDefined(data)) {
					if (data.medical_licence_exp_date) {
						this.medical_licence_exp_date = moment(data.medical_licence_exp_date).toDate();
					}
					if (data.dea_exp_date) {
						this.dea_exp_date = moment(data.dea_exp_date).toDate();
					}
					if (data.cds_exp_date) {
						this.cds_exp_date = moment(data.cds_exp_date).toDate();
					}
					if (data.insurance_exp_date) {
						this.insurance_exp_date = moment(data.insurance_exp_date).toDate();
					}
					if (data.insurance_reappointment_date) {
						this.insurance_reappointment_date = moment(data.insurance_reappointment_date).toDate();
					}
					if (data.acls_date) {
						this.acls_date = moment(data.acls_date).toDate();
					}
					if (data.immunizations_ppp_due) {
						this.immunizations_ppp_due = moment(data.immunizations_ppp_due).toDate();
					}
					if (data.immunizations_help_b) {
						this.immunizations_help_b = moment(data.immunizations_help_b).toDate();
					}
					if (data.immunizations_rubella) {
						this.immunizations_rubella = moment(data.immunizations_rubella).toDate();
					}
					if (data.immunizations_rubeola) {
						this.immunizations_rubeola = moment(data.immunizations_rubeola).toDate();
					}
					if (data.immunizations_varicela) {
						this.immunizations_varicela = moment(data.immunizations_varicela).toDate();
					}
					if (data.immunizations_mumps) {
						this.immunizations_mumps = moment(data.immunizations_mumps).toDate();
					}
					if (data.immunizations_flue) {
						this.immunizations_flue = moment(data.immunizations_flue).toDate();
					}
					if (data.retest_date) {
						this.retest_date = moment(data.retest_date).toDate();
					}
					if (data.licence_expr_date) {
						this.licence_expr_date = moment(data.licence_expr_date).toDate();
					}
					if (data.bls_date) {
						this.bls_date = moment(data.bls_date).toDate();
					}
					if (data.cnor_date) {
						this.cnor_date = moment(data.cnor_date).toDate();
					}
					if (data.malpractice_exp_date) {
						this.malpractice_exp_date = moment(data.malpractice_exp_date).toDate();
					}
					if (data.hp_exp_date) {
						this.hp_exp_date = moment(data.hp_exp_date).toDate();
					}

					if (data.npi_file) {
						this.npi_file.uploaded_date = moment(data.npi_file.uploaded_date).toDate();
					}
					if (data.medical_licence_file) {
						this.medical_licence_file.uploaded_date = moment(data.medical_licence_file.uploaded_date).toDate();
					}
					if (data.dea_file) {
						this.dea_file.uploaded_date = moment(data.dea_file.uploaded_date).toDate();
					}
					if (data.cds_file) {
						this.cds_file.uploaded_date = moment(data.cds_file.uploaded_date).toDate();
					}
					if (data.insurance_file) {
						this.insurance_file.uploaded_date = moment(data.insurance_file.uploaded_date).toDate();
					}
					if (data.acls_file) {
						this.acls_file.uploaded_date = moment(data.acls_file.uploaded_date).toDate();
					}
					if (data.immunizations_file) {
						this.immunizations_file.uploaded_date = moment(data.immunizations_file.uploaded_date).toDate();
					}
					if (data.licence_file) {
						this.licence_file.uploaded_date = moment(data.licence_file.uploaded_date).toDate();
					}
					if (data.bls_file) {
						this.bls_file.uploaded_date = moment(data.bls_file.uploaded_date).toDate();
					}
					if (data.cnor_file) {
						this.cnor_file.uploaded_date = moment(data.cnor_file.uploaded_date).toDate();
					}
					if (data.malpractice_file) {
						this.malpractice_file.uploaded_date = moment(data.malpractice_file.uploaded_date).toDate();
					}
					if (data.hp_file) {
						this.hp_file.uploaded_date = moment(data.hp_file.uploaded_date).toDate();
					}
				}

				this.toJSON = function() {
					var copy = angular.copy(this);

					if (copy.medical_licence_exp_date) {
						copy.medical_licence_exp_date = moment(copy.medical_licence_exp_date).format('YYYY-MM-DD');
					}
					if (copy.dea_exp_date) {
						copy.dea_exp_date = moment(copy.dea_exp_date).format('YYYY-MM-DD');
					}
					if (copy.cds_exp_date) {
						copy.cds_exp_date = moment(copy.cds_exp_date).format('YYYY-MM-DD');
					}
					if (copy.insurance_exp_date) {
						copy.insurance_exp_date = moment(copy.insurance_exp_date).format('YYYY-MM-DD');
					}
					if (copy.insurance_reappointment_date) {
						copy.insurance_reappointment_date = moment(copy.insurance_reappointment_date).format('YYYY-MM-DD');
					}
					if (copy.acls_date) {
						copy.acls_date = moment(copy.acls_date).format('YYYY-MM-DD');
					}
					if (copy.immunizations_ppp_due) {
						copy.immunizations_ppp_due = moment(copy.immunizations_ppp_due).format('YYYY-MM-DD');
					}
					if (copy.immunizations_help_b) {
						copy.immunizations_help_b = moment(copy.immunizations_help_b).format('YYYY-MM-DD');
					}
					if (copy.immunizations_rubella) {
						copy.immunizations_rubella = moment(copy.immunizations_rubella).format('YYYY-MM-DD');
					}
					if (copy.immunizations_rubeola) {
						copy.immunizations_rubeola = moment(copy.immunizations_rubeola).format('YYYY-MM-DD');
					}
					if (copy.immunizations_varicela) {
						copy.immunizations_varicela = moment(copy.immunizations_varicela).format('YYYY-MM-DD');
					}
					if (copy.immunizations_mumps) {
						copy.immunizations_mumps = moment(copy.immunizations_mumps).format('YYYY-MM-DD');
					}
					if (copy.immunizations_flue) {
						copy.immunizations_flue = moment(copy.immunizations_flue).format('YYYY-MM-DD');
					}
					if (copy.retest_date) {
						copy.retest_date = moment(copy.retest_date).format('YYYY-MM-DD');
					}
					if (copy.licence_expr_date) {
						copy.licence_expr_date = moment(copy.licence_expr_date).format('YYYY-MM-DD');
					}
					if (copy.bls_date) {
						copy.bls_date = moment(copy.bls_date).format('YYYY-MM-DD');
					}
					if (copy.acls_date) {
						copy.acls_date = moment(copy.acls_date).format('YYYY-MM-DD');
					}
					if (copy.cnor_date) {
						copy.cnor_date = moment(copy.cnor_date).format('YYYY-MM-DD');
					}
					if (copy.malpractice_exp_date) {
						copy.malpractice_exp_date = moment(copy.malpractice_exp_date).format('YYYY-MM-DD');
					}
					if (copy.hp_exp_date) {
						copy.hp_exp_date = moment(copy.hp_exp_date).format('YYYY-MM-DD');
					}

					return copy;
				};
			};

			return (UserCredentials);
		}]);
})(opakeApp, angular);
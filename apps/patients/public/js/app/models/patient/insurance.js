(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('PatientInsurance', ['PatientConst', function (PatientConst) {

			var PatientInsurance = function (data) {

				if (data.data === null) {
					data.data = {};
				}

				this.data = data.data || {};
				this.data.country_id = 235;

				angular.extend(this, data);

				if (this.data.effective) {
					this.data.effective =  moment(this.data.effective).toDate();
				}

				if (this.data.dob) {
					this.data.dob = moment(this.data.dob).toDate();
				}

				if (this.data.accident_date) {
					this.data.accident_date = moment(this.data.accident_date).toDate();
				}

				this.getTypeName = function() {
					if (!this.type) {
						return 'Insurance';
					} else {
						var text = PatientConst.INSURANCE_TYPES[this.type];
						if (this.type != 2) {
							if (this.insurance) {
								text += ' - ' + this.insurance.name;
							}
						}
						return text;
					}
				};

				this.isInsuranceCompanyEqualsType = function() {
					return (this.type == PatientConst.INSURANCE_TYPES_ID.MEDICARE ||
					this.type == PatientConst.INSURANCE_TYPES_ID.TRICARE ||
					this.type == PatientConst.INSURANCE_TYPES_ID.CHAMPVA ||
					this.type == PatientConst.INSURANCE_TYPES_ID.FECA_BLACK_LUNG);
				};

				this.getInsuranceTypeTitle = function () {
					var type = PatientConst.INSURANCE_TYPES[this.type];
					if (!type) {
						return '';
					}
					return type;
				};

				this.getTitle = function() {
					var type = PatientConst.INSURANCE_TYPES[this.type];
					if (!type) {
						return '';
					}
					if (this.type == PatientConst.INSURANCE_TYPES_ID.AUTO_ACCIDENT ||
						this.type == PatientConst.INSURANCE_TYPES_ID.WORKERS_COMP) {
						if (!this.data.insurance_name) {
							return type;
						}
						return type + ' - ' + this.data.insurance_name;
					}

					if (this.type == PatientConst.INSURANCE_TYPES_ID.LOP ||
						this.type == PatientConst.INSURANCE_TYPES_ID.SELF_PAY) {
						return type;
					}

					if (this.isInsuranceCompanyEqualsType()) {
						if (this.data.insurance_company_name && this.data.insurance_company_name != type) {
							return type + ' - ' + this.data.insurance_company_name;
						}
						return type;
					}

					var result = type;
					if (this.data.insurance) {
						result += ' - ' + this.data.insurance.name;
						if (result.length > 80) {
							result = result.substring(0, 80) + '...';
						}
					}

					return result;
				};


				this.toJSON = function() {
					var copy = angular.copy(this);
					if (copy.data.accident_date) {
						copy.data.accident_date = moment(copy.data.accident_date).format('YYYY-MM-DD');
					}
					return copy;
				};
			};

			return (PatientInsurance);
		}]);
})(opakeApp, angular);
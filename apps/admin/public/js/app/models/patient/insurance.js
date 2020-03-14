(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('PatientInsurance', ['$q', '$http', 'PatientConst', function ($q, $http, PatientConst) {


			var InsuranceHandlingStrategy = {};

			InsuranceHandlingStrategy.Blank = function(insurance) {
				this.getTitle = function() {
					var type = PatientConst.INSURANCE_TYPES[insurance.type];
					if (!type) {
						return '';
					}
					return type;
				};

				this.getTypeName = function() {
					if (!this.type) {
						return 'Insurance';
					}

					return PatientConst.INSURANCE_TYPES[insurance.type];
				};

				this.fillAddressFromSelected =  function() {

				};

				this.fillPayorInfo = function() {

				}
			};

			InsuranceHandlingStrategy.AutoAccident = function(insurance) {
				this.getTitle = function() {
					var result = PatientConst.INSURANCE_TYPES[insurance.type];

					if (insurance.data.insurance_company) {
						result += ' - ' + insurance.data.insurance_company.name;
					} else if (insurance.data.insurance_company_name) {
						result += ' - ' + insurance.data.insurance_company_name;
					}

					if (result.length > 80) {
						result = result.substring(0, 80) + '...';
					}

					return result;
				};

				this.getTypeName = function() {
					if (!this.type) {
						return 'Insurance';
					}
					var text = PatientConst.INSURANCE_TYPES[insurance.type];
					if (insurance.insurance_company) {
						text += ' - ' + insurance.insurance_company.name;
					}
					return text;
				};

				this.fillAddressFromSelected =  function() {
					var item = insurance;
					if (item.data.address_insurance_selected) {
						item.data.insurance_company_phone = item.data.address_insurance_selected.phone;
						item.data.state = item.data.address_insurance_selected.state;
						item.data.city = item.data.address_insurance_selected.city;
						item.data.zip = item.data.address_insurance_selected.zip_code;
					}
				};

				this.fillPayorInfo = function() {
					var insuranceItem = insurance;
					var def = $q.defer();
					if (insuranceItem.data.insurance_company.id) {
						$http.get('/insurances/ajax/getPayorInfo?id=' + insuranceItem.data.insurance_company.id).then(function (res) {

							insuranceItem.data.insurance_address = '';
							insuranceItem.data.insurance_company_phone = '';
							insuranceItem.data.state = null;
							insuranceItem.data.city = null;
							insuranceItem.data.zip = '';
							insuranceItem.data.eligibility_payer_id = '';
							insuranceItem.data.ub04_payer_id = '';
							insuranceItem.data.cms1500_payer_id = '';
							insuranceItem.data.address_insurance_selected = null;

							if (res.data.success) {

								var payorData = res.data.data;

								if (payorData.address_insurance_selected) {
									insuranceItem.data.address_insurance_selected = {
										id: payorData.address_insurance_selected.id,
										address: payorData.address_insurance_selected.address,
										is_new: false
									};
									insuranceItem.data.insurance_company_phone = payorData.address_insurance_selected.phone;
									insuranceItem.data.state = payorData.address_insurance_selected.state;
									insuranceItem.data.city = payorData.address_insurance_selected.city;
									insuranceItem.data.zip = payorData.address_insurance_selected.zip_code;
								}

								insuranceItem.data.eligibility_payer_id = payorData.navicure_eligibility_payor_id;
								insuranceItem.data.ub04_payer_id = payorData.ub04_payer_id;
								insuranceItem.data.cms1500_payer_id = payorData.cms1500_payer_id;
							}

							def.resolve();
						});

					}
					return def.promise;
				}
			};

			InsuranceHandlingStrategy.WorkersComp = InsuranceHandlingStrategy.AutoAccident;

			InsuranceHandlingStrategy.Regular = function(insurance) {
				this.getTitle = function() {
					var result = PatientConst.INSURANCE_TYPES[insurance.type];

					if (insurance.data.insurance) {
						result += ' - ' + insurance.data.insurance.name;
						if (result.length > 80) {
							result = result.substring(0, 80) + '...';
						}
					}

					return result;
				};

				this.getTypeName = function() {
					if (!this.type) {
						return 'Insurance';
					}
					var text = PatientConst.INSURANCE_TYPES[insurance.type];
					if (insurance.insurance) {
						text += ' - ' + insurance.insurance.name;
					}
					return text;
				};

				this.fillAddressFromSelected = function() {
					var item = insurance;
					if (item.data.address_insurance_selected) {
						item.data.provider_phone = item.data.address_insurance_selected.phone;
						item.data.insurance_state = item.data.address_insurance_selected.state;
						item.data.insurance_city = item.data.address_insurance_selected.city;
						item.data.insurance_zip_code = item.data.address_insurance_selected.zip_code;
					}
				};

				this.fillPayorInfo = function() {

					var insuranceItem = insurance;
					var def = $q.defer();
					if (insuranceItem.data.insurance.id) {
						$http.get('/insurances/ajax/getPayorInfo?id=' + insuranceItem.data.insurance.id).then(function (res) {

							insuranceItem.data.address_insurance = '';
							insuranceItem.data.provider_phone = '';
							insuranceItem.data.insurance_state = null;
							insuranceItem.data.insurance_city = null;
							insuranceItem.data.insurance_zip_code = '';
							insuranceItem.data.eligibility_payer_id = '';
							insuranceItem.data.ub04_payer_id = '';
							insuranceItem.data.cms1500_payer_id = '';
							insuranceItem.data.address_insurance_selected = null;

							if (res.data.success) {

								var payorData = res.data.data;

								if (payorData.address_insurance_selected) {
									insuranceItem.data.address_insurance_selected = {
										id: payorData.address_insurance_selected.id,
										address: payorData.address_insurance_selected.address,
										is_new: false
									};
									insuranceItem.data.provider_phone = payorData.address_insurance_selected.phone;
									insuranceItem.data.insurance_state = payorData.address_insurance_selected.state;
									insuranceItem.data.insurance_city = payorData.address_insurance_selected.city;
									insuranceItem.data.insurance_zip_code = payorData.address_insurance_selected.zip_code;
								}

								insuranceItem.data.eligibility_payer_id = payorData.navicure_eligibility_payor_id;
								insuranceItem.data.ub04_payer_id = payorData.ub04_payer_id;
								insuranceItem.data.cms1500_payer_id = payorData.cms1500_payer_id;
							}

							def.resolve();
						});

					}
					return def.promise;
				};
			};

			var PatientInsurance = function (data) {

				if (data.data === null) {
					data.data = {};
				}

				this.data = data.data || {};
				this.data.country_id = 235;

				angular.extend(this, data);

				var self = this;

				if (this.data.effective) {
					this.data.effective =  moment(this.data.effective).toDate();
				}

				if (this.data.dob) {
					this.data.dob = moment(this.data.dob).toDate();
				}

				if (this.data.accident_date) {
					this.data.accident_date = moment(this.data.accident_date).toDate();
				}

				this.newInsuranceCompany = function(name) {
					return {
						id: null,
						name: name
					};
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
					return this.getTypeStrategy().getTitle();
				};

				this.getTypeName = function() {
					return this.getTypeStrategy().getTypeName();
				};

				this.fillAddressFromSelected =  function() {
					return this.getTypeStrategy().fillAddressFromSelected();
				};

				this.fillPayorInfo = function() {
					return this.getTypeStrategy().fillPayorInfo();
				};

				this.toJSON = function() {
					var copy = angular.copy(this);
					if (copy.data.accident_date) {
						copy.data.accident_date = moment(copy.data.accident_date).format('YYYY-MM-DD');
					}
					return copy;
				};

				this.typeChanged = function() {

				};

				this.getTypeStrategy = function() {
					return new (this.determineHandlingStrategy())(this);
				};

				this.determineHandlingStrategy = function() {
					if (!this.type) {
						return InsuranceHandlingStrategy.Blank;
					}

					if (this.type == PatientConst.INSURANCE_TYPES_ID.AUTO_ACCIDENT) {
						return InsuranceHandlingStrategy.AutoAccident;
					}

					if (this.type == PatientConst.INSURANCE_TYPES_ID.WORKERS_COMP) {
						return InsuranceHandlingStrategy.WorkersComp;
					}

					if (this.type == PatientConst.INSURANCE_TYPES_ID.LOP ||
						this.type == PatientConst.INSURANCE_TYPES_ID.SELF_PAY) {
						return InsuranceHandlingStrategy.Blank;
					}

					return InsuranceHandlingStrategy.Regular;
				};

			};

			return (PatientInsurance);
		}]);
})(opakeApp, angular);
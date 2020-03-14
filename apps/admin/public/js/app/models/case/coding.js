(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('CaseCoding', ['$filter', 'CodingOccurrence', 'CodingValue', 'CodingInsurance', 'Source', 'PatientConst',
		function ($filter, CodingOccurrence, CodingValue, CodingInsurance, Source, PatientConst) {

			var CaseCoding = function (data) {
				var self = this;

				this.diagnoses = [];
				this.bills = [];

				angular.extend(this, data);

				this.occurrences = [];
				if (angular.isDefined(data) && data.occurrences && data.occurrences.length) {
					angular.forEach(data.occurrences, function (occurrence) {
						self.occurrences.push(new CodingOccurrence(occurrence));
					});
				} else {
					for (var i = 1; i <= 8; i++) {
						var occurrence = new CodingOccurrence();
						occurrence.coding_id = this.id;
						this.occurrences.push(occurrence);
					}
				}

				this.values = [];
				if (angular.isDefined(data) && data.values && data.values.length) {
					angular.forEach(data.values, function (value) {
						self.values.push(new CodingValue(value));
					});
				} else {
					for (var j = 1; j <= 12; j++) {
						var value = new CodingValue();
						value.coding_id = this.id;
						this.values.push(value);
					}
				}

				this.insurances = [];
				if (angular.isDefined(data) && data.insurances && data.insurances.length) {
					angular.forEach(data.insurances, function (insurance) {
						self.insurances.push(new CodingInsurance(insurance));
					});
					if (!$filter('filter')(self.insurances, {order_number: 1}).length) {
						generateBlankInsurance(1);
					}
					if (!$filter('filter')(self.insurances, {order_number: 2}).length) {
						generateBlankInsurance(2);
					}
				} else {
					generateBlankInsurance(1);
					generateBlankInsurance(2);
				}

				function generateBlankInsurance($order) {
					var insurance = new CodingInsurance();
					insurance.coding_id = self.id;
					insurance.order_number = $order.toString();
					self.insurances.push(insurance);
				}

				this.getDiagnosis = function (rowId) {
					return $filter('filter')(self.diagnoses, {row: rowId})[0];
				};

				this.preFillOccurrences = function (primaryInsuranceType, secondaryInsuranceType) {
					var primaryInsuranceFilled = false;

					Source.getOccurrenceCodes().then(function (result) {
						var occurrenceCodes = result;

						if (primaryInsuranceType) {
							if (primaryInsuranceType == PatientConst.INSURANCE_TYPES_ID.AUTO_ACCIDENT) {
								self.occurrences[0].occurrence_code = $filter('filter')(occurrenceCodes, {code: '2'}, true)[0];
								primaryInsuranceFilled = true;
							} else if (primaryInsuranceType == PatientConst.INSURANCE_TYPES_ID.WORKERS_COMP) {
								self.occurrences[0].occurrence_code = $filter('filter')(occurrenceCodes, {code: '4'}, true)[0];
								primaryInsuranceFilled = true;
							}
						}

						if (secondaryInsuranceType) {
							var occurrenceCodePosition = 0;
							if (primaryInsuranceFilled) {
								occurrenceCodePosition = 1;
							}
							if (secondaryInsuranceType == PatientConst.INSURANCE_TYPES_ID.AUTO_ACCIDENT) {
								self.occurrences[occurrenceCodePosition].occurrence_code = $filter('filter')(occurrenceCodes, {code: '2'}, true)[0];
							} else if (secondaryInsuranceType == PatientConst.INSURANCE_TYPES_ID.WORKERS_COMP) {
								self.occurrences[occurrenceCodePosition].occurrence_code = $filter('filter')(occurrenceCodes, {code: '4'}, true)[0];
							}
						}
					});
				};

			};

			return (CaseCoding);
		}]);
})(opakeApp, angular);
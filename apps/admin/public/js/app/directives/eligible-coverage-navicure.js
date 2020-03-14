(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('eligibleCoverageNavicure', [
		'View',
		'EligibleNavicureConst',
		function (View, EligibleNavicureConst) {
			return {
				restrict: "E",
				terminal: true,
				scope: {
					coverage: '=',
					latestUpdate: '='
				},
				controller: function ($scope, $filter) {
					var vm = this;
					vm.patient = {};
					vm.dependent = {};
					vm.insurance = {};
					vm.provider = {};
					vm.eligibleConst = EligibleNavicureConst;

					vm.updatePatient = function(coverage) {
						var patient = coverage.subscriber[0];
						vm.patient.first_name = patient.name.first_name;
						vm.patient.last_name = patient.name.last_name;
						vm.patient.dob = moment(patient.demographic.date_time_period, 'YYYYMMDD').format('YYYY-MM-DD');
						vm.patient.gender = getGenderName(patient.demographic.gender_code);
						vm.patient.address = formatAddress(patient);
						vm.patient.traceNumber = patient.traceNumbers[0].ref_id;
						var planNetworkId = $filter('filter')(patient.additionalIdentifications, {ref_id_qualifier: 'N6'});
						if(planNetworkId.length) {
							vm.patient.planNetworkId = planNetworkId[0].ref_id;
						}
						var memberId = $filter('filter')(patient.additionalIdentifications, {ref_id_qualifier: '1W'});
						if(memberId.length) {
							vm.patient.memberId = memberId[0].ref_id;
						}
						var planNumber = $filter('filter')(patient.additionalIdentifications, {ref_id_qualifier: '18'});
						if(planNumber.length) {
							vm.patient.planNumber = planNumber[0].ref_id;
						}
						var groupNumber = $filter('filter')(patient.additionalIdentifications, {ref_id_qualifier: '6P'});
						if(groupNumber.length) {
							vm.patient.groupNumber = groupNumber[0].ref_id;
						}
						var planBegin = $filter('filter')(patient.dates, {dateTimeQualifier: '346'});
						if(planBegin.length) {
							vm.patient.planBegin = moment(planBegin[0].dateTimePeriod, 'YYYYMMDD').format('YYYY-MM-DD');
						}
						var planEnd = $filter('filter')(patient.dates, {dateTimeQualifier: '347'});
						if(planEnd.length) {
							vm.patient.planEnd = moment(planEnd[0].dateTimePeriod, 'YYYYMMDD').format('YYYY-MM-DD');
						}
						var eligibilityEnd = $filter('filter')(patient.dates, {dateTimeQualifier: '356'});
						if(eligibilityEnd.length) {
							vm.patient.eligibilityEnd = moment(eligibilityEnd[0].dateTimePeriod, 'YYYYMMDD').format('YYYY-MM-DD');
						}
						var serviceDate = $filter('filter')(patient.dates, {dateTimeQualifier: '472'});
						if(serviceDate.length) {
							vm.patient.serviceDate = moment(serviceDate[0].dateTimePeriod, 'YYYYMMDD').format('YYYY-MM-DD');
						}
					};

					vm.updateDependent = function (coverage) {
						var patient = coverage.dependent[0];
						vm.dependent.first_name = patient.name.first_name;
						vm.dependent.last_name = patient.name.last_name;
						vm.dependent.dob = moment(patient.demographic.date_time_period, 'YYYYMMDD').format('YYYY-MM-DD');
						vm.dependent.gender = getGenderName(patient.demographic.gender_code);
						vm.dependent.address = formatAddress(patient);
						var planBegin = $filter('filter')(patient.dates, {dateTimeQualifier: '346'});
						if(planBegin.length) {
							vm.dependent.planBegin = moment(planBegin[0].dateTimePeriod, 'YYYYMMDD').format('YYYY-MM-DD');
						}
						var planEnd = $filter('filter')(patient.dates, {dateTimeQualifier: '347'});
						if(planEnd.length) {
							vm.dependent.planEnd = moment(planEnd[0].dateTimePeriod, 'YYYYMMDD').format('YYYY-MM-DD');
						}
						var eligibilityEnd = $filter('filter')(patient.dates, {dateTimeQualifier: '356'});
						if(eligibilityEnd.length) {
							vm.dependent.eligibilityEnd = moment(eligibilityEnd[0].dateTimePeriod, 'YYYYMMDD').format('YYYY-MM-DD');
						}
						var serviceDate = $filter('filter')(patient.dates, {dateTimeQualifier: '472'});
						if(serviceDate.length) {
							vm.dependent.serviceDate = moment(serviceDate[0].dateTimePeriod, 'YYYYMMDD').format('YYYY-MM-DD');
						}
					};

					vm.updateInsurance = function (coverage) {
						var org = coverage.info_source_detail[0].individualOrOrganizationalName;
						vm.insurance.name = org.last_name;
						vm.insurance.id = org.id_code;
						if(org.entity_type_qualifier) {
							vm.insurance.member_type = EligibleNavicureConst.ENTITY_TYPE_QUALIFIER[org.entity_type_qualifier];
						}
						if(org.entity_id_code) {
							vm.insurance.insurance_type = EligibleNavicureConst.ENTITY_IDENTIFIER_CODE_INSURANCE[org.entity_id_code];
						}
					};

					vm.updateProvider = function (coverage) {
						var org = coverage.info_receiver_detail[0].individualOrOrganizationalName;
						vm.provider.name = org.last_name;
						vm.provider.id = org.id_code;
						if(org.entity_id_code) {
							vm.provider.type = EligibleNavicureConst.ENTITY_IDENTIFIER_CODE_PROVIDER[org.entity_id_code];
						}
						if(org.entity_type_qualifier) {
							vm.provider.member_type = EligibleNavicureConst.ENTITY_TYPE_QUALIFIER[org.entity_type_qualifier];
						}
						vm.provider.additional_info = coverage.info_receiver_detail[0].referenceInformations;

					};

					vm.updateEligibilities = function (coverage) {
						vm.eligibilities = [];
						angular.forEach(coverage.subscriber[0].eligibilities, function (item) {
							item.patientType = 'Subscriber';
							vm.eligibilities.push(item);
						});
						if(angular.isDefined(coverage.dependent)) {
							angular.forEach(coverage.dependent[0].eligibilities, function (item) {
								item.patientType = 'Dependent';
								vm.eligibilities.push(item);
							});
						}

						angular.forEach(vm.eligibilities, function (item, key) {
							var benefitDate = $filter('filter')(item.eligibilityDates, {dateTimeQualifier: '292'});
							if(benefitDate.length) {
								if(benefitDate[0].dateTimePeriodFormatQualifier === 'D8') {
									item.benefitDate = moment(benefitDate[0].dateTimePeriod, 'YYYYMMDD').format('YYYY-MM-DD');
								}
								if(benefitDate[0].dateTimePeriodFormatQualifier === 'RD8') {
									var dates = benefitDate[0].dateTimePeriod.split('-');
									item.benefitDate = moment(dates[0], 'YYYYMMDD').format('YYYY-MM-DD') + ' - ' +
										moment(dates[1], 'YYYYMMDD').format('YYYY-MM-DD');
								}
							}
						});

						vm.ActiveCoverageFam = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode : '1'}});
						vm.PrimaryCareProviderFam = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode : 'L'}});
						vm.LimitationsFam = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode : 'F', yesNoConditionOrResponseCode2: 'W', coverageLevelCode: 'FAM'}});
						vm.LimitationsInd = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode : 'F', yesNoConditionOrResponseCode2: 'W', coverageLevelCode: 'IND'}});

						vm.InNetworkFamDeductible = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode: 'C', yesNoConditionOrResponseCode2: 'Y', coverageLevelCode : 'FAM'}});
						vm.InNetworkFamOutOfPocket = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode: 'G', yesNoConditionOrResponseCode2: 'Y', coverageLevelCode : 'FAM'}});
						vm.InNetworkFamLimitations = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode: 'F', yesNoConditionOrResponseCode2: 'Y', coverageLevelCode : 'FAM'}});

						vm.InNetworkIndDeductible = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode: 'C', yesNoConditionOrResponseCode2: 'Y', coverageLevelCode : 'IND'}});
						vm.InNetworkIndOutOfPocket = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode: 'G', yesNoConditionOrResponseCode2: 'Y', coverageLevelCode : 'IND'}});
						vm.InNetworkIndLimitations = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode: 'F', yesNoConditionOrResponseCode2: 'Y', coverageLevelCode : 'IND'}});

						vm.OutNetworkIndDeductible = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode: 'C', yesNoConditionOrResponseCode2: 'N', coverageLevelCode : 'IND'}});
						vm.OutNetworkIndOutOfPocket = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode: 'G', yesNoConditionOrResponseCode2: 'N', coverageLevelCode : 'IND'}});
						vm.OutNetworkIndLimitations = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode: 'F', yesNoConditionOrResponseCode2: 'N', coverageLevelCode : 'IND'}});

						vm.OutNetworkFamDeductible = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode: 'C', yesNoConditionOrResponseCode2: 'N', coverageLevelCode : 'FAM'}});
						vm.OutNetworkFamOutOfPocket = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode: 'G', yesNoConditionOrResponseCode2: 'N', coverageLevelCode : 'FAM'}});
						vm.OutNetworkFamLimitations = $filter('filter')(vm.eligibilities, {eligibility: {eligibilityOrBenefitInformationCode: 'F', yesNoConditionOrResponseCode2: 'N', coverageLevelCode : 'FAM'}});


					};


					function getGenderName(code) {
						return EligibleNavicureConst.GENDER[code];
					}

					function formatAddress(patient) {
						var result = '';
						if(!angular.isArray(patient.address)) {
							if(angular.isDefined(patient.address.address_info_1)) {
								result += patient.address.address_info_1;
							}
							if(angular.isDefined(patient.address.address_info_2)) {
								result += ' ' + patient.address.address_info_2;
							}
							if(patient.address.address_info_1 && patient.address.address_info_2) {
								result += ', ';
							}
						}
						if(!angular.isArray(patient.cityStateZip)) {
							if(angular.isDefined(patient.cityStateZip.city_name )) {
								result += patient.cityStateZip.city_name;
							}
							if(angular.isDefined(patient.cityStateZip.state)) {
								result += ', ' + patient.cityStateZip.state;
							}
							if(angular.isDefined(patient.cityStateZip.postal)) {
								result += ', ' + patient.cityStateZip.postal;
							}
						}
						return result;
					}

				},
				controllerAs: 'coverageVm',
				templateUrl: function () {
					return View.get('verification/eligible-table.html');
				},
				link: function (scope, elem, attrs, ctrl) {

					scope.$watch('coverage', function(newValue, oldValue) {
						if (newValue) {
							renderCoverage(newValue);
						}
					});

					function renderCoverage(coverage) {
						ctrl.updatePatient(coverage);
						ctrl.updateInsurance(coverage);
						ctrl.updateProvider(coverage);
						ctrl.updateEligibilities(coverage);
						if(angular.isDefined(coverage.dependent)) {
							ctrl.updateDependent(coverage);
						}
					}

				}

			};
		}]);

})(opakeApp, angular);

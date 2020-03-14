(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('EligibilityCoverageCtrl', [
		'$rootScope',
		'$scope',
		'$q',
		'$http',
		'$timeout',
		'$window',
		'Tools',

		function ($rootScope, $scope, $q, $http, $timeout, $window, Tools) {

			var vm = this;

			vm.eligibleErrors = null;
			vm.eligibleCoverage = null;
			vm.isEligibilityChecking = false;
			vm.isEligibilityLoaded = false;
			vm.hasEligibility = false;

			vm.selectedInsurance = null;
			vm.selectedInsuranceId = null;
			vm.caseRegistrationId = null;

			vm.initOptions = {};

			vm.init = function(caseRegistrationId, options) {
				vm.caseRegistrationId = caseRegistrationId;
				vm.initOptions = options || {};
			};

			vm.resetErrors = function () {
				vm.eligibleErrors = [];
			};

			vm.loadCoverage = function (insuranceId) {
				vm.eligibleErrors = null;
				vm.hasEligibility = false;
				vm.eligibleCoverage = null;

				return $http.get('/insurances/ajax/eligible/getCoverage', {params: {
						'caseRegistrationId': vm.caseRegistrationId,
						'caseInsuranceId': insuranceId,
					}}).then(function(result) {
						vm.isLoaded = true;
						if (result.data.success && result.data.coverage) {
							vm.eligibleCoverage  = result.data.coverage;
							vm.eligibleCoverageLatestUpdate = moment(result.data.latestUpdate).toDate();

							vm.hasEligibility = true;
						}
				}, function (error) {
					vm.eligibleErrors = [error.message];
				});
			};

			vm.checkInsuranceEligibility = function(insuranceId) {

				vm.eligibleCoverage = null;
				vm.eligibleErrors = null;
				vm.isEligibilityChecking = true;


				var params = {
					'case_insurance_id': insuranceId,
					'organization_id': $scope.org_id,
					'case_registration_id': vm.caseRegistrationId
				};

				return $http.post('/insurances/ajax/eligible/loadCoverage', $.param({data: JSON.stringify(params)})).then(function (result) {
					if (result.data.success) {
						vm.eligibleCoverage  = result.data.coverage;
						vm.eligibleCoverageLatestUpdate = moment(result.data.latestUpdate).toDate();
						vm.hasEligibility = true;
						$timeout(function() {
							vm.isEligibilityChecking = false;
						});
						$scope.$emit('Eligibility.Updated', vm);
						$scope.$broadcast('Eligibility.Updated', vm);
					} else {
						vm.eligibleErrors = result.data.errors;
						vm.isEligibilityChecking = false;
					}
				}, function() {
					vm.isEligibilityChecking = false;
				});
			};

			vm.isInsuranceSupportEligibility = function(insurance) {
				let type = parseInt(insurance.type);
				return type != 6 && type != 8;
			};
			
			vm.print = function (insuranceId) {
				var params = {
					'case_insurance_id': insuranceId,
					'organization_id': $scope.org_id,
					'case_registration_id': vm.caseRegistrationId
				};

				vm.isDocumentsLoading = true;
				$http.post('/insurances/ajax/eligible/exportEligibility/', $.param({data: JSON.stringify(params)})).then(function (res) {
					vm.isDocumentsLoading = false;
					if (res.data.success) {
						Tools.print(location.protocol + '//' + location.host + res.data.url);
					}
				}, function () {
					vm.isDocumentsLoading = false;
				});
			};


		}]);

})(opakeApp, angular);

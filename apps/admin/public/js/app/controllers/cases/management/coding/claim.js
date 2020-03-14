// Case Coding
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseCodingClaimCtrl', [
		'$scope',
		'$http',
		'$window',
		'$q',
		'BillingConst',
		function ($scope, $http, $window, $q, BillingConst) {

			$scope.BillingConst = BillingConst;

			var vm = this;

			var ELECTRONIC_UB04_CLAIM_TYPE_ID = 3;
			var ELECTRONIC_1500_CLAIM_TYPE_ID = 4;

			vm.caseId = null;
			vm.errors = null;
			vm.warnings = {
				common: null,
				institutional: null,
				professional: null
			};
			vm.claim = null;
			vm.isInitDone = false;
			vm.isLoading = false;
			vm.paperUB04Claim = false;
			vm.paper1500Claim = false;
			vm.electronicProfessionalClaim = false;
			vm.electronicInstitutionalClaim = false;
			vm.isAllowedToSendProfessionalClaim = true;
			vm.isAllowedToSendInstitutionalClaim = true;

			vm.init = function (caseId) {
				var def = $q.defer();
				vm.isInitDone = false;
				vm.errors = null;
				vm.warnings = {
					common: null,
					institutional: null,
					professional: null
				};
				vm.claim = null;
				vm.caseId = caseId;
				$http.get('/cases/ajax/coding/claim/' + $scope.org_id + '/getClaims/' + caseId).then(function(response) {
					if (response.data.success) {
						$http.get('/cases/ajax/coding/claim/' + $scope.org_id + '/checkCaseErrors/' + caseId).then(function (response) {
							if (response.data.errors) {
								if (response.data.errors.common) {
									vm.warnings.common = response.data.errors.common;
								}
								if (response.data.errors.professional) {
									vm.warnings.professional = response.data.errors.professional;
								}
								if (response.data.errors.institutional) {
									vm.warnings.institutional = response.data.errors.institutional;
								}
							}

							vm.isInitDone = true;
						});
						setActiveClaims(response.data.active_claims);
						vm.isInitDone = true;
					}

					return def.resolve();
				});

				return def.promise;
			};

			vm.sendNewClaim = function() {
				vm.warnings = {
					common: null,
					institutional: null,
					professional: null
				};
				vm.isLoading = true;
				var claimTypes = {
					paperUB04Claim: vm.paperUB04Claim,
					paper1500Claim: vm.paper1500Claim,
					electronicProfessionalClaim: vm.electronicProfessionalClaim,
					electronicInstitutionalClaim: vm.electronicInstitutionalClaim
				};
				var params = {'case': vm.caseId, 'claim_types': claimTypes};

				$http.post('/cases/ajax/coding/claim/' + $scope.org_id + '/sendClaim',
					$.param({'data': JSON.stringify(params)})
				).then(function (res) {
					if (!res.data.success) {
						vm.errors = res.data.errors;
						vm.isLoading = false;
					} else {
						vm.errors = null;
						if(vm.paperUB04Claim || vm.paper1500Claim) {
							$window.location = '/billings/claims-management/' + $scope.org_id + '/paperClaims';
						} else {
							$http.get('/cases/ajax/coding/claim/' + $scope.org_id + '/getClaims/' + vm.caseId).then(function(response) {
								if (response.data.success) {
										vm.paperUB04Claim = false;
										vm.paper1500Claim = false;
										vm.electronicProfessionalClaim = false;
										vm.electronicInstitutionalClaim = false;
										setActiveClaims(response.data.active_claims);
								} else {
									vm.errors = response.data.errors;
								}

								vm.isLoading = false;
							});
						}

						$scope.$emit('Billing.ClaimSent');
						$scope.$broadcast('Billing.ClaimSent');
					}
				});
			};

			vm.forceUpdateState = function() {
				vm.isLoading = true;
				$http.post('/cases/ajax/coding/claim/' + $scope.org_id + '/forceUpdateStatus').then(function(response) {
					$http.get('/cases/ajax/coding/claim/' + $scope.org_id + '/getClaims/' + vm.caseId).then(function(response) {
						setActiveClaims(response.data.active_claims);
						vm.isLoading = false;
					});
				});
			};

			vm.isShowForceUpdate = function(claim) {
				return claim.status == BillingConst.NAV_CLAIM_STATUSES.ACCEPTED_BY_PROVIDER || claim.status == BillingConst.NAV_CLAIM_STATUSES.SENT;
			};

			vm.isShowSendClaimAgain = function() {
				return claim.status == BillingConst.NAV_CLAIM_STATUSES.REJECTED_BY_PROVIDER || claim.status == BillingConst.NAV_CLAIM_STATUSES.REJECTED_BY_PAYOR;
			};

			vm.isSelectedClaimType = function () {
				return (vm.paperUB04Claim || vm.paper1500Claim || vm.electronicProfessionalClaim || vm.electronicInstitutionalClaim);
			};

			vm.markAsReadyToSend = function () {
				if (vm.isInitDone && !vm.errors) {
					var electronicProfessionalClaim = false;
					var electronicInstitutionalClaim = false;

					if (!vm.warnings.common || !vm.warnings.common.length) {
						if (!vm.warnings.institutional || !vm.warnings.institutional.length) {
							electronicInstitutionalClaim = vm.electronicInstitutionalClaim;
						}
						if (!vm.warnings.institutional || !vm.warnings.professional.length) {
							electronicProfessionalClaim = vm.electronicProfessionalClaim;
						}
					}

					var claimTypes = {
						electronicProfessionalClaim: electronicProfessionalClaim,
						electronicInstitutionalClaim: electronicInstitutionalClaim
					};

					if (claimTypes.electronicProfessionalClaim || claimTypes.electronicInstitutionalClaim) {
						var params = {'case': vm.caseId, 'claim_types': claimTypes};
						vm.isLoading = true;
						$http.post('/cases/ajax/coding/claim/' + $scope.org_id + '/markAsReadyToSend/' + vm.caseId,
							$.param({'data': JSON.stringify(params)})
						).then(function(response) {
								if(response.data) {
									vm.init(vm.caseId).then(function() {
										vm.markedTypeOfClaims = response.data;
										vm.isLoading = false;
									});
								}
							});
					}
				}
			};

			$scope.$on('Billing.CodingSaved', function() {
				if (vm.caseId) {
					vm.init(vm.caseId);
				}
			});

			function setActiveClaims(activeClaims) {
				vm.activeClaims = activeClaims;
				vm.isAllowedToSendInstitutionalClaim = true;
				vm.isAllowedToSendProfessionalClaim = true;
				angular.forEach(vm.activeClaims, function(claim) {
					if (claim.type == ELECTRONIC_UB04_CLAIM_TYPE_ID) {
						vm.isAllowedToSendInstitutionalClaim = claim.can_send_new;
					} else if (claim.type == ELECTRONIC_1500_CLAIM_TYPE_ID) {
						vm.isAllowedToSendProfessionalClaim = claim.can_send_new;
					}
				});
			}

		}]);

})(opakeApp, angular);

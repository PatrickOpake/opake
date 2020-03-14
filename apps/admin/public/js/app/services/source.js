// TODO; перенести из cases
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('Source', [
		'$http',
		'$rootScope',
		'$q',
		'UserConst',

		function ($http, $rootScope, $q, UserConst) {

			var data = {};

			var getData = function (src, params, notCache) {
				var deferred = $q.defer();
				var key = src;
				if (params) {
					key += JSON.stringify(params);
				}
				if (angular.isDefined(data[key]) && !notCache) {
					deferred.resolve(data[key]);
				} else {
					$http.get(src, {params: params}).then(function (result) {
						data[key] = result.data;
						deferred.resolve(result.data);
					});
				}
				return deferred.promise;
			};

			this.getCountries = function () {
				return getData('/geo/countries');
			};

			this.getStates = function () {
				return getData('/geo/states');
			};

			this.getCities = function (stateId, query, notCache) {
				var params = {
					state_id: stateId
				};
				if (query) {
					params.query = query;
				}
				return getData('/geo/cities', params, notCache);
			};

			this.getLanguages = function () {
				return getData('/ajax/languages/');
			};

			this.getProfessions = function () {
				return getData('/clients/ajax/professions/');
			};

			this.getRoles = function () {
				return getData('/clients/ajax/roles/');
			};

			this.getOrganizations = function () {
				return getData('/clients/ajax/org/', {org: $rootScope.org_id});
			};

			this.getDepartments = function () {
				return getData('/clients/ajax/departmentsList/', {org: $rootScope.org_id});
			};

			this.getSites = function () {
				return getData('/clients/ajax/site/', {org: $rootScope.org_id});
			};

			this.getLocations = function (notCache) {
				return getData('/clients/ajax/location/', {org: $rootScope.org_id}, notCache);
			};

			this.getStorage = function (notCache) {
				return getData('/clients/ajax/storage/', {org: $rootScope.org_id}, notCache);
			};

			this.getInsurances = function (q, notCache, withoutEmpty, insuranceType) {
				var params = {
					query: q
				};

				if (insuranceType) {
					params.insuranceType = insuranceType;
				}

				if (!withoutEmpty) {
					params.includeEmpty = true;
				}

				return getData('/insurances/ajax/insurances/', params, notCache);
			};

			this.getCaseInsurancesForCoding = function(caseId) {
				return getData('/cases/ajax/coding/' + $rootScope.org_id + '/caseInsuranceOptions/' + caseId);
			};

			this.getInsuranceCompanyPossibleAddresses = function(insuranceCompanyId) {
				return getData('/insurances/ajax/getPossiblePayorAddresses?id=' + insuranceCompanyId, null, true);
			};

			this.getUsers = function (searchParams, notCache) {
				return getData('/users/ajax/' + $rootScope.org_id + '/users/', searchParams, notCache);
			};

			this.getSurgeons = function(notCache) {
				return this.getUsers({
					role: UserConst.ROLES.DOCTOR
				}, notCache);
			};

			this.getSurgeonsAndPracticeGroups = function(notCache) {
				return getData('/users/ajax/' + $rootScope.org_id + '/usersAndPractices/', notCache);
			};

			this.getSurgeonsAndAssistant = function(notCache) {
				return this.getUsers({
					profession: UserConst.PROFESSION.PHYSICIAN_ASSISTANT,
					role: UserConst.ROLES.DOCTOR
				}, notCache);
			};

			this.getMedicalStaffs = function(notCache) {
				return this.getUsers({
					profession: JSON.stringify([
						UserConst.PROFESSION.SURGEON,
						UserConst.PROFESSION.ANESTHESIOLOGIST,
						UserConst.PROFESSION.PHYSICIAN_ASSISTANT,
						UserConst.PROFESSION.NURSE_ANESTHETIST,
						UserConst.PROFESSION.NURSE_PRACTITIONER
					])
				}, notCache);
			};

			this.getNonSurgicalStaffs = function (notCache) {
				return getData('/users/ajax/' + $rootScope.org_id + '/nonSurgicalUsers/', notCache);
			};

			this.getAnesthesiologists = function() {
				return this.getUsers({
					profession: UserConst.PROFESSION.ANESTHESIOLOGIST
				});
			};

			this.getCaseTypes = function (q, yearAdding, usedCodesIds) {
				var usedCodesIdsStr = '';
				if (usedCodesIds) {
					usedCodesIdsStr = usedCodesIds.join(',');
				}
				return getData('/settings/case-types/ajax/' + $rootScope.org_id + '/caseTypes', {query: q, year_adding: yearAdding, used_codes_ids: usedCodesIdsStr});
			};

			this.getCptCodes = function (q, yearAdding, usedCodesIds) {
				var usedCodesIdsStr = '';
				if (usedCodesIds) {
					usedCodesIdsStr = usedCodesIds.join(',');
				}
				return getData('/ajax/cpts/', {query: q, year_adding: yearAdding, used_codes_ids: usedCodesIdsStr});
			};

			this.getCaseBillingCodes = function(q, caseId, usedCodesIds) {
				var usedCodesIdsStr = '';
				if (usedCodesIds) {
					usedCodesIdsStr = usedCodesIds.join(',');
				}
				return getData('/billings/ajax/' + $rootScope.org_id + '/caseCodes/', {query: q, case_id: caseId, used_codes_ids: usedCodesIdsStr});
			};

			this.getLastYearCptCodes = function (q) {
				return getData('/ajax/lastYearCpts/', {query: q});
			};

			this.getCpts = function (q) {
				return this.getCaseTypes(q);
			};

			this.getIcds = function (q, yearAdding, usedCodesIds) {
				var usedCodesIdsStr = '';
				if (usedCodesIds) {
					usedCodesIdsStr = usedCodesIds.join(',');
				}
				return getData('/ajax/icds/', {query: q, year_adding: yearAdding, used_codes_ids: usedCodesIdsStr});
			};

			this.getDischargeStatusCodes = function (q) {
				return getData('/ajax/dischargeStatusCodes/', {query: q});
			};

			this.getConditionCodes = function (q) {
				return getData('/ajax/conditionCodes/', {query: q});
			};

			this.getOccurrenceCodes = function (q, usedCodesIds) {
				var usedCodesIdsStr = '';
				if (usedCodesIds) {
					usedCodesIdsStr = usedCodesIds.join(',');
				}
				return getData('/ajax/occurrenceCodes/', {query: q, used_codes_ids: usedCodesIdsStr});
			};

			this.getValueCodes = function (q, usedCodesIds) {
				var usedCodesIdsStr = '';
				if (usedCodesIds) {
					usedCodesIdsStr = usedCodesIds.join(',');
				}
				return getData('/ajax/valueCodes/', {query: q, used_codes_ids: usedCodesIdsStr});
			};

			this.getPatients = function (params) {
				return getData('/patients/ajax/' + $rootScope.org_id + '/patients', params);
			};

			this.getUserPatients = function(q) {
				return getData('/patients/ajax/' + $rootScope.org_id + '/userPatients', {query: q});
			};

			this.getVendors = function (q, type) {
				return getData('/vendors/ajax/' + $rootScope.org_id + '/search', {query: q, type: type});
			};

			this.getShippingTypes = function () {
				return getData('/orders/ajax/received/' + $rootScope.org_id + '/shippingTypes');
			};

			this.getInventoryTypes = function () {
				return getData('/inventory/ajax/' + $rootScope.org_id + '/types');
			};

			this.getInventoryUoms = function () {
				return getData('/inventory/ajax/' + $rootScope.org_id + '/uoms');
			};

			this.getFullInventoryTypes = function () {
				return getData('/inventory/ajax/' + $rootScope.org_id + '/fullTypes');
			};

			this.getEquipments = function(q) {
				return this.getInventoryItems(q, 'Equipment');
			};

			this.getImplants = function(q) {
				return this.getInventoryItems(q, 'Implant');
			};

			this.getInventoryItems = function (q, type) {
				return getData('/inventory/ajax/' + $rootScope.org_id + '/searchItems', {query: q, type: type});
			};

			this.getInventoryInvoices = function (q) {
				return getData('/inventory/invoices/ajax/' + $rootScope.org_id + '/searchInvoices', {query: q});
			};

			this.getUserActivityTypes = function () {
				return getData('/analytics/ajax/userActivityTypes');
			};

			this.getUsersForAllOrganizations = function (searchParams) {
				return getData('/users/ajax/internal/users/', searchParams);
			};

			this.getPatientsForAllOrganizations = function(q) {
				return getData('/users/ajax/internal/patients/', {query: q});
			};

			this.getMasterItems = function (q, type) {
				return getData('/master/ajax/' + $rootScope.org_id + '/searchMasterItems', {query: q});
			};

			this.getPracticeGroups = function() {
				return getData('/organizations/ajax/' + $rootScope.org_id + '/allPracticeGroups');
			};

			this.getPracticeGroupsForOrganization = function (organizationId) {
				return getData('/organizations/ajax/' + organizationId + '/allowedPracticeGroups');
			};

			this.getPrefCardStages = function() {
				return getData('/organizations/ajax/' + $rootScope.org_id + '/getPrefCardStages');
			};

			this.getAllPrefCardStages = function() {
				return getData('/organizations/ajax/' + $rootScope.org_id + '/getAllPrefCardStages');
			};

			this.getMaserChargeCPT = function (q) {
				return getData('/clients/sites/ajax/' + $rootScope.org_id + '/charges-master/searchCPT', {query: q});
			};

			this.getLedgerActivityPaymentSource  = function() {
				return getData('/billings/ledger-payment-activity/ajax/' + $rootScope.org_id + '/paymentSources');
			};

			// TODO: убрать как нибудь
			this.getList = function (src, q) {
				var deferred = $q.defer();
				$http.get(src, {params: {
						org: $rootScope.org_id,
						query: q
					}}).then(function (data) {
					deferred.resolve(data.data);
				});
				return deferred.promise;
			};

			this.getData = getData;

		}]);
})(opakeApp, angular);

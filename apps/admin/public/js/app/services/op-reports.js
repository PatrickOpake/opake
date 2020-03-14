// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('OpReports', [
		'$http',
		'$rootScope',
		'$q',
		function ($http, $rootScope, $q) {
			var self = this;

			this.templateFields = [
				'anesthesia_administered',
				'drains',
				'consent',
				'complications',
				'approach',
				'description_procedure',
				'follow_up_care',
				'conditions_for_discharge',
				'clinical_history',
				'total_tourniquet_time',
				'ebl',
				'blood_transfused',
				'fluids',
				'urine_output',
				'findings',
				'specimens_removed',
				'scribe'
			];

			this.saveTemplate = function (data, callback) {
				return $http.post('/operative-reports/ajax/save/' + $rootScope.org_id + '/template/', $.param({
					data: JSON.stringify(data)
				})).then(function (result) {
					if(callback) {
						callback(result);
					}
				});
			};

			this.save = function (data, params, callback) {
				if (!angular.isObject(params)) {
					params = {
						future: params
					};
				}
				params.data = JSON.stringify(data);
				return $http.post('/operative-reports/ajax/save/' + $rootScope.org_id + '/report/', $.param(params)).then(function (result) {
					if(callback) {
						callback(result);
					}
				});
			};

			this.saveFutureTemplate = function (data, callback) {
				return $http.post('/operative-reports/ajax/save/' + $rootScope.org_id + '/futureReport/', $.param({
					data: JSON.stringify(data)
				})).then(function (result) {
					if(callback) {
						callback(result);
					}
				});
			};

			this.changeBulkStatus = function(reportIds, status, callback) {
				return $http.post('/operative-reports/ajax/' + $rootScope.org_id + '/changeBulkStatus/', $.param({reportIds: reportIds})).then(function (result) {
					if(callback) {
						callback(result);
					}
				});
			};

			this.archive = function(reportIds, callback) {
				return $http.post('/operative-reports/ajax/' + $rootScope.org_id + '/archive/', $.param({reportIds: reportIds})).then(function (result) {
					if(callback) {
						callback(result);
					}
				});
			};

			this.changeStatus = function(reportId, status, callback) {
				return $http.post('/operative-reports/ajax/' + $rootScope.org_id + '/changeStatus/' + reportId, $.param({status: status})).then(function (result) {
					if(callback) {
						callback(result);
					}
				});
			};

			this.copyFromTemplate = function(report, template, caseItem) {

				var dynamicFields = extractDynamicFieldValues(caseItem);

				return getCardInventories(dynamicFields, template, caseItem).then(function () {
					angular.forEach(self.templateFields, function (fieldName) {
						report[fieldName] = replaceDynamicFields(template[fieldName], dynamicFields);
					});
					report.applied_template = template;
					angular.forEach(template.template, function (group) {
						angular.forEach(group, function (item) {
							if(item.field === 'custom' && item.custom_value) {
								item.custom_value = replaceDynamicFields(item.custom_value, dynamicFields)
							}
						});
					});
				});
			};

			function replaceDynamicFields(text, dynamicFields) {
				if (text) {
					angular.forEach(dynamicFields, function(dynamicField) {
						var value = dynamicField.value;
						if (angular.isUndefined(value) || value === null) {
							value = '';
						}
						text = replaceAll(text.toString(), dynamicField.tag, value)

					});
				} else {
					text = '';
				}
				return text;
			}

			function replaceAll(str, find, replace) {
				return str.replace(new RegExp(find, 'ig'), replace);
			}

			function extractDynamicFieldValues(caseItem) {

				var dynamicFields = {
					firstName: {
						tag: '%FirstName%'
					},
					lastName: {
						tag: '%LastName%'
					},
					age: {
						tag: '%Age%'
					},
					dob: {
						tag: '%DOB%'
					},
					gender: {
						tag: '%Gender%'
					},
					gender2: {
						tag: '%Gender2%'
					},
					street: {
						tag: '%Street%'
					},
					city: {
						tag: '%City%'
					},
					state: {
						tag: '%State%'
					},
					country: {
						tag: '%Country%'
					},
					zip: {
						tag: '%Zip%'
					},
					mrn: {
						tag: '%MRN%'
					},
					physician: {
						tag: '%Physician%'
					},
					dos: {
						tag: '%DOS%'
					},
					insurance: {
						tag: '%Insurance%'
					},
					siteName: {
						tag: '%SiteName%'
					},
					siteAddress: {
						tag: '%SiteAddress%'
					},
					siteCity: {
						tag: '%SiteCity%'
					},
					siteState: {
						tag: '%SiteState%'
					},
					siteCountry: {
						tag: '%SiteCountry%'
					},
					siteZip: {
						tag: '%SiteZip%'
					},
					sitePhone: {
						tag: '%SitePhone%'
					},
					account: {
						tag: '%Account%'
					},
					apt: {
						tag: '%Apt%'
					}
				};

				dynamicFields.firstName.value = caseItem.patient.first_name;
				dynamicFields.lastName.value = caseItem.patient.last_name;
				dynamicFields.age.value = caseItem.patient.age;
				dynamicFields.gender.value = caseItem.patient.sex;

				if (caseItem.patient.sex == 'Male') {
					dynamicFields.gender2.value = 'He';
				}
				if (caseItem.patient.sex == 'Female') {
					dynamicFields.gender2.value = 'She';
				}
				if (caseItem.patient.sex == 'Unknown' || caseItem.patient.sex == 'Transgender') {
					dynamicFields.gender2.value = 'They';
				}

				dynamicFields.dob.value = moment(caseItem.patient.dob).format('MM/DD/YYYY');
				dynamicFields.street.value = caseItem.patient.home_address;
				dynamicFields.city.value = caseItem.patient.home_city_name;
				dynamicFields.state.value = caseItem.patient.home_state_name;
				dynamicFields.country.value = caseItem.patient.home_country_name;
				dynamicFields.zip.value = caseItem.patient.home_zip_code;
				dynamicFields.mrn.value = caseItem.patient.full_mrn;
				dynamicFields.physician.value = caseItem.getSurgeonNames();
				dynamicFields.dos.value = moment(caseItem.time_start).format('MM/DD/YYYY');
				dynamicFields.insurance.value = caseItem.registration.primary_insurance_title;
				dynamicFields.siteName.value = caseItem.location.site_name;
				dynamicFields.siteAddress.value = caseItem.location.site_address;
				dynamicFields.siteCity.value = caseItem.location.site_city_name;
				dynamicFields.siteState.value = caseItem.location.site_state_name;
				dynamicFields.siteCountry.value = caseItem.location.site_country_name;
				dynamicFields.siteZip.value = caseItem.location.site_zip;
				dynamicFields.sitePhone.value = caseItem.location.site_phone;
				dynamicFields.account.value = caseItem.id;
				dynamicFields.apt.value = caseItem.patient.home_apt_number;

				return dynamicFields;
			}

			function getCardInventories(dynamicFields, template, caseItem) {
				var getMatchesFromText = function (text) {
					var matches = [];
					var tag = /%\d+%/g;
					if(text) {
						matches = text.toString().match(tag);
					}
					return matches;
				};

				var getNumberFromMatches = function (match) {
					return match.replace(new RegExp('%', 'g'), '');
				};

				var inventoryIds = [];
				angular.forEach(self.templateFields, function (fieldName) {
					angular.forEach(getMatchesFromText(template[fieldName]), function (match) {
						inventoryIds.push(getNumberFromMatches(match));
					});
				});

				angular.forEach(template.template, function(group) {
					angular.forEach(group, function (item) {
						if(item.field === 'custom' && item.custom_value) {
							angular.forEach(getMatchesFromText(item.custom_value), function (match) {
								var inventoryId = getNumberFromMatches(match);
								if (inventoryIds.indexOf(inventoryId) == -1) {
									inventoryIds.push(inventoryId);
								}
							});
						}

					});
				});

				return $http.post('/cases/ajax/' + $rootScope.org_id + '/cardInventories/' + caseItem.id, $.param({
					IDs: inventoryIds
				})).then(function (result) {
					if(result.data) {
						angular.forEach(result.data, function (item) {
							var text = ' ';
							if(item.actual_use) {
								text += item.actual_use + ' ';
							}
							text += item.uom_name + ' ' + item.name;

							dynamicFields['inventory_' + item.id] = {
								tag: '%' + item.id + '%',
								value: text
							}
						});
					}
				});
			}

		}]);
})(opakeApp, angular);

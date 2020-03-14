// Case Operative Report
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('OperativeReportCtrl', [
		'$scope',
		'$http',
		'$q',
		'$window',
		'$filter',
		'$location',
		'$sce',
		'$controller',
		'View',
		'OpReports',
		'OperativeReport',
		'OperativeReportTemplate',
		'OperativeReportFutureTemplate',
		'OperativeReportConst',
		'Tools',
		'Permissions',
		'CaseManagement',
		'Case',
		'OperativeReportTemplateConst',
		'BeforeUnload',
		function ($scope, $http, $q, $window, $filter, $location, $sce, $controller, View, OpReports, OperativeReport, OperativeReportTemplate, OperativeReportFutureTemplate, OperativeReportConst, Tools, Permissions, CaseManagement, Case, OperativeReportTemplateConst, BeforeUnload) {

			var vm = this;
			$controller('AbstractReportCrtl', {vm: vm});

			var loadReportPromise = null;

			vm.action = 'view';
			vm.isShowEdit = true;
			vm.source = null;
			vm.templateConst = OperativeReportTemplateConst;
			vm.currentDate = new Date();
			vm.allowedTypes = [];
			vm.toRemoveCustomFields = [];
			vm.availableFields = [];
			angular.forEach(OperativeReportTemplateConst.GROUPS, function(group_id, key) {
				if (key !== 'CASEINFO') {
					vm.allowedTypes.push(group_id);
				}
			});

			$scope.$on("$locationChangeSuccess", function () {
				var params = $location.search();
				vm.type = angular.isDefined(params.type) ? params.type : 'open';
				vm.signed = angular.isDefined(params.signed) ? params.signed : false;
			});

			vm.init = function(id, user_id, source, fromReportsList, caseVm) {
				var loadReportDef = $q.defer();
				loadReportPromise = loadReportDef.promise;

				$http.get('/cases/ajax/' + $scope.org_id + '/report/' + id).then(function (response) {
					vm.report = new OperativeReport(response.data.report);
					if (vm.report.case) {
						vm.report.case = new Case(vm.report.case);
					}
					vm.template = response.data.template;
					vm.site_template = response.data.site_template;

					angular.forEach(response.data.template, function (groupItems, group_id) {
						angular.forEach(groupItems, function (defaultItem) {
							var keepGoing = true;
							angular.forEach(vm.template[group_id], function (templateItem) {
								templateItem.confirmed_active = templateItem.active;
								if(keepGoing) {
									if(templateItem.name === defaultItem.name && !templateItem.active) {
										vm.availableFields.push(defaultItem);
										keepGoing = false;
									}
								}
							});
						});
					});

					vm.divideCaseInfoToColumn();
					vm.future_templates = [];
					angular.forEach(response.data.future_reports, function(template) {
						if (!angular.isArray(template)) {
							vm.future_templates.push(new OperativeReportFutureTemplate(template));
						}
					});
					vm.isShowEdit = Permissions.hasAccess('operative_reports', 'edit', vm.report);
					loadReportDef.resolve(vm.report);
				});
	
				if(user_id) {
					vm.user_id = user_id;
				}

				vm.source = source;
				if (fromReportsList) {
					vm.trySwitchToEditMode(caseVm);
				}
			};

			vm.changeTab = function(tabName, caseVm) {
				if (tabName == 'report') {
					vm.trySwitchToEditMode(caseVm);
				}
			};

			vm.trySwitchToEditMode = function(caseVm) {
				loadReportPromise.then(function(report){
					if (((report.status == OperativeReportConst.STATUSES.open)
						|| (report.status == OperativeReportConst.STATUSES.draft)
						|| (report.status == OperativeReportConst.STATUSES.signed)) && vm.isShowEdit) {
						vm.edit(caseVm);
					}
				});
			};

			vm.edit = function(caseVm) {
				vm.isFutureReportLoaded = false;
				caseVm.edit();
				vm.original_report = angular.copy(vm.report);
				vm.master_template = angular.copy(vm.template);
				vm.masterAvailableFields = angular.copy(vm.availableFields);
				vm.future_template_picker_master = angular.copy(vm.report.applied_template);
				if(vm.report.applied_template && vm.report.applied_template.id) {
					vm.future_template_picker = vm.report.applied_template;
				}
				angular.forEach(vm.template, function (groupItems, group_id) {
					angular.forEach(vm.template[group_id], function (templateItem) {
						templateItem.confirmed_active = templateItem.active;
					});
				});
				vm.divideCaseInfoToColumn();
				vm.action = 'edit';
				BeforeUnload.addForms(vm.original_report , vm.report, 'report');
			};

			vm.selectFutureTemplate = function(caseItem) {
				vm.isFutureReportLoaded = false;
				if (vm.report.status == OperativeReportConst.STATUSES.draft) {
					if (vm.future_template_picker && vm.future_template_picker_master != vm.future_template_picker) {
						$scope.dialog(View.get('cases/report/warn-override-report.html'), $scope, {windowClass: 'alert'}).result.then(function () {
							vm.future_template_picker_master = angular.copy(vm.future_template_picker);
							copyTemplate(vm.future_template_picker, caseItem);
						}, function() {
							vm.future_template_picker = vm.future_template_picker_master ? vm.future_template_picker_master : null;
						});
					}
				} else {
					copyTemplate(vm.future_template_picker, caseItem);
				}
			};

			vm.editMyReport = function(caseVm) {
				if(vm.type === 'submitted') {
					$scope.dialog(View.get('cases/report/reopen_submitted.html'), $scope, {windowClass: 'alert'}).result.then(function () {
						vm.edit(caseVm);
					});
				} else {
					vm.edit(caseVm);
				}
			};

			vm.submitForm = function(case_ctrl) {
				vm.submit('submitted', function() {
					case_ctrl.save(function(){
						CaseManagement.complete('report');
					}).then(function(){
						var idx = $scope.cmVm.activeTab;
						if ($scope.cmVm.tabs[idx + 1]) {
							$scope.cmVm.activeTab = idx + 1;
						}
					});
				});
			};

			vm.submit = function(status, callback) {
				vm.save(false, status).then(function(){
					if(callback) {
						callback();
					}
				});
			};

			vm.save = function(isFuture, status) {
				vm.reindexCaseInfoColumns();
				var def = $q.defer();
				var report = vm.report;
				if (status) {
					report.status = OperativeReportConst.STATUSES[status];
				}
				var params = {};
				params.future = isFuture;
				params.source = vm.source;
				params.template = angular.copy(vm.template);
				vm.saveTemplate(vm.template);
				OpReports.save(report, params, function(result) {
					if (result.data.id) {
						vm.action = 'view';
						vm.init(report.id);
						def.resolve();
					} else if (result.data.errors) {
						vm.errors = result.data.errors.split(';');
						def.reject();
					}
				});
				return def.promise;
			};

			vm.chooseReport = function (report) {
				vm.report.template_name = report.name;
			};

			vm.cancel = function () {
				vm.future_template_picker = vm.future_template_picker_master;
				if (vm.original_report) {
					vm.report = vm.original_report;
					vm.form = false;
				}
				vm.action = 'view';
				vm.template = vm.master_template;
				vm.availableFields = vm.masterAvailableFields;
				vm.errors = [];
				BeforeUnload.clearForms('report');
				vm.divideCaseInfoToColumn();
			};

			vm.isDisabled = function(field_name) {
				if (angular.isUndefined(vm.template)) {
					return false;
				} else {
					return !vm.template[field_name];
				}
			};

			vm.getPrintUrl = function (id) {
				return '/cases/ajax/' + $scope.org_id + '/exportReport/' + id + '?to_download=false';
			};

			vm.saveWithCase = function(status, case_ctrl) {
				var saveCaseFunc = function() {
					return case_ctrl.save();
				};

				vm.submit(status, function() {
					saveCaseFunc().then(vm.doTheBack);
				});
			};

			vm.addAdditionalField = function() {
				vm.type_field = null;
				vm.fieldSection = null;
				vm.fieldsToAdd = [];
				vm.errors = [];
				vm.toRemoveCustomFields = [];
				vm.newCustomFieldName = '';
				vm.master_template_additional = angular.copy(vm.template);
				vm.modalAdditionalField = $scope.dialog(View.get('operative-report/add_fields.html'), $scope, {windowClass: 'operative-reports--add-fields', size: 'md'});
				vm.modalAdditionalField.result.then(function () {
					if(vm.type_field == vm.templateConst.TYPE_FIELDS.LIST) {
						var list_value = {count_columns: vm.newListItem.list_value.count_columns};
						var items = [
							vm.getMasterListItem(),
							vm.getMasterListItem(),
							vm.getMasterListItem()
						];
						list_value.column1 = items;
						if(vm.newListItem.list_value.count_columns > 1) {
							list_value.column2 = angular.copy(items);
						}
						vm.newListItem.list_value = list_value;
						vm.template[vm.fieldSection].push(vm.newListItem);
					} else if(vm.type_field == vm.templateConst.TYPE_FIELDS.TEXT_FIELD) {
						if(vm.newCustomFieldName) {
							var field = {
								id: null,
								future_template_id: vm.report.id,
								name: vm.newCustomFieldName,
								organization_id: $scope.org_id,
								field: 'custom',
								active: true,
								confirmed_active: true
							};
							vm.template[vm.fieldSection].push(field);
						} else {
							angular.forEach(vm.fieldsToAdd, function (itemField) {
								var index = vm.availableFields.indexOf(itemField);
								if (index > -1) {
									vm.availableFields.splice(index, 1);
								}
								itemField.active = true;
								itemField.confirmed_active = true;
								angular.forEach(vm.template[itemField.group_id], function (tempItem, index) {
									if(tempItem.name === itemField.name) {
										vm.template[itemField.group_id].splice(index, 1);
									}
								});
								vm.template[itemField.group_id].push(itemField);
							});
						}
					}
					vm.reindexCaseInfoColumns();
				}, function () {
					angular.forEach(vm.template, function (groupFields) {
						angular.forEach(groupFields, function (fieldTemplate) {
							if(fieldTemplate.field === 'custom'
								&& angular.isUndefined(fieldTemplate.report_id)
								&& angular.isUndefined(fieldTemplate.id)) {
									vm.toRemoveCustomFields.push(fieldTemplate);
							}
						});
					});
					vm.removeReportCustomFields();
					vm.template = vm.master_template_additional;
					vm.reindexCaseInfoColumns();

				});
			};

			vm.saveAdditionalFields = function () {

				vm.removeReportCustomFields();

				vm.saveTemplate(vm.template, function() {
					vm.modalAdditionalField.close('closing');
				}, function (result) {
					vm.errors =  result.data.errors.split(';');
				}, true);
			};

			vm.saveTemplate = function(data, callback, errorCallback, isOnlyValidate) {
				vm.reindexCaseInfoColumns();
				$http.post('/operative-reports/ajax/save/' + $scope.org_id + '/fieldsReport/' + vm.report.id, $.param({data: JSON.stringify(data), isOnlyValidate: isOnlyValidate})).then(function (result) {
					if (result.data.id) {
						vm.errors = [];
						if(callback) {
							callback(result);
						}
					} else if (result.data.errors) {
						if(errorCallback) {
							errorCallback(result);
						} else {
							vm.errors =  result.data.errors.split(';');
						}
					}
				});
			};

			vm.doTheBack = function() {
				var link = '/operative-reports/my/' + $scope.org_id;
				if(vm.user_id) {
					link += '/index/' + vm.user_id;
				}
				if((vm.report.status == OperativeReportConst.STATUSES.submitted) || (vm.report.status == OperativeReportConst.STATUSES.signed)) {
					link += '/' + '#?type=submitted';
				}
				$window.location = link;
			};

			vm.saveFinishLater = function(case_ctrl) {
				vm.submit('draft', function() {
					case_ctrl.save(function(){
						CaseManagement.uncomplete('report');
					}).then(function(){
						var idx = $scope.cmVm.activeTab;
						if ($scope.cmVm.tabs[idx + 1]) {
							$scope.cmVm.activeTab = idx + 1;
						}
					});
				});
			};

			vm.removeCustomField = function(field) {
				$scope.dialog(View.get('operative-report/confirm_delete.html'), $scope).result.then(function () {
					var idx = vm.template[field.group_id].indexOf(field);
					if(idx > -1) {
						var toRemove = vm.template[field.group_id].splice(idx, 1);
						if(angular.isDefined(toRemove[0].report_id)) {
							vm.toRemoveCustomFields.push(toRemove[0]);
						}
					}
				});
			};

			vm.removeReportCustomFields = function () {
				if(vm.toRemoveCustomFields.length) {
					$http.post('/operative-reports/ajax/' + $scope.org_id + '/removeReportCustomField/',
						$.param({data: JSON.stringify(vm.toRemoveCustomFields)})
					);
				}
			};

			vm.isShowCaseInfoField = function (fieldCode) {
				if(vm.template) {
					var newField = $filter('filter')(vm.template[OperativeReportTemplateConst.GROUPS.CASEINFO], {field: fieldCode});
					if(newField.length) {
						return newField[0].active;
					} else {
						return true;
					}
				}
			};


			vm.isSigned = function () {
				if (vm.report) {
					return vm.report.status == OperativeReportConst.STATUSES.signed;
				}
			};

			function copyTemplate(future_template, caseItem) {
				var copiedFromTemplate;
				if(future_template.id) {
					copiedFromTemplate = future_template.template;
				} else {
					copiedFromTemplate = vm.site_template;
					vm.report.applied_template = future_template;
				}
				angular.forEach(copiedFromTemplate, function (groupItems, group_id) {
					angular.forEach(copiedFromTemplate[group_id], function (templateItem) {
						templateItem.confirmed_active = templateItem.active;
					});
				});
				vm.template = copiedFromTemplate;
				OpReports.copyFromTemplate(vm.report, future_template, caseItem).then(function () {
					vm.divideCaseInfoToColumn();
				});
				vm.isFutureReportLoaded = true;

			}

		}]);

})(opakeApp, angular);

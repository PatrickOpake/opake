// Operative Report Future Template view/edit
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('OperativeReportSurgeonTemplateCrtl', [
		'$scope',
		'$http',
		'$window',
		'$sce',
		'$filter',
		'$controller',
		'View',
		'OperativeReportFutureTemplate',
		'OpReports',
		'OperativeReportTemplateConst',

		function ($scope, $http, $window, $sce, $filter, $controller, View, OperativeReportFutureTemplate, OpReports, OperativeReportTemplateConst) {
			var vm = this;

			$controller('AbstractReportCrtl', {vm: vm});

			vm.plug_text = 'This field is case specific and cannot be filled out in templates';
			vm.action = 'view';
			vm.templateConst = OperativeReportTemplateConst;
			vm.toRemoveCustomFields = [];
			vm.fieldsToClearValue = [];
			vm.customFieldsToClearValue = [];
			vm.availableFields = [];
			vm.allowedTypes = [];
			angular.forEach(OperativeReportTemplateConst.GROUPS, function(group_id, key) {
				if (key !== 'CASEINFO') {
					vm.allowedTypes.push(group_id);
				}
			});

			vm.init = function(id, user_id) {
				vm.availableFields = [];
				if(user_id) {
					vm.user_id = user_id;
				}
				$http.get('/operative-reports/ajax/' + $scope.org_id + '/future/' + id).then(function (response) {
					vm.report = new OperativeReportFutureTemplate(response.data.future_report);
					vm.template = response.data.template;
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
				});
			};

			vm.isEditMode = function () {
				return vm.action === 'edit';
			};

			vm.edit = function() {
				vm.master_report = angular.copy(vm.report);
				vm.master_template = angular.copy(vm.template);
				angular.forEach(vm.template, function (groupItems, group_id) {
					angular.forEach(vm.template[group_id], function (templateItem) {
						templateItem.confirmed_active = templateItem.active;
					});
				});
				vm.divideCaseInfoToColumn();
				vm.action = 'edit';
			};

			vm.save = function() {
				vm.reindexCaseInfoColumns();
				vm.saveTemplate(function () {
					OpReports.saveFutureTemplate(vm.report, function (result) {
						if (result.data.id) {
							vm.errors = [];
							vm.action = 'view';
							vm.init(vm.report.id);
						} else if (result.data.errors) {
							vm.errors = result.data.errors.split(';');
						}
					});
				});
			};

			vm.cancel = function () {
				vm.report = vm.master_report;
				vm.template = vm.master_template;
				vm.action = 'view';
				vm.errors = [];
				vm.divideCaseInfoToColumn();
			};

			vm.doTheBack = function() {
				var link = '/operative-reports/' + $scope.org_id;
				if(vm.user_id) {
					link += '/index/' + vm.user_id;
				}
				$window.location = link;
			};

			vm.addAdditionalField = function() {
				vm.type_field = null;
				vm.fieldSection = null;
				vm.fieldsToAdd = [];
				vm.errors = [];
				vm.toRemoveCustomFields = [];
				vm.fieldsToClearValue = [];
				vm.customFieldsToClearValue = [];
				vm.newCustomFieldName = '';
				vm.master_template = angular.copy(vm.template);
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
					vm.removeFutureCustomFields();
					vm.template = vm.master_template;
					vm.reindexCaseInfoColumns();
				});
			};

			vm.saveAdditionalFields = function () {
				vm.clearValues();
				vm.removeFutureCustomFields();
				vm.saveTemplate(function () {
					vm.modalAdditionalField.close('closing');
				}, true);
			};

			vm.clearValues = function () {
				angular.forEach(vm.fieldsToClearValue, function (field) {
					vm.report[field] = '';
				});
				angular.forEach(vm.customFieldsToClearValue, function (field) {
					field.custom_value = '';
				});
			};

			vm.saveTemplate = function (callback, isOnlyValidate) {
				$http.post('/operative-reports/ajax/save/' + $scope.org_id + '/futureFieldsReport/' + vm.report.id,
					$.param({data: JSON.stringify(vm.template), isOnlyValidate: isOnlyValidate})).then(function (result) {
					if (result.data.id) {
						vm.errors = [];
						if(callback) {
							callback();
						}
					} else if (result.data.errors) {
						vm.errors =  result.data.errors.split(';');
					}
				});
			};

			vm.isUnDefined = function(field) {
				return field === 'operation_time' || !angular.isDefined(vm.report[field]);
			};

			vm.removeCustomField = function(field) {
				$scope.dialog(View.get('operative-report/confirm_delete.html'), $scope).result.then(function () {
					var idx = vm.template[field.group_id].indexOf(field);
					if(idx > -1) {
						var toRemove = vm.template[field.group_id].splice(idx, 1);
						if(angular.isDefined(toRemove[0].future_template_id)) {
							vm.toRemoveCustomFields.push(toRemove[0]);
						}
					}
				});
			};

			vm.removeFutureCustomFields = function () {
				if(vm.toRemoveCustomFields.length) {
					$http.post('/operative-reports/ajax/' + $scope.org_id + '/removeFutureCustomField/',
						$.param({data: JSON.stringify(vm.toRemoveCustomFields)})
					);
				}
			};

			vm.clearValue = function (item) {
				var clearArray = vm.fieldsToClearValue;
				var key = item.field;
				if(item.field === 'custom') {
					clearArray = vm.customFieldsToClearValue;
					key = item;
				}
				var idx = clearArray.indexOf(key);
				if(item.active) {
					clearArray.push(key);
				} else if(idx > -1) {
					clearArray.splice(idx, 1);
				}

			};

		}]);

})(opakeApp, angular);

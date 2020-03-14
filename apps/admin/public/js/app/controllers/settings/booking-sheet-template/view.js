(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BookingSheetTemplateViewCtrl', [
		'$scope',
		'$http',
		'$q',
		'$window',
		'View',
		function ($scope, $http, $q, $window, View) {

			var FORM_PATIENT_INFO = 1;
			var FORM_CASE_INFO = 2;

			var vm = this;
			vm.isConfigLoaded = false;
			vm.isButtonsDisabled = false;
			vm.template = {};
			vm.templateId = null;
			vm.errors = null;
			vm.isPreview = false;
			vm.fieldsForPreview = null;

			var defaultFields = null;

			vm.init = function(id) {
				if (id) {
					vm.templateId = id;
					$http.get('/settings/booking-sheet-templates/ajax/' + $scope.org_id + '/getDefaultFields').then(function(result) {
						defaultFields = result.data;
					});
					$http.get('/settings/booking-sheet-templates/ajax/' + $scope.org_id + '/getTemplate/' + id).then(function(result) {
						vm.template = prepareFieldsConfig({
							name: result.data.name,
							fields: result.data.fields
						});

						vm.isConfigLoaded = true;
					});
				} else {
					$http.get('/settings/booking-sheet-templates/ajax/' + $scope.org_id + '/getDefaultFields').then(function(result) {
						defaultFields = result.data;
						vm.template = prepareFieldsConfig({
							name: '',
							fields: result.data
						});

						vm.isConfigLoaded = true;
					});
				}
			};

			vm.preview = function() {
				syncTemplateFields();
				vm.fieldsForPreview = exportFieldsForPreview();
				vm.isPreview = true;
			};

			vm.getView = function() {
				return (vm.isPreview) ? View.get('/settings/booking-sheet-template/preview.html') :
					View.get('/settings/booking-sheet-template/form.html');
			};

			vm.backToForm = function() {
				vm.fieldsForPreview = null;
				vm.isPreview = false;
			};

			vm.save = function() {
				vm.isButtonsDisabled = true;
				var id = vm.templateId || '';
				$http.post(
					'/settings/booking-sheet-templates/ajax/' + $scope.org_id + '/saveTemplate/' + id,
					$.param({
							data: angular.toJson({
								name: vm.template.name,
								fields: exportFields()
							})
						}
					)
				).then(function(result) {
					if (result.data.success) {
						$window.location.href = '/settings/booking-sheet-templates/' + $scope.org_id;
					} else {
						vm.errors = result.data.errors;
						vm.isButtonsDisabled = false;
					}
				}, function() {
					vm.isButtonsDisabled = false;
				});
			};

			vm.cancel = function() {
				$window.location.href = '/settings/booking-sheet-templates/' + $scope.org_id;
			};

			function syncTemplateFields() {
				var exportedFields = exportFields();
				var allFields = angular.copy(defaultFields);
				angular.forEach(allFields, function(fieldData, fieldId) {
					if (exportedFields[fieldId]) {
						fieldData.x = exportedFields[fieldId].x;
						fieldData.y = exportedFields[fieldId].y;
						fieldData.active = exportedFields[fieldId].active;
					}
				});

				prepareFields(allFields, vm.template);
			}

			function exportFieldsForPreview() {

				var fields = exportFields();
				var fieldsForPreview = {};
				angular.forEach(fields, function(fieldData, fieldId) {
					if (fieldData.active && defaultFields[fieldId]) {
						var defaultFieldConfig = angular.copy(defaultFields[fieldId]);
						defaultFieldConfig.x = fieldData.x;
						defaultFieldConfig.y = fieldData.y;
						fieldsForPreview[fieldId] = defaultFieldConfig;
					}
				});

				return fieldsForPreview;
			}

			function exportFields() {

				var fields = {};
				if (vm.template.patientInfo.control.export) {
					angular.forEach(vm.template.patientInfo.control.export(), function(fieldData, fieldId) {
						fields[fieldId] = fieldData;
					});
				}
				if (vm.template.caseInfo.control.export) {
					angular.forEach(vm.template.caseInfo.control.export(), function(fieldData, fieldId) {
						fields[fieldId] = fieldData;
					});
				}

				return fields;
			}

			function prepareFieldsConfig(settings) {

				var template = {};
				template.name = settings.name;
				template.patientInfo = {};
				template.patientInfo.options = {
					name: 'patientInfo',
					label: 'Patient Information'
				};
				template.patientInfo.control = {};
				template.caseInfo = {};
				template.caseInfo.options = {
					name: 'caseInfo',
					label: 'Case Information'
				};
				template.caseInfo.control = {};

				prepareFields(settings.fields, template);

				return template;
			}

			function prepareFields(fields, template) {
				template.caseInfo.activeItems = [];
				template.caseInfo.inactiveItems = [];
				template.patientInfo.activeItems = [];
				template.patientInfo.inactiveItems = [];
				angular.forEach(fields, function(config, fieldId) {
					var targetForm = null;
					if (config.form == FORM_PATIENT_INFO) {
						targetForm = template.patientInfo;
					} else if (config.form == FORM_CASE_INFO) {
						targetForm = template.caseInfo;
					}

					if (targetForm) {
						config.field = fieldId;
						if (config.active) {
							targetForm.activeItems.push(config);
						} else {
							targetForm.inactiveItems.push(config);
						}
					}
				});
			}

		}]);

})(opakeApp, angular);

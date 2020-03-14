(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('bookingFormWidget', ['$rootScope', '$filter', '$templateCache', '$compile', '$timeout', 'config', 'View', 'Source', 'Permissions', 'PatientConst', 'CaseRegistrationConst',
		function ($rootScope, $filter, $templateCache, $compile, $timeout, config, View, Source, Permissions, PatientConst, CaseRegistrationConst) {
			return {
				restrict: "E",
				replace: true,
				scope: {
					template: '=',
					bookingVm: '='
				},
				controller: function ($scope) {

				},
				controllerAs: 'widgetVm',
				templateUrl: function () {
					return View.get('booking/form_widget.html');
				},
				link: function (scope, elem, attrs, ctrl) {

					scope.patientConst = PatientConst;
					scope.caseRegistrationConst = CaseRegistrationConst;
					scope.source = Source;
					scope.permissions = Permissions;
					scope.loggedUser = $rootScope.loggedUser;
					scope.isCompiled = false;

					angular.forEach(scope.template, function (item, key) {
						item.field = key;
					});

					var templateArray = $.map(scope.template, function(value, index) {
						return [value];
					});

					var patientDetailsFields = getFieldsForForm(templateArray, 1);
					var patientDetailsFieldsRows = [];

					var rowFields;

					for (var i = 0; i < 15; i++) {
						rowFields = getFieldsForRow(patientDetailsFields, i);
						if (rowFields.length) {

							var previousFieldsWidthsSum = 0;
							angular.forEach(rowFields, function (field) {
								field.widthClass = 'col-sm-' + field.width;
								field.offsetClass = 'x-offset-' + (field.x - previousFieldsWidthsSum);
								field.widthAndOffsetClassesStr = field.widthClass + ' ' + field.offsetClass;
								previousFieldsWidthsSum += field.width;
							});
						}

						patientDetailsFieldsRows[i] = {row: i, fields: rowFields};
					}
					patientDetailsFieldsRows = removeLastEmptyRows(patientDetailsFieldsRows);
					ctrl.patientDetailsFieldsRows = patientDetailsFieldsRows;

					var caseDetailsFields = getFieldsForForm(templateArray, 2);
					var caseDetailsFieldsRows = [];

					for (i = 0; i < 15; i++) {
						rowFields = getFieldsForRow(caseDetailsFields, i);
						if (rowFields.length) {

							previousFieldsWidthsSum = 0;
							angular.forEach(rowFields, function (field) {
								field.widthClass = 'col-sm-' + field.width;
								field.offsetClass = 'x-offset-' + (field.x - previousFieldsWidthsSum);
								field.widthAndOffsetClassesStr = field.widthClass + ' ' + field.offsetClass;
								previousFieldsWidthsSum += field.width;
							});
						}

						caseDetailsFieldsRows[i] = {row: i, fields: rowFields};
					}
					caseDetailsFieldsRows = removeLastEmptyRows(caseDetailsFieldsRows);
					ctrl.caseDetailsFieldsRows = caseDetailsFieldsRows;

					var widgetBodyElem = $($templateCache.get('booking-form-widget/main'));
					renderFieldsTemplate(patientDetailsFieldsRows, widgetBodyElem.find('.widget-patient-info-form'));
					renderFieldsTemplate(caseDetailsFieldsRows, widgetBodyElem.find('.widget-case-info-form'));
					elem.append(widgetBodyElem);
					$compile(widgetBodyElem)(scope);
					$timeout(function() {
						scope.isCompiled = true;
						scope.$emit('BookingFormWidget.compiled');
					});


					function renderFieldsTemplate(rows, formElem) {
						angular.forEach(rows, function(row) {
							var rowElem = $('<div>').addClass('row').appendTo(formElem);
							if (row.fields && row.fields.length) {
								angular.forEach(row.fields, function(field) {
									var fieldRootElem = $('<div>').addClass('field').addClass(field.widthAndOffsetClassesStr);
									var fieldTemplate = $templateCache.get('booking-form-widget/field/' + field.field);
									fieldRootElem.append(fieldTemplate);
									rowElem.append(fieldRootElem);
								});
							} else {
								$('<div>').addClass('empty-row').appendTo(rowElem);
							}
						});
					}

					function getFieldsForForm(fields, formId) {
						var result = [];
						angular.forEach(fields, function(field) {
							if (field.form == formId) {
								result.push(field);
							}
						});

						return $filter('orderBy')(result, 'y');
					}

					function getFieldsForRow(fields, rowIndex) {
						var result = [];
						angular.forEach(fields, function(field) {
							if (field.y == rowIndex) {
								result.push(field);
							}
						});

						return $filter('orderBy')(result, 'x');
					}

					function removeLastEmptyRows(rows) {
						var lastValueIndex = null;
						for (var i = 0, count = rows.length; i < count; ++i) {
							if (rows[i].fields.length) {
								lastValueIndex = i;
							}
						}

						if (lastValueIndex !== null) {
							return rows.slice(0, lastValueIndex + 1);
						}

						return rows;
					}
				}
			};
		}]);

})(opakeApp, angular);

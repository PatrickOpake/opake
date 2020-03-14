(function (opakeApp, angular) {
	'use strict';

	var FIELD_MIN_WIDTH = 54;
	var FIELD_MAX_WIDTH = 600;
	var FIELD_MIN_HEIGHT = 32;
	var FIELD_MAX_HEIGHT = 350;

	var ALLOW_DROP_KEY = 'allowDrop';

	opakeApp.directive('uploadedFormDynamicFields', ['$filter', '$http', '$q', function ($filter, $http, $q) {
			return {
				restrict: "AC",
				bindToController: {
					currentPage: '=',
					pagePreview: '=',
					options: '=',
					docFields: '='
				},
				controller: function ($scope) {
					var vm = this;

					// VM Objects
					vm.pageFields = [];

					// VM Actions
					vm.addField = addField;
					vm.updateDocField = updateDocField;
					vm.removeField = removeField;


					function getTitle(key) {
						var option = $filter('filter')(vm.options, {key: key})[0];
						if (option) {
							return option.title;
						}
					}

					function addField(key, x, y) {
						var docField = {
							page: vm.currentPage,
							key: key
						};
						var field = {
							doc_field: docField,
							title: getTitle(key),
							x: x,
							y: y,
							width: 170,
							height: 34
						};
						updateDocField(field);

						vm.pageFields.push(field);
						vm.docFields.push(docField);
					}

					function updateDocField(field) {
						var img = vm.pagePreview,
							docField = field.doc_field;

						docField.x = (field.x / img.width) * 100;
						docField.y = (field.y / img.height) * 100;
						docField.width = (field.width / img.width) * 100;
						docField.height = (field.height / img.height) * 100;
					}

					function removeField(field) {
						var idx = vm.pageFields.indexOf(field);
						if (idx > -1) {
							vm.pageFields.splice(idx, 1);
						}
						var idx = vm.docFields.indexOf(field.doc_field);
						if (idx > -1) {
							vm.docFields.splice(idx, 1);
						}
					}

					$scope.$watch('optionsVm.pagePreview', function (img) {
						vm.pageFields = [];
						if (img) {
							// Initialize fields
							angular.forEach($filter('filter')(vm.docFields, {page: vm.currentPage}), function (docField) {
								vm.pageFields.push({
									doc_field: docField,
									title: docField.title || getTitle(docField.key),
									x: parseInt((docField.x / 100) * img.width),
									y: parseInt((docField.y / 100) * img.height),
									width: parseInt((docField.width / 100) * img.width),
									height: parseInt((docField.height / 100) * img.height)
								});
							});
						}
					});
				},
				controllerAs: 'optionsVm',
				link: function (scope, elem, attrs, ctrl) {
					var area = elem.find('.document-area')[0],
						optionsList = elem.find('.options-list'),
						activeOption,
						activeField,
						elOffsetX, elOffsetY;

					optionsList.on('dragstart', '.options-list--option', function (event) {
						var e = event.originalEvent;
						e.dataTransfer.setData('text', ALLOW_DROP_KEY);
						activeOption = e.target.getAttribute('key');
						elOffsetX = e.offsetX;
						elOffsetY = e.offsetY;
					});

					scope.dragstart = function (e, field) {
						angular.element(e.target).trigger('mouseleave');
						e.dataTransfer.setData('text', ALLOW_DROP_KEY);
						activeField = field;
						elOffsetX = e.offsetX;
						elOffsetY = e.offsetY;
					};

					scope.remove = function (e, field) {
						scope.$apply(function () {
							ctrl.removeField(field);
						});
						e.preventDefault();
					};

					area.ondragover = function (e) {
						e.preventDefault();
					};
					area.ondrop = function (e) {
						if (e.dataTransfer.getData('text') === ALLOW_DROP_KEY) {
							var offsetX = e.offsetX,
								offsetY = e.offsetY;
							if (e.target !== area) {
								offsetX += e.target.parentElement.offsetLeft;
								offsetY += e.target.parentElement.offsetTop;
							}
							scope.$apply(function () {
								if (activeField) {
									activeField.x = offsetX - elOffsetX;
									activeField.y = offsetY - elOffsetY;
									ctrl.updateDocField(activeField);
									activeField = null;
								} else {
									var x = offsetX - elOffsetX,
										y = offsetY - elOffsetY;
									ctrl.addField(activeOption, x, y);
									activeOption = null;
								}
							});
						}
						e.preventDefault();
					};
				}
			};
		}]);

	opakeApp.directive('dynamicFieldResize', [function () {
			return {
				require: '^uploadedFormDynamicFields',
				restrict: "AC",
				scope: {field: '='},
				link: function (scope, elem, attrs, ctrl) {
					var elemDom = elem[0],
						field = scope.field,
						startX, startY,
						startWidth, startHeight;

					elemDom.addEventListener('mousedown', function (e) {
						startX = e.clientX;
						startY = e.clientY;
						startWidth = field.width;
						startHeight = field.height;
						document.documentElement.addEventListener('mousemove', doDrag, false);
						document.documentElement.addEventListener('mouseup', stopDrag, false);
						e.preventDefault();
					}, false);

					function doDrag(e) {
						scope.$apply(function () {
							var width = startWidth + e.clientX - startX,
								height = startHeight + e.clientY - startY;
							if (width >= FIELD_MIN_WIDTH && width <= FIELD_MAX_WIDTH) {
								field.width = width;
							}
							if (height >= FIELD_MIN_HEIGHT && height <= FIELD_MAX_HEIGHT) {
								field.height = height;
							}
							ctrl.updateDocField(field);
						});
					}

					function stopDrag(e) {
						document.documentElement.removeEventListener('mousemove', doDrag, false);
						document.documentElement.removeEventListener('mouseup', stopDrag, false);
					}
				}
			};
		}]);

})(opakeApp, angular);

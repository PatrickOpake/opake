(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('formFieldsGrid', [function () {
		return {
			restrict: "E",
			scope: {
				activeItems: '=',
				inactiveItems: '=',
				options: '=',
				control: '='
			},
			link: function (scope, element, attrs) {

				var instanceClass = 'form-fields-grid-instance-' + scope.options.name;

				var root = $('<div>').addClass('form-fields-grid ' + instanceClass);
				var title = $('<div>').addClass('grid-title').text(scope.options.label).appendTo(root);
				var gridContainer = $('<div>').addClass('grid-container').appendTo(root);

				var leftZone = $('<div>').addClass('grid-left-zone grid-zone').appendTo(gridContainer);
				var rightZone = $('<div>').addClass('grid-right-zone grid-zone').appendTo(gridContainer);

				var inactiveGridStack = $('<div>').addClass('grid-stack inactive-stack');
				var activeGridStack = $('<div>').addClass('grid-stack active-stack');

				rightZone.append(
					$('<div>').addClass('grid-remove-area').append(
						$('<div>').addClass('title').text('Drag a field to area below to remove'),
						$('<hr>'),
						inactiveGridStack
					)
				);

				leftZone.append(activeGridStack);

				angular.forEach(scope.activeItems, function(item) {
					activeGridStack.append(createGridElement(item))
				});

				angular.forEach(scope.inactiveItems, function(item) {
					inactiveGridStack.append(createGridElement(item));
				});

				element.append(root);

				var gridOptions = scope.options.gridOptions || {};
				var options = angular.extend({
					acceptWidgets: false,
					cellHeight: 40,
					disableResize: true,
					height: 15,
					minWidth: 768,
					verticalMargin: 10,
					width: 12,
					float: true
				}, gridOptions);
				var activeGridStackOptions = angular.copy(options);
				var inactiveGridStackOptions = angular.extend(options, {
					cellHeight: 25,
					verticalMargin: 5,
					float: false
				});

				activeGridStack.gridstack(activeGridStackOptions);
				inactiveGridStack.gridstack(inactiveGridStackOptions);

				var activePlugin = activeGridStack.data('gridstack');
				var inactivePlugin = inactiveGridStack.data('gridstack');

				inactiveGridStack.on('dragstop', function(event, ui) {
					onDragToField(event, false);
				});

				activeGridStack.on('dragstop', function(event, ui) {
					onDragToField(event, true);
				});

				var tooltipConfig = {
					items: '.grid-stack-item-content',
					close: function () {
						$(".ui-helper-hidden-accessible > *:not(:last)").remove();
					},
					tooltipClass: 'grid-stack-tooltip',
					position: {
						my: "center top+5",
						at: "center bottom",
						collision: "flipfit"
					},
					content: function() {
						var element = $(this);
						var diff = element[0].scrollWidth - element.innerWidth();
						if (diff > 1) {
							var text = element.text();
							if (element.hasClass('required')) {
								text += ' (Required Field)';
							}

							return text;
						}

						if (element.hasClass('required')) {
							return 'Required Field'
						}

						return null;
					}
				};

				inactiveGridStack.tooltip(tooltipConfig);
				activeGridStack.tooltip(tooltipConfig);

				inactiveGridStack.on('dragstart', function() {
					inactiveGridStack.tooltip('disable');
					activeGridStack.tooltip('disable');
				});
				inactiveGridStack.on('dragstop', function() {
					inactiveGridStack.tooltip('enable');
					activeGridStack.tooltip('enable');
				});
				activeGridStack.on('dragstart', function() {
					inactiveGridStack.tooltip('disable');
					activeGridStack.tooltip('disable');
				});
				activeGridStack.on('dragstop', function() {
					inactiveGridStack.tooltip('enable');
					activeGridStack.tooltip('enable');
				});

				if (angular.isObject(scope.control)) {
					scope.control.export = function() {
						var fields = {};
						var extractFieldData = function(active) {
							return function() {
								var el = $(this);
								var node = el.data('_gridstack_node');
								var fieldId = el.attr('data-field-id');
								fields[fieldId] = {
									x: node.x,
									y: node.y,
									active: active
								};
							};
						};
						activeGridStack.find('.grid-stack-item:visible').each(extractFieldData(true));
						inactiveGridStack.find('.grid-stack-item:visible').each(extractFieldData(false));

						return fields;
					};
				}

				function onDragToField(event, active) {

					var el = $(event.target);

					var oppositeZone, oppositePlugin, plugin;
					if (active) {
						oppositeZone = inactiveGridStack;
						oppositePlugin = inactivePlugin;
						plugin = activePlugin;
					} else {
						oppositeZone = activeGridStack;
						oppositePlugin = activePlugin;
						plugin = inactivePlugin;
					}

					if (active && el.find('.grid-stack-item-content').hasClass('required')) {
						return;
					}

					var oppositeZoneCoords = getDropZoneCoords(oppositeZone);
					var dropTop = event.pageY;
					var dropLeft = event.pageX;

					if (dropLeft >= oppositeZoneCoords[0] && dropTop >= oppositeZoneCoords[1] &&
						dropLeft <= oppositeZoneCoords[2] && dropTop <= oppositeZoneCoords[3]) {
						var newEl = copyElemWithoutPosition(el);
						var width = parseInt(el.attr('data-gs-width'));
						var height = parseInt(el.attr('data-gs-height'));
						oppositePlugin.addWidget(newEl, 0, 0, width, height, true);
						plugin.removeWidget(el);
					}
				}

				function copyElemWithoutPosition(elem) {

					var newElem = $('<div>').addClass('grid-stack-item').attr({
						'data-field-id': elem.attr('data-field-id')
					});

					newElem.append(
						$('<div>').addClass('grid-stack-item-content').text(elem.find('.grid-stack-item-content').text())
					);

					return newElem;

				}

				function getDropZoneCoords(el) {
					var offset = el.offset();
					var outerHeight = el.outerHeight();
					var outerWidth = el.outerWidth();

					return [
						offset.left,
						offset.top,
						offset.left + outerWidth,
						offset.top + outerHeight
					];
				}

				function createGridElement(item) {
					var elem = $('<div>').addClass('grid-stack-item').attr({
						'data-field-id': item.field,
						'data-gs-x': item.x,
						'data-gs-y': item.y,
						'data-gs-width': item.width,
						'data-gs-height': item.height
					});
					var content = $('<div>').addClass('grid-stack-item-content').text(item.title);
					if (item.required) {
						content.addClass('required');
					}
					elem.append(content);
					return elem;
				}
			}
		};
	}]);

}(opakeApp, angular));
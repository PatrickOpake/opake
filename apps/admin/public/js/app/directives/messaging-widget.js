(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('messagingWidget', ['$rootScope', '$filter', 'config', 'View', 'Messaging', function ($rootScope, $filter, config, View, Messaging) {
			return {
				restrict: "E",
				replace: true,
				scope: {},
				controller: function ($scope) {
					$scope.messaging = Messaging;
					$scope.loggedUser = $rootScope.loggedUser;

					var vm = this;
					var state = Messaging.getViewState(),
						windows = [],
						controlWindow = {
							key: 'control',
							type: 'control'
						},
						maxOpenWindows = config.messaging.max_open_windows || 2,
						openQueue = state.open_queue || [];

					windows.push(controlWindow);

					// VM Objects
					vm.controlWindow = controlWindow;
					vm.windows = windows;

					// VM Actions
					vm.openDialogWindow = openDialogWindow;
					vm.closeDialogWindow = closeDialogWindow;
					vm.expandWindow = expandWindow;
					vm.collapseWindow = collapseWindow;
					vm.toggleWindow = function (window) {
						window.open ?
							collapseWindow(window) :
							expandWindow(window);
					};

					// Initialization
					if (angular.isDefined(state.windows)) {
						var windowsState = angular.copy(state.windows);
						if (windowsState.control) {
							controlWindow.open = windowsState.control.open;
							delete windowsState.control;
						}
						angular.forEach(windowsState, function (windowState, key) {
							var user = Messaging.getUser(key);
							if (user) {
								openDialogWindow(user, windowState.open);
							}
						});
					} else {
						expandWindow(controlWindow);
					}

					$scope.$watch('widgetVm.windows', function (newVal, oldVal) {
						if (!angular.equals(newVal, oldVal)) {
							var windows = {};
							angular.forEach(newVal, function (window) {
								windows[window.key] = {open: window.open};
							});
							Messaging.updateViewState({
								windows: windows,
								open_queue: openQueue
							});
						}
					}, true);

					$rootScope.$on('OpenDialogWindowForUser', function(e, user) {
						openDialogWindow(user, true, true);
					});

					// Functions
					function getWindow(key) {
						return $filter('filter')(windows, {key: key})[0];
					}

					function openDialogWindow(user, expand, needUpdateWindows) {
						var dialog = $filter('filter')(windows, {type: 'dialog', user: user})[0];
						if (!dialog) {
							dialog = {
								key: user.id,
								type: 'dialog',
								user: user
							};
							windows.push(dialog);
						}
						if (expand) {
							expandWindow(dialog);
						} else {
							dialog.open = false;
						}

						if (needUpdateWindows) {
							updateWindows(windows);
						}
					}

					function updateWindows(newWindows) {
						var windows = {};
						angular.forEach(newWindows, function (window) {
							windows[window.key] = {open: window.open};
						});

						Messaging.updateViewState({
							windows: windows,
							open_queue: openQueue
						});
					}

					function closeDialogWindow(dialog) {
						collapseWindow(dialog);
						var idx = windows.indexOf(dialog);
						if (idx > 0) {
							windows.splice(idx, 1);
						}
					}

					function expandWindow(window) {
						window.open = true;

						var idx = openQueue.indexOf(window.key);
						if (idx === -1) {
							openQueue.push(window.key);
							if (openQueue.length > maxOpenWindows) {
								var windowToClose = getWindow(openQueue[0]);
								if (windowToClose) {
									collapseWindow(windowToClose);
								} else {
									openQueue.shift();
								}
							}
						}
					}

					function collapseWindow(window) {
						window.open = false;

						var idx = openQueue.indexOf(window.key);
						if (idx > -1) {
							openQueue.splice(idx, 1);
						}
					}
				},
				controllerAs: 'widgetVm',
				templateUrl: function () {
					return View.get('widgets/messaging.html');
				}
			};
		}]);

	opakeApp.directive('messagingWindowHead', [function () {
			return {
				require: '^messagingWidget',
				restrict: "C",
				link: function (scope, elem) {
					elem.find('i').on('mouseenter', function (e) {
						elem.trigger('mouseleave');
					}).on('mouseleave', function (e) {
						elem.trigger('mouseenter');
					});
				}
			};
		}]);

	opakeApp.directive('messagingControlWindow', [function () {
			return {
				require: '^messagingWidget',
				restrict: "EA",
				replace: true,
				bindToController: {
					window: "="
				},
				controller: function () {
					var vm = this;

					// VM Actions
				},
				controllerAs: 'controlVm',
				templateUrl: 'messaging/control-window.html'
			};
		}]);

	opakeApp.directive('messagingDialogWindow', [function () {
			return {
				require: '^messagingWidget',
				restrict: "EA",
				replace: true,
				controllerAs: 'windowVm',
				templateUrl: 'messaging/dialog-window.html'
			};
		}]);

	opakeApp.directive('messagingDialog', ['$compile', '$q', 'Messaging', function ($compile, $q, Messaging) {
			return {
				restrict: "E",
				replace: true,
				bindToController: {
					user: "="
				},
				controller: function ($scope) {
					var vm = this,
						editMessage = null;

					Messaging.getMessages(vm.user.id).then(function(result){
						vm.messages = result;
					});

					vm.save = function () {
						if (vm.input_text) {
							var text = vm.input_text;
							vm.input_text = '';

							if (editMessage) {
								var message = angular.copy(editMessage);
								message.text = text;
								Messaging.editMessage(message).then(function(){
									editMessage.text = text;
									editMessage = null;
								});
							} else {
								var message = {};
								message.text = text;
								message.recipient_id = vm.user.id;
								Messaging.addMessage(message);
							}
						}
					};

					vm.edit = function (message) {
						vm.input_text = message.text;
						editMessage = message;
					};

					vm.editCancel = function () {
						if (editMessage) {
							vm.input_text = '';
							editMessage = null;
						}
					};

					vm.remove = function (message) {
						var removeDeferred = $q.defer();
						removeDeferred.promise.finally(function () {
							vm.showRemoveConfirm = false;
						}).then(function () {
							Messaging.removeMessage(vm.user.id, message);
						});
						vm.removeDeferred = removeDeferred;
						vm.showRemoveConfirm = true;
					};

					$scope.$on('$destroy', function () {
						Messaging.clearHistory(vm.user.id);
					});
				},
				controllerAs: 'dialogVm',
				templateUrl: 'messaging/dialog.html',
				link: function (scope, elem) {
					var ttip = angular.element(
						'<div class="messaging-dialog--tooltip">' +
							'<div class="messaging-dialog--tooltip-action" ng-click="dialogVm.remove(message)"><i class="icon-trash-16x16"></i></div>' +
							'<div class="messaging-dialog--tooltip-action" ng-click="dialogVm.edit(message)"><i class="icon-edit-16x16"></i></div>' +
						'</div>');

					elem.on('mouseenter', '.messaging-dialog--message.my', function () {
						$(this).append(ttip);
						$compile(ttip)($(this).scope());
					}).on('mouseleave', '.messaging-dialog--message.my', function () {
						ttip.remove();
					});
				}
			};
		}]);

	opakeApp.filter('messagingDate', function ($filter) {
			return function (date) {
				if (moment(date).isSame((new Date()), "day")) {
					return $filter('date')(date, 'h:mm a');
				}
				return $filter('date')(date, 'MMM d');
			};
		});
})(opakeApp, angular);

(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('reminderIcon', ['$compile', '$timeout', function ($compile, $timeout) {
			return {
				restrict: "E",
				require: 'ngModel',
				replace: true,
				transclude: true,
				scope: {
					ngModel: "=",
					note:'='
				},
				bindToController: {
					noteVm: "=ctrl"
				},
				controller: function ($scope, $filter, ReminderWidgetService) {
					var vm = this;
					vm.remind = function () {
						if (vm.noteVm) {
							$timeout(function () {
								$scope.$eval(vm.noteVm.changeReminderDate($scope.ngModel, $scope.note));
								ReminderWidgetService.init();
							});
						}
					};

					vm.getTooltipText = function (note) {
						if(note.reminder && note.reminder.id) {
							return 'Set for ' + $filter('date')(note.reminder.reminder_date, 'M/d/yy')
						} else {
							return 'Set Reminder';
						}
					};

					vm.unremind = function () {
						if (vm.noteVm) {
							$timeout(function () {
								$scope.$eval(vm.noteVm.unremind($scope.note));
								ReminderWidgetService.init();
							});
						}
					};

				},
				controllerAs: 'noteVm',
				template: '<a href="" class="icon"><i class="icon-note-bell-{{note.isSetReminder() ? \'filled\' : \'outline\'}}" uib-tooltip="{{noteVm.getTooltipText(note)}}" ng-model="ngModel" type="button" \n\
							uib-datepicker-popup is-open="open"\n\
							datepicker-append-to-body="true" datepicker-options="{showWeeks: false}"\n\
							tooltip-class="blue" tooltip-append-to-body="true"\n\
							ng-change="noteVm.remind()"></i></a>',
				link: function (scope, elem, attrs, ctrl) {

					scope.open = false;

					$timeout(function () {
						var icon = elem.find('i');

						icon.click(function (e) {
							if(scope.note.reminder && scope.note.reminder.id) {
								scope.noteVm.unremind();
							} else {
								scope.$apply(function () {
									scope.open = !scope.open;
									scope.note.calendar_is_open = scope.open;
								});
							}

							e.preventDefault();
						});
					});

				}
			};
		}]);

})(opakeApp, angular);

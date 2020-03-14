(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('reminderWidget', ['$rootScope', '$http', 'View', 'ReminderWidgetService', function ($rootScope, $http, View, ReminderWidgetService) {
		return {
			restrict: "E",
			replace: true,
			scope: {},
			controller: ['$scope', function ($scope) {

				$scope.view = View;

				var vm = this;
				vm.isInitialized = false;
				vm.isShowWidget = false;

				ReminderWidgetService.assign(vm);


				vm.init = function() {

				};

				vm.toggleShowWidget = function() {
					if (!vm.isShowWidget) {
						vm.isShowWidget = true;
						if (!vm.isInitialized) {
							vm.init();
							vm.isInitialized = true;
						}
					} else {
						vm.isShowWidget = false;
					}
				};

			}],
			controllerAs: 'widgetVm',
			templateUrl: function () {
				return View.get('widgets/reminder.html');
			},
			link: function (scope, elem, attrs, ctrl) {

			}
		};
	}]);


	opakeApp.controller('ReminderList', [
		'$scope',
		'$rootScope',
		'$controller',
		'$http',
		'ReminderWidgetService',
		function($scope, $rootScope, $controller, $http, ReminderWidgetService) {

			var displayInOnePage = 8;
			var vm = this;
			vm.isShowLoading = true;
			vm.isInitLoading = true;
			vm.currentPage = 1;
			vm.totalPages = 1;
			vm.items = [];
			vm.toComplete = [];

			vm.org_id = $rootScope.org_id;

			$controller('ListCrtl', {vm: vm, options: {
				defaultParams: {
					l: displayInOnePage
				}
			}});


			vm.init = function() {
				vm.items = [];
				$http.get('/reminder/ajax/list/').then(function(response) {
					angular.forEach(response.data.items, function (item) {
						item.reminder_date = moment(item.reminder_date).toDate();
						vm.items.push(item);
					});
					vm.count = response.data.total_count;
				});
			};

			vm.markAsCompleted = function (item) {
				var idx = vm.toComplete.indexOf(item.id);
				if (idx > -1) {
					vm.toComplete.splice(idx, 1);
				} else {
					vm.toComplete.push(item.id);
				}
			};

			vm.commitCompletedReminders = function () {
				if (vm.toComplete && vm.toComplete.length) {
					$http.post('/reminder/ajax/complete/', $.param({reminders: vm.toComplete})).then(function (res) {
						ReminderWidgetService.init();
						vm.init();
					});

				}
			};



		}
	])


})(opakeApp, angular);

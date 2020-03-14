(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('scheduleLegend', ['$http', '$filter', 'View',  function ($http, $filter, View) {
			return {
				restrict: "E",
				replace: true,
				controller: function ($scope) {
					var vm = this;
					vm.open = true;

					var hiddenSurgeonIds = [];

					$scope.$on('CaseCalendarLoaded', function (event, view, surgeonsSrc) {
						$http.get(surgeonsSrc.url, {params: surgeonsSrc.data}).then(function (response) {
							var scheduledSurgeons = [];
							angular.forEach(response.data, function (surgeon) {
								var scheduledSurgeon = {
									id: surgeon.id,
									name: surgeon.full_name,
									color: surgeon.case_color,
									hide: $filter('filter')(hiddenSurgeonIds, surgeon.id, true).length ? true : false
								};
								if (!$filter('filter')(scheduledSurgeons, scheduledSurgeon).length) {
									scheduledSurgeons.push(scheduledSurgeon);
								}
							});

							vm.scheduledSurgeons = scheduledSurgeons;
						});
					});

					vm.hideSurgeonEvents = function (surgeon) {
						surgeon.hide = !surgeon.hide;

						angular.forEach(vm.scheduledSurgeons, function (surgeon) {
							if (surgeon.hide) {
								if (!$filter('filter')(hiddenSurgeonIds, surgeon.id, true).length) {
									hiddenSurgeonIds.push(surgeon.id);
								}
							} else {
								if ($filter('filter')(hiddenSurgeonIds, surgeon.id, true).length) {
									var index = hiddenSurgeonIds.indexOf(surgeon.id);
									hiddenSurgeonIds.splice(index, 1);
								}
							}
						});

						$scope.$emit('surgeonsCasesHidden', hiddenSurgeonIds);
					};

					vm.getColorClassName = function (surgeon) {
						if (!surgeon.hide) {
							return 'color-' + surgeon.color;
						}
					};

				},
				controllerAs: 'legendVm',
				templateUrl: function () {
					return View.get('widgets/schedule_legend.html');
				}
			};
		}]);

})(opakeCore, angular);

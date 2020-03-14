// List of cases
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseSettingCtrl', [
		'$scope',
		'$http',
		'$filter',
		'CaseSettingConst',
		'CalendarConst',
		'Source',
		'Cases',
		'Calendar',

		function ($scope, $http, $filter, CaseSettingConst, CalendarConst, Source, Cases, Calendar) {

			$scope.caseSettingConst = CaseSettingConst;
			$scope.calendarConst = CalendarConst;

			var vm = this;

			vm.colorType = $scope.listVm.search_params.color_type || 'doctor';

			vm.init = function() {
				$http.get('/cases/ajax/' + $scope.org_id + '/setting/').then(function (result) {
					vm.setting = result.data;
					Source.getSurgeons(true).then(function (data) {
						vm.setting.doctors = $filter('orderBy')(data, 'last_name');
					});
				});
			};

			vm.save = function() {
				$http.post('/cases/ajax/save/' + $scope.org_id + '/setting/', $.param({
					data: JSON.stringify(vm.setting)
				})).then(function (result) {
					if (result.data.id) {
						Calendar.reset();
						Calendar.refetchEvents();
					} else if (result.data.errors) {
						vm.errors = result.data.errors.split(';');
					}
				});
			};

			vm.changeColorType = function() {
				$scope.listVm.search_params.color_type = vm.colorType;
				$scope.listVm.search();
			};

			vm.getClassNameForColor = function(color) {
				return 'color-' + color.key;
			};

			vm.getSurgeonColorClass = function(surgeon) {
				return 'color-' + surgeon.case_color;
			};

			vm.getSurgeonColorName = function(surgeon) {
				var color = $filter('filter')(CalendarConst.COLORS, {key: surgeon.case_color}, true);
				if (color.length) {
					return color[0].name;
				} else {
					return 'Color is not selected';
				}
			};

			vm.updateSurgeonColor = function(surgeon, color) {
				surgeon.case_color = color.key;
			};

			vm.getRoomColorClass = function(room) {
				return 'color-' + room.case_color;
			};

			vm.getRoomColorName = function(room) {
				var color = $filter('filter')(CalendarConst.COLORS, {key: room.case_color}, true);
				if (color.length) {
					return color[0].name;
				} else {
					return 'Color is not selected';
				}
			};

			vm.updateRoomColor = function(room, color) {
				room.case_color = color.key;
			};

			vm.getPracticeColorClass = function(practice) {
				return 'color-' + practice.case_color;
			};

			vm.getPracticeColorName = function(practice) {
				var color = $filter('filter')(CalendarConst.COLORS, {key: practice.case_color}, true);
				if (color.length) {
					return color[0].name;
				} else {
					return 'Color is not selected';
				}
			};

			vm.updatePracticeColor = function(practice, color) {
				practice.case_color = color.key;
			};

		}]);

})(opakeApp, angular);

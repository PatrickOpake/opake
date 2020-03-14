// Case Management
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseManagementCrtl', [
		'$scope',
		'$rootScope',
		'$filter',
		'$location',
		'CMConst',
		'CaseManagement',
		'Permissions',

		function ($scope, $rootScope, $filter, $location, CMConst, CaseManagement, Permissions) {
			$scope.CMConst = CMConst;
			$scope.mgmt = CaseManagement;

			$rootScope.topMenu = getTopMenuItems();

			var vm = this;

			vm.hasInventoryAccess = Permissions.hasAccess('case_management_clinical', 'view_inventory');
			vm.tabs = [];
			vm.caseManagementParams = {};

			var params = $location.search();
			if (angular.isDefined(params.phase) && params.phase) {
				$rootScope.topMenuActive = params.phase;
			}

			$scope.$on('caseLoaded', function (e, data, callback) {
				if (vm.caseManagementParams.activeTab && vm.caseManagementParams.activeTab in $rootScope.topMenu) {
					$rootScope.topMenuActive = vm.caseManagementParams.activeTab;
				} else if(!$rootScope.topMenuActive){
					$rootScope.topMenuActive =  getFirstTopMenu();
				}

				CaseManagement.setCase(data);
				vm.case = data;

				if (!vm.tabs.length) {
					var state = CaseManagement.getState(),
						idx = 0;
					angular.forEach(CMConst.PHASES['clinical'], function (name, key) {
						if (angular.isDefined(state[key])) {
							var completed = state[key];

							if (hasAccessToTab(key)) {
								vm.tabs.push({
									key: key,
									title: name
								});

								if (angular.isUndefined(vm.activeTab) && !completed) {
									vm.activeTab = idx;
								}
								idx++;
							}
						}
					});
					if (angular.isUndefined(vm.activeTab) && idx) {
						vm.activeTab = idx - 1;
					}
				}

				if(callback) {
					callback();
				}


			});

			vm.init = function (params) {
				vm.caseManagementParams = params;
			};

			vm.isActiveTimeline = function () {
				return $rootScope.topMenuActive === 'clinical' && $filter('filter')(vm.tabs, {key: 'operation', active: true}).length;
			};

			vm.isActiveCasePanel = function () {
				return ($rootScope.topMenuActive === 'clinical') && !vm.isOpReportStage();
			};

			vm.isOpReportStage = function () {
				return $rootScope.topMenuActive === 'clinical' && $filter('filter')(vm.tabs, {key: 'report', active: true}).length;
			};

			vm.getPathToCase = function() {
				if(vm.isOpReportStage()) {
					return 'cases/report/case_info/';
				} else {
					return 'cases/';
				}
			};

			vm.next = function () {
				CaseManagement.complete(vm.tabs[vm.activeTab].key);
				vm.activeTab++;
			};

			vm.back = function (i) {
				if (vm.activeTab - 1 >= 0) {
					vm.activeTab--;
				}
			};

			function isEnabledOperativeReport() {
				var is_enabled_op_report = false;
				angular.forEach(vm.case.users, function (user) {
					if(user.is_enabled_op_report || Permissions.user.is_internal) {
						is_enabled_op_report =  true;
					}
				});
				return is_enabled_op_report;
			}

			function hasAccessToTab(name) {

				if (name === 'report') {
					if (Permissions.getAccessLevel('operative_reports', 'view').isDisallowed() || !isEnabledOperativeReport()) {
						return false;
					}
				}

				var clinicalPermissionMapping = {
					hp: 'view_hp',
					pre_op: 'view_pre_op',
					operation: 'view_op',
					post_op: 'view_post_op',
					report: 'view_op_report',
					discharge: 'view_discharge'
				};

				if (clinicalPermissionMapping[name]) {
					if (Permissions.getAccessLevel('case_management_clinical', clinicalPermissionMapping[name]).isDisallowed()) {
						return false;
					}
				}

				return true;
			}

			function getTopMenuItems() {
				var stages = angular.copy(CMConst.STAGES);
				if (!Permissions.hasAccess('billing', 'view')) {
					delete stages['billing'];
				}
				if (!Permissions.hasAccess('case_management_intake', 'view')) {
					delete stages['intake'];
				}
				if (!Permissions.hasAccess('case_management_clinical', 'view')) {
					delete stages['clinical'];
				}
				if (!Permissions.hasAccess('case_management_audit', 'view')) {
					delete stages['audit'];
				}

				return stages;
			}

			function getFirstTopMenu() {
				for (var name in $rootScope.topMenu) {
					return name;
				}

				return 'intake';
			}

			$scope.$watch('topMenuActive', function (newVal) {
				var name;

				if (newVal === 'item_log') {
					$rootScope.subTopMenu = CMConst.PHASES.item_log;
					for (name in $rootScope.subTopMenu) {
						$rootScope.subTopMenuActive = name;
						break;
					}
				} else if (angular.equals($rootScope.subTopMenu, CMConst.PHASES.item_log) ||
							angular.equals($rootScope.subTopMenu, CMConst.PHASES.billing)) {
					$rootScope.subTopMenu = {};
				}
			});

		}]);

})(opakeApp, angular);

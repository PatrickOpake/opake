// Operative Report list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('OperativeReportListCrtl', [
		'$rootScope',
		'$scope',
		'$http',
		'$controller',
		'$location',
		'$window',
		'$sce',
		'View',
		'Tools',
		'OperativeReportConst',
		'OperativeReportTemplateConst',
		'OpReports',
		'OperativeReport',
		'Case',
		function ($rootScope, $scope, $http, $controller, $location, $window, $sce, View, Tools, OperativeReportConst, OperativeReportTemplateConst, OpReports, OperativeReport, Case) {
			$scope.operativeReportConst = OperativeReportConst;

			var vm = this;
			vm.type = 'open';
			vm.isShowLoading = false;
			vm.isDocumentsLoading = false;
			vm.templateConst = OperativeReportTemplateConst;
			vm.showOverviewReportTable = false;
			$controller('ListCrtl', {vm: vm});

			$scope.$on("$locationChangeSuccess", function () {
				vm.items = null;
				var params = $location.search();
				vm.type = angular.isDefined(params.type) ? params.type : 'open';
				vm.reset();
			});

			vm.init = function(user_id) {
				if(user_id) {
					vm.user_id = user_id;
				}
			};

			vm.setType = function(type) {
				if (vm.type != type) {
					$location.search('type', type);
				}
			};

			vm.search = function () {
				vm.toSelected = [];
				vm.isShowLoading = true;
				vm.selectAll = false;
				if(vm.user_id) {
					vm.search_params.user_id = vm.user_id;
				}
				if(vm.type) {
					vm.search_params.type = vm.type;
				}
				var data = angular.copy(vm.search_params);
				if(data.dos) {
					data.dos = moment(data.dos).format('YYYY-MM-DD');
				}
				$http.get('/operative-reports/ajax/' + $scope.org_id + '/myOperativeReports', {params: angular.extend(data, {
					alerts: true
				}) }).then(function (response) {
					var data = response.data;
					vm.items = [];

					angular.forEach(data.items, function(item) {
						var report = new OperativeReport(item);
						vm.items.push(report);
					});

					vm.total_count = data.total_count;
					vm.alerts_count = data.alerts;
					vm.isShowLoading = false;
				});
			};

			vm.getPrintUrl = function (id) {
				return '/cases/ajax/' + $scope.org_id + '/exportReport/' + id + '?to_download=false';

			};

			vm.view = function(report) {
				if($scope.permissions.user.is_internal
					|| $scope.permissions.hasAccess('operative_reports', 'edit', report)
					|| $scope.permissions.hasAccess('operative_reports', 'view', report)) {
					var params = '#?type=' + vm.type;
					if(vm.user_id) {
						params = '/' + vm.user_id + params;
					}
					if(report.status == OperativeReportConst.STATUSES.signed) {
						params += '&signed=true';
					}
					$window.location = '/operative-reports/my/' + $scope.org_id + '/view/' + report.id + params;
				}
			};

			vm.previewReport = function(id) {
				$http.get('/operative-reports/ajax/' + $scope.org_id + '/preview/' + id).then(function (result) {
					vm.report = new OperativeReport(result.data.report);
					vm.report.case = new Case(vm.report.case);
					vm.organization = result.data.organization;
					vm.previewTemplate = result.data.template;
					$scope.dialog(View.get('operative-report/preview.html'), $scope, {size: 'lg'});
				});
			};

			vm.printAll = function() {
				if (vm.toSelected && vm.toSelected.length) {
					var documents = [];
					angular.forEach(vm.toSelected, function (item) {
						documents.push(item.id);
					});
					vm.isDocumentsLoading = true;
					$http.post('/cases/operative-reports/ajax/' + $scope.org_id + '/compileOperativeReports/', $.param({reports: documents})).then(function (res) {
						vm.isDocumentsLoading = false;
						if (res.data.success) {
							Tools.print(location.protocol + '//' + location.host + res.data.url);
						}
					}, function() {
						vm.isDocumentsLoading = false;
					});
				}
			};

			vm.archiveAll = function() {
				if (vm.toSelected && vm.toSelected.length) {
					$scope.dialog(View.get('operative-report/confirm_archive.html'), $scope, {windowClass: 'alert'}).result.then(function () {
						var documents = [];
						angular.forEach(vm.toSelected, function (item) {
							documents.push(item.id);
						});
						OpReports.archive(documents, function(result) {
							if(result.data.success) {
								vm.reset();
							}
						});
					});
				}
			};

			vm.sign = function (report) {
				$scope.dialog(View.get('cases/report/sign-alert.html'), $scope, {windowClass: 'alert'}).result.then(function () {
					$http.post('/operative-reports/ajax/' + $scope.org_id + '/sign/' + report.id).then(function (res) {
						if (res.data.success) {
							vm.reset();
						}
					});
				});
			};

			vm.reopen = function (report) {
				$scope.dialog(View.get('cases/report/reopen_submitted.html'), $scope, {windowClass: 'alert'}).result.then(function () {
					OpReports.changeStatus(report.id, OperativeReportConst.STATUSES.draft, function (result) {
						if (result.data.success) {
							vm.setType('open');
							vm.reset();
						}
					});
				});
			};

			vm.canUserSign = function (report) {
				return !$scope.permissions.user.is_internal && $scope.permissions.hasAccess('operative_reports', 'sign', report);
			};

			vm.trustAsHtml = function(string) {
				return $sce.trustAsHtml(string);
			};

			vm.redirectToOverview = function() {
				$window.location = '/overview/dashboard/' + $scope.org_id;
			};

			vm.showGenerateReportTable = function () {
				vm.showOverviewReportTable = !vm.showOverviewReportTable;
			};

		}]);

})(opakeApp, angular);

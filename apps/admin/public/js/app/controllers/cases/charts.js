(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseChartsCtrl', [
		'$scope',
		'$http',
		'$filter',
		'View',
		'Tools',
		'CaseChart',

		function ($scope, $http, $filter, View, Tools, CaseChart) {

			var vm = this;

			vm.isDocumentsLoading = false;

			vm.init = function(caseId) {
				vm.caseId = caseId;
				$http.get('/cases/ajax/' + $scope.org_id + '/chartsList/' + caseId).then(function (result) {
					var charts = [];
					angular.forEach(result.data.charts, function (chart) {
						var chartObject = new CaseChart(chart);
						charts.push(chartObject);
					});
					vm.charts = charts;
				});

				vm.modal = $scope.dialog(View.get('booking/charts.html'), $scope, {windowClass: 'alert forms upload'});
				vm.modal.result.then(function () {
				}, function () {
				});
			};

			vm.uploadFiles = function (files) {
				vm.errors = [];
				angular.forEach(files, function (file) {
					if (file.type == 'application/pdf') {
						var chart = {file: file, name: file.name};
						vm.charts.push(chart);
					} else {
						vm.errors = ['Only PDF files are supported'];
					}

				});
			};

			vm.uploadFileChanged = function(files) {
				vm.errors = [];
				angular.forEach(files, function (file) {
					if (file.type == 'application/pdf') {
						var chart = {file: file, name: file.name};
						vm.charts.push(chart);
					} else {
						vm.errors = ['Only PDF files are supported'];
					}

				});
				$scope.$apply();
			};


			vm.saveUploadedCharts = function() {
				vm.errors = [];
				angular.forEach(vm.charts, function (chart) {
					if (chart.name) {
						var fd = new FormData();
						angular.forEach(chart, function (value, key) {
							fd.append(key, value);
						});
						fd.append('doc_name', chart.name);
						var caseId = vm.caseId;
						return $http.post('/cases/ajax/' + $scope.org_id + '/uploadDoc/' + caseId, fd, {
							withCredentials: true,
							headers: {'Content-Type': undefined},
							transformRequest: angular.identity
						}).then(function () {
							vm.modal.close();
						});
					}
				});
			};

			vm.renameMode = function(chart) {
				chart.new_name = chart.name;
				chart.rename_mode = true;
			};

			vm.renameChart = function(chart) {
				chart.name = chart.new_name;
				chart.rename_mode = false;
			};

			vm.cancelRenameChart = function(chart) {
				chart.rename_mode = false;
			};

			vm.removeChart = function (chart) {
				if ($filter('filter')(vm.charts, chart, true).length) {
					var index = vm.charts.indexOf(chart);
					vm.charts.splice(index, 1);
				}

				if (chart.id) {
					$http.post('/cases/ajax/' + $scope.org_id + '/removeDoc/' + chart.id + '?case=' + vm.caseId);
				}
			};


			vm.preview = function(doc) {
				$scope.doc = doc;
				$scope.dialog(View.get('/preview-doc.html'), $scope, {size: 'lg', windowClass: 'preview-doc'});
			};

		}]);

})(opakeApp, angular);
